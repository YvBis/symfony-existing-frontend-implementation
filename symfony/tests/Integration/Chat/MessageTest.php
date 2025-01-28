<?php

declare(strict_types=1);

namespace App\Tests\Integration\Chat;

use App\Entity\Message;
use App\Repository\MessageRepository;
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

class MessageTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;
    use CsrfTokenStubbedTrait;

    public function testGetRoomMessagesFromMember(): void
    {
        $client = $this->makeClient();
        $room = RoomFactory::createOne(
            [
                'name' => 'testRoom',
                'members' => [UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser'])],
            ]
        );
        MessageFactory::createMany(3, static fn ($i) => ['relation' => $room, 'content' => 'Test Message '.$i]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $room = $roomRepository->findOneBy(['name' => 'testRoom']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/rooms/'.$room->getId().'/messages/',
        );
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertEquals(3, $responseBody['count']);
        self::assertCount(3, $responseBody['results']);
        self::assertEquals('Test Message 1', $responseBody['results'][0]['content']);
        self::assertEquals('Test Message 2', $responseBody['results'][1]['content']);
        self::assertEquals('Test Message 3', $responseBody['results'][2]['content']);
    }

    public function testGetRoomMessagesNonMember(): void
    {
        $client = $this->makeClient();
        $room = RoomFactory::createOne(
            [
                'name' => 'testRoom',
            ]
        );
        UserFactory::createOne(
            [
                'email' => 'test-email@email.com',
                'username' => 'testUser',
            ]
        );
        MessageFactory::createMany(3, static fn ($i) => ['relation' => $room, 'content' => 'Test Message '.$i]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $room = $roomRepository->findOneBy(['name' => 'testRoom']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_GET,
            '/rooms/'.$room->getId().'/messages/',
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAddMessage(): void
    {
        $client = $this->makeClient();
        RoomFactory::createOne(
            [
                'name' => 'testRoom',
                'members' => [UserFactory::createOne(['email' => 'test-email@email.com', 'username' => 'testUser'])],
            ]
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $messageRepository = static::getContainer()->get(MessageRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $room = $roomRepository->findOneBy(['name' => 'testRoom']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/rooms/'.$room->getId().'/messages/',
            [
                'content' => 'Test Message',
            ]
        );
        $persistedMessage = $messageRepository->findOneBy(['content' => 'Test Message']);
        $response = $client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertInstanceOf(Message::class, $persistedMessage);
        self::assertSame($responseBody['id'], $persistedMessage->getId());
        self::assertEquals('Test Message', $responseBody['content']);
        self::assertEquals('testUser', $responseBody['user']['username']);
        self::assertEquals('testRoom', $responseBody['room']['name']);
        self::assertNotNull($persistedMessage);
        self::assertEquals('Test Message', $persistedMessage->getContent());
    }

    public function testAddMessageFromNonRoomMember(): void
    {
        $client = $this->makeClient();
        $room = RoomFactory::createOne(
            [
                'name' => 'testRoom',
            ]
        );
        UserFactory::createOne(
            [
                'email' => 'test-email@email.com',
                'username' => 'testUser',
            ]
        );
        MessageFactory::createMany(3, static fn ($i) => ['relation' => $room, 'content' => 'Test Message '.$i]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $messageRepository = static::getContainer()->get(MessageRepository::class);
        $user = $userRepository->findOneBy(['username' => 'testUser']);
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $room = $roomRepository->findOneBy(['name' => 'testRoom']);
        $client->loginUser($user);

        $client->jsonRequest(
            Request::METHOD_POST,
            '/rooms/'.$room->getId().'/messages/',
            [
                'content' => 'Test Message',
            ]
        );
        $persistedMessage = $messageRepository->findOneBy(['content' => 'Test Message']);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertNull($persistedMessage);
    }
}
