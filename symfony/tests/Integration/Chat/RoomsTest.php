<?php

declare(strict_types=1);

namespace App\Tests\Integration\Chat;

use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Tests\Factory\MessageFactory;
use App\Tests\Factory\RoomFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RoomsTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testListRooms(): void
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
            '/api/rooms',
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($responseBody['count'], 3);
        $this->assertCount(3, $responseBody['results']);
    }

    public function testListRoomsNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/rooms',
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetRoom(): void
    {
        $client = static::createClient();
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
            '/api/rooms/'.$room->getId(),
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $responseBody);
        $this->assertEquals($responseBody['name'], 'testRoom');
        $this->assertEquals($responseBody['member_count'], 1);
    }

    public function testSearch(): void
    {
        $client = static::createClient();
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
            '/api/search',
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($responseBody['count'], 5);
        $this->assertCount(5, $responseBody['results']);
        $this->assertEquals($responseBody['results'][0]['name'], 'testRoom');
        $this->assertEquals($responseBody['results'][0]['is_member'], 1);
        $this->assertEquals($responseBody['results'][1]['is_member'], 0);
    }
}
