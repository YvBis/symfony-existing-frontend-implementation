<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Attribute\CheckRequestAttributeTrue;
use App\Dto\NewMessageDto;
use App\Entity\Message;
use App\Entity\Room;
use App\Entity\User;
use App\Enum\CsrfTokenConstant;
use App\Response\RoomChangedPayload;
use App\Response\RoomListResponse;
use App\Response\RoomMessagesResponse;
use App\Response\RoomSearchResponse;
use App\Service\RoomService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RoomsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly RoomService $roomService)
    {
    }

    #[Route('/rooms/', name: 'app_chat_rooms', methods: ['GET'])]
    public function view(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $rooms = $this->em->getRepository(Room::class)->getRoomsByUser($user);
        $response = new RoomListResponse(count: count($rooms), results: $rooms);

        return $this->json(
            data: $response,
            context: ['groups' => [Room::API_LIST_GROUP]],
        );
    }

    #[Route('/rooms/{id}/', name: 'app_chat_rooms_details', methods: ['GET'])]
    public function details(Room $room): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $room = $this->em->getRepository(Room::class)->getRoomForUserById($room, $user);

        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        return $this->json($room, context: ['groups' => [Room::API_LIST_GROUP]]);
    }

    #[Route('/search/', name: 'app_chat_rooms_search', methods: ['GET'])]
    public function search(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $rooms = $this->em->getRepository(Room::class)->getRoomsByUserForSearch($user);
        $response = new RoomSearchResponse(
            count: count($rooms),
            results: $rooms,
        );

        return $this->json(data: $response);
    }

    #[Route('/rooms/{id}/messages/', name: 'app_chat_rooms_messages', methods: ['GET'])]
    #[IsGranted('room_membership', 'room')]
    public function getRoomMessages(Room $room): JsonResponse
    {
        $messages = $this->em->getRepository(Message::class)->getMessagesByRoom($room);
        $response = new RoomMessagesResponse(
            count: count($messages),
            results: $messages,
        );

        return $this->json($response, context: ['groups' => [Message::API_LIST_GROUP]]);
    }

    #[Route('/rooms/{id}/messages/', name: 'app_chat_rooms_add_message', methods: ['POST'])]
    #[IsGranted('room_membership', 'room')]
    public function addRoomMessage(
        Room $room,
        #[MapRequestPayload] NewMessageDto $newMessageDto
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $message = $this->roomService->addRoomMessage($room, $user, $newMessageDto->content);

        return $this->json($message, context: ['groups' => [Message::API_LIST_GROUP]]);
    }

    #[Route('/rooms/{id}/join/', name: 'app_chat_rooms_join_room', methods: ['POST'])]
    public function joinRoom(Room $room): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRooms()->contains($room)) {
            return $this->json(
                data: ['message' => 'Already a member'],
                status: Response::HTTP_CONFLICT,
            );
        }
        $responsePayload = $this->roomService->joinRoom($room, $user);

        return $this->json(data: $responsePayload, context: ['groups' => [Room::API_LIST_GROUP]]);
    }

    #[Route('/rooms/{id}/leave/', name: 'app_chat_rooms_leave_room', methods: ['POST'])]
    public function leaveRoom(Room $room): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getRooms()->contains($room)) {
            return $this->json(
                data: ['detail' => 'Not found'],
                status: Response::HTTP_NOT_FOUND,
            );
        }
        $this->roomService->leaveRoom($room, $user);
        $response = new RoomChangedPayload(
            room: $room,
            user: $user,
        );

        return $this->json(data: $response, context: ['groups' => [Room::API_LIST_GROUP]]);
    }
}
