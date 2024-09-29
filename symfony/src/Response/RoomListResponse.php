<?php

namespace App\Response;

use App\Entity\Room;
use App\Interface\TimestampableEntityInterface;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class RoomListResponse
{
    /**
     * @param int $count
     * @param int|null $next
     * @param int|null $previous
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
        public array $results = []
    ) {
    }
}
