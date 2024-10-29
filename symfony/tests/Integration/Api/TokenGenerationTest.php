<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use App\Repository\UserRepository;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TokenGenerationTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testSubscriptionTokenGeneration()
    {
        $client = static::createClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/token/subscription',
            [
                'channelName' => 'testChannel',
            ],
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $responseBody);
    }

    public function testConnectionTokenGeneration()
    {
        $client = static::createClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/token/subscription',
            [
                'channelName' => 'testChannel',
            ],
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $responseBody);
    }

    public function testConnectionTokenGenerationWithoutLogin()
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/token/connection',
            [
                'channelName' => 'testChannel',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
    public function testSubscriptionTokenGenerationWithoutLogin()
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/token/subscription',
            [
                'channelName' => 'testChannel',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
