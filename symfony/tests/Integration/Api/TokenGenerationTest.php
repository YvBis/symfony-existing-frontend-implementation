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

    public function testSubscriptionTokenGeneration()
    {
        $client = $this->makeClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/token/subscription',
            [
                'channelName' => 'personal:testUser',
            ],
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $responseBody);
    }

    public function testConnectionTokenGeneration()
    {
        $client = $this->makeClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/token/subscription',
            [
                'channelName' => 'personal:testUser',
            ],
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $responseBody);
    }

    public function testConnectionTokenGenerationWithoutLogin()
    {
        $client = $this->makeClient();
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
        $client = $this->makeClient();
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
