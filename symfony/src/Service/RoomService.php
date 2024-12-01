<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\CentrifugoSender\BroadcastRequestDto;
use App\Entity\Message;
use App\Entity\Room;
use App\Entity\User;
use App\Enum\ChannelTemplates;
use App\Response\RoomChangedPayload;
use App\Service\Centrifugo\CentrifugoSenderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class RoomService
{
    public const MESSAGE_ADDED = 'message_added';
    public const USER_JOINED = 'user_joined';
    public const USER_LEFT = 'user_left';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CentrifugoSenderService $centrifugoSenderService,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function addRoomMessage(Room $room, User $author, string $content): Message
    {
        $this->em->beginTransaction();
        try {
            $message = new Message();
            $room->addMessage($message);
            $room->setLastMessage($message)->setBumpedAt(new \DateTimeImmutable());
            $message->setUser($author);
            $message->setContent($content);
            $this->em->persist($message);
            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }

        $this->refreshEntities($message);
        $messageData = [
            'type' => self::MESSAGE_ADDED,
            'body' => $content,
        ];
        $this->centrifugoSenderService->broadcast(new BroadcastRequestDto(
            channels: $this->getRoomMembersChannels($room),
            data: $messageData,
            idempotencyKey: sprintf('%s_%s', self::MESSAGE_ADDED, $message->getId()),
        ));

        return $message;
    }

    public function joinRoom(Room $room, User $user): RoomChangedPayload
    {
        $this->em->beginTransaction();
        try {
            $roomMembers = $room->getMembers();
            $roomMembers->add($user);
            $this->em->persist($room);
            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
        $this->refreshEntities($room, $user);
        $payload = new RoomChangedPayload($room, $user);
        $messageData = [
            'type' => self::USER_JOINED,
            'body' => $this->serializer->serialize($payload, 'json', ['groups' => [Room::API_LIST_GROUP]]),
        ];

        $this->centrifugoSenderService->broadcast(new BroadcastRequestDto(
            channels: $this->getRoomMembersChannels($room),
            data: $messageData,
            idempotencyKey: sprintf('%s_%s', self::USER_JOINED, $user->getId()),
        ));

        return $payload;
    }

    public function leaveRoom(Room $room, User $user): RoomChangedPayload
    {
        $this->em->beginTransaction();
        try {
            $roomMembers = $room->getMembers();
            $roomMembers->removeElement($user);
            $this->em->persist($room);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        $this->refreshEntities($room, $user);
        $payload = new RoomChangedPayload($room, $user);
        $messageData = [
            'type' => self::USER_LEFT,
            'body' => $this->serializer->serialize($payload, 'json', ['groups' => [Room::API_LIST_GROUP]]),
        ];

        $this->centrifugoSenderService->broadcast(new BroadcastRequestDto(
            channels: $this->getRoomMembersChannels($room),
            data: $messageData,
            idempotencyKey: sprintf('%s_%s', self::USER_LEFT, $user->getId()),
        ));

        return $payload;
    }

    /**
     * @param object $entities
     */
    private function refreshEntities(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->em->refresh($entity);
        }
    }

    /**
     * @return list<string>
     */
    private function getRoomMembersChannels(Room $room): array
    {
        $roomMembers = $room->getMembers();

        return $roomMembers->map(
            fn (User $user) => sprintf(ChannelTemplates::PERSONAL->value, $user->getUserIdentifier())
        )->toArray();
    }
}
