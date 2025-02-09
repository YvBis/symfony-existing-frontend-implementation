<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use App\Repository\UserRepository;
use App\Tests\Factory\UserFactory;
use App\Tests\Traits\CsrfTokenStubbedTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TokenGenerationTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;
    use CsrfTokenStubbedTrait;

    public function testSubscriptionTokenGeneration(): void
    {
        $client = $this->makeClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_ACCEPT', 'application/json');
        $client->request(Request::METHOD_GET, '/token/subscription/', [
            'channel' => 'personal:'.$user->getId(),
        ]);
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $responseBody);
    }

    public function testConnectionTokenGeneration(): void
    {
        $client = $this->makeClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/token/connection/');
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $responseBody);
    }

    public function testConnectionTokenGenerationWithoutLogin()
    {
        $client = $this->makeClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/token/connection/',
            [
                'channelName' => 'testChannel',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testSubscriptionTokenGenerationWithoutLogin()
    {
        $client = $this->makeClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/token/subscription/',
            [
                'channelName' => 'testChannel',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
