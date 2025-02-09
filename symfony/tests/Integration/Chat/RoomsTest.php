<?php

declare(strict_types=1);

namespace App\Tests\Integration\Chat;

use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Tests\Factory\MessageFactory;
use App\Tests\Factory\RoomFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Traits\CsrfTokenStubbedTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RoomsTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;
    use CsrfTokenStubbedTrait;

    public function testListRooms(): void
    {
        $client = $this->makeClient();
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
            '/rooms/',
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertEquals(3, $responseBody['count']);
        self::assertCount(3, $responseBody['results']);
    }

    public function testListRoomsNotLoggedIn(): void
    {
        $client = $this->makeClient();

        $client->jsonRequest(
            Request::METHOD_GET,
            '/rooms/',
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGetRoom(): void
    {
        $client = $this->makeClient();
        $room = RoomFactory::createOne(
            [
                'name' => 'testRoom',
                'members' => [UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser'])],
            ]
        );
        MessageFactory::createMany(3, ['relation' => $room]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $room = $roomRepository->findOneBy(['name' => 'testRoom']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/rooms/'.$room->getId().'/',
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertCount(7, $responseBody);
        self::assertEquals('testRoom', $responseBody['name']);
        self::assertEquals(1, $responseBody['member_count']);
    }

    public function testSearch(): void
    {
        $client = $this->makeClient();
        $room = RoomFactory::createOne(
            [
                'name' => 'testRoom',
                'members' => [UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser'])],
            ]
        );
        MessageFactory::createMany(3, ['relation' => $room]);
        RoomFactory::createMany(4);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/search/',
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertEquals(5, $responseBody['count']);
        self::assertCount(5, $responseBody['results']);
        self::assertEquals('testRoom', $responseBody['results'][0]['name']);
        self::assertEquals(1, $responseBody['results'][0]['is_member']);
        self::assertEquals(0, $responseBody['results'][1]['is_member']);
    }
}
