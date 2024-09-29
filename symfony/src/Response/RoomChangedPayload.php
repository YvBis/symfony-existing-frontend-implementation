<?php

namespace App\Response;

use App\Entity\Room;
use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class RoomChangedPayload
{
    public function __construct(
        #[Groups(Room::API_LIST_GROUP)]
        public Room $room,
        #[Groups(Room::API_LIST_GROUP)]
        public User $user,
    ) {
    }
}
