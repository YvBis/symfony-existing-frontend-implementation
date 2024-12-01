<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class CSRFTokenTest extends WebTestCase
{
    use ResetDatabase;

    public function testGetCsrfToken(): void
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/csrf',
        );
        $csrfToken = $client->getResponse()->headers->all('X-CSRFToken');

        $this->assertResponseIsSuccessful();
        $this->assertIsArray($csrfToken);
        $this->assertNotEmpty($csrfToken);
        $this->assertCount(1, $csrfToken);
        $this->assertIsString($csrfToken[0]);
    }

    public function testCheckCsrfTokenSuccessful(): void
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/csrf',
        );
        $csrfToken = $client->getResponse()->headers->all('X-CSRFToken');
        $client->jsonRequest(
            Request::METHOD_POST,
            '/api/registerUser',
            [
                'username' => 'testUser2',
                'plainPassword' => UserFactory::DEFAULT_PASSWORD,
                'email' => 'test-email2@email.com',
            ],
            [
                'HTTP_X_CSRFToken' => $csrfToken[0],
            ],
        );

        $this->assertResponseIsSuccessful();
    }

    public function testCheckCsrfTokenFailed(): void
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_POST,
            '/api/registerUser',
            [
                'username' => 'testUser2',
                'plainPassword' => UserFactory::DEFAULT_PASSWORD,
                'email' => 'test-email2@email.com',
            ],
            [
                'HTTP_X_CSRFToken' => 'random-string',
            ],
        );
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertSame($response['detail'], 'Invalid CSRF token.');
    }
}
