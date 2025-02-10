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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RoomService
{
    public const MESSAGE_ADDED = 'message_added';
    public const USER_JOINED = 'user_joined';
    public const USER_LEFT = 'user_left';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CentrifugoSenderService $centrifugoSenderService,
        private readonly NormalizerInterface $normalizer,
        private readonly string $centrifugoPersonalChannelPrefix,
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
        if ($room->getMembers()->count()) {
            $this->broadcastMessage(
                $this->getRoomMembersChannels($room),
                self::MESSAGE_ADDED,
                $this->normalizer->normalize($message, 'json', ['groups' => [Message::API_LIST_GROUP]]),
                sprintf('%s_%s', self::MESSAGE_ADDED, $message->getId()),
            );
        }

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
        if ($room->getMembers()->count()) {
            $this->broadcastMessage(
                $this->getRoomMembersChannels($room),
                self::USER_JOINED,
                $this->normalizer->normalize($payload, 'json', ['groups' => [Room::API_LIST_GROUP]]),
                sprintf('%s_%s', self::USER_JOINED, $user->getId()),
            );
        }

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
        if ($room->getMembers()->count()) {
            $this->broadcastMessage(
                $this->getRoomMembersChannels($room),
                self::USER_LEFT,
                $this->normalizer->normalize($payload, 'json', ['groups' => [Room::API_LIST_GROUP]]),
                sprintf('%s_%s', self::USER_LEFT, $user->getId()),
            );
        }

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
            fn (User $user) => sprintf('%s:%s', $this->centrifugoPersonalChannelPrefix, $user->getUserIdentifier())
        )->toArray();
    }

    /**
     * @param array<string> $channels
     */
    private function broadcastMessage(
        array $channels,
        string $messageType,
        mixed $messageBody,
        string $idempotencyKey,
    ): void {
        $messageData = [
            'type' => $messageType,
            'body' => $messageBody,
        ];
        $this->centrifugoSenderService->broadcast(new BroadcastRequestDto(
            channels: $channels,
            data: $messageData,
            idempotencyKey: $idempotencyKey,
        ));
    }
}
