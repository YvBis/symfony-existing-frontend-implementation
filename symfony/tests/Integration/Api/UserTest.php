<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use App\Attribute\CheckRequestAttributeTrue;
use App\Enum\CsrfTokenConstant;
use App\Repository\UserRepository;
use App\Tests\Factory\RoomFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Traits\CsrfTokenStubbedTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;
    use CsrfTokenStubbedTrait;

    public function testRegister(): void
    {
        $client = $this->makeClient();
        $client->jsonRequest(
            Request::METHOD_POST,
            '/register/',
            [
                'username' => 'testUser',
                'plainPassword' => UserFactory::DEFAULT_PASSWORD,
                'email' => 'test-email@email.com',
            ],
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);

        self::assertResponseIsSuccessful();
        self::assertEquals($response['message'], 'User created successfully');
        self::assertEquals($user->getEmail(), 'test-email@email.com');
    }

    public function testRegisterDuplicatedEmail(): void
    {
        $client = $this->makeClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/register/',
            [
                'username' => 'testUser1',
                'plainPassword' => 'test123',
                'email' => 'test-email@email.com',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegisterDuplicatedUsername(): void
    {
        $client = $this->makeClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/register/',
            [
                'username' => 'testUser',
                'plainPassword' => 'test123',
                'email' => 'test-email1@email.com',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLogin(): void
    {
        $client = $this->makeClient();
        $user = UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/login/',
            [
                'username' => 'testUser',
                'password' => UserFactory::DEFAULT_PASSWORD,
            ],
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertEquals($responseBody['user']['id'], $user->getId());
        self::assertEquals($responseBody['user']['username'], $user->getUsername());
    }
}
