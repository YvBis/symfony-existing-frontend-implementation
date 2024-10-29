<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use App\Repository\UserRepository;
use App\Tests\Factory\RoomFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testRegister(): void
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_POST,
            '/api/registerUser',
            [
                'username' => 'testUser',
                'plainPassword' => UserFactory::DEFAULT_PASSWORD,
                'email' => 'test-email@email.com',
            ],
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($response['message'], 'User created successfully');
        $this->assertEquals($user->getEmail(), 'test-email@email.com');
    }

    public function testRegisterDuplicatedEmail(): void
    {
        $client = static::createClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/api/registerUser',
            [
                'username' => 'testUser1',
                'plainPassword' => 'test123',
                'email' => 'test-email@email.com',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegisterDuplicatedUsername(): void
    {
        $client = static::createClient();
        UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/api/registerUser',
            [
                'username' => 'testUser',
                'plainPassword' => 'test123',
                'email' => 'test-email1@email.com',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser']);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/api/login',
            [
                'login' => 'testUser',
                'password' => UserFactory::DEFAULT_PASSWORD,
            ],
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($responseBody['user']['id'], $user->getId());
        $this->assertEquals($responseBody['user']['username'], $user->getUsername());
    }

    public function testInfo(): void
    {
        $client = static::createClient();
        UserFactory::createOne([
            'email' => 'test-email@email.com',
            'username' => 'testUser',
            'rooms' => RoomFactory::createMany(3),
        ]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/info',
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($responseBody['id'], $user->getId());
        $this->assertEquals($responseBody['username'], 'testUser');
        $this->assertEquals($responseBody['email'], 'test-email@email.com');
        $this->assertEquals($responseBody['rooms'], 3);
    }
}
