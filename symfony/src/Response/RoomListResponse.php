<?php

namespace App\Response;

use App\Entity\Room;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class RoomListResponse
{
    /**
     * @param array<Room> $results
     */
    public function __construct(
        #[Groups(Room::API_LIST_GROUP)]
        public int $count,
        #[Groups(Room::API_LIST_GROUP)]
        public ?int $next = null,
        #[Groups(Room::API_LIST_GROUP)]
        public ?int $previous = null,
        #[Groups([Room::API_LIST_GROUP])]
        public array $results = [],
    ) {
    }
}
