<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zenstruck\Foundry\Test\ResetDatabase;

class CSRFTokenTest extends WebTestCase
{
    use ResetDatabase;

    public function testGetCsrfToken(): void
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/csrf/',
        );
        $csrfToken = $client->getResponse()->headers->all('X-CSRFToken');

        self::assertResponseIsSuccessful();
        self::assertIsArray($csrfToken);
        self::assertNotEmpty($csrfToken);
        self::assertCount(1, $csrfToken);
        self::assertIsString($csrfToken[0]);
    }

    public function testCheckCsrfTokenSuccessful(): void
    {
        $client = static::createClient();
        $client->disableReboot();
        /** @var SessionInterface $session */
        $session = $client->getContainer()->has('session')
            ? $client->getContainer()->get('session')
            : $client->getContainer()->get('session.factory')->createSession();
        $session->start();
        $client->getContainer()->set('session', $session);
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->jsonRequest(
            Request::METHOD_GET,
            '/csrf/',
        );
        $csrfToken = $client->getResponse()->headers->all('X-CSRFToken');
        $client->jsonRequest(
            Request::METHOD_POST,
            '/register/',
            [
                'username' => 'testUser2',
                'plainPassword' => UserFactory::DEFAULT_PASSWORD,
                'email' => 'test-email2@email.com',
            ],
            [
                'HTTP_X_CSRFToken' => $csrfToken[0],
            ],
        );

        self::assertResponseIsSuccessful();
    }

    public function testCheckCsrfTokenFailed(): void
    {
        $client = static::createClient();
        $client->disableReboot();
        /** @var SessionInterface $session */
        $session = $client->getContainer()->has('session')
            ? $client->getContainer()->get('session')
            : $client->getContainer()->get('session.factory')->createSession();
        $session->start();
        $client->getContainer()->set('session', $session);
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->jsonRequest(
            Request::METHOD_GET,
            '/csrf/',
        );
        $csrfToken = $client->getResponse()->headers->all('X-CSRFToken');
        $client->jsonRequest(
            Request::METHOD_POST,
            '/register/',
            [
                'username' => 'testUser2',
                'plainPassword' => UserFactory::DEFAULT_PASSWORD,
                'email' => 'test-email2@email.com',
            ],
            [
                'HTTP_X_CSRFToken' => 'some random token',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
