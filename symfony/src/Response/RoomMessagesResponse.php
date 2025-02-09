<?php

namespace App\Response;

use App\Entity\Message;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class RoomMessagesResponse
{
    /**
     * @param array<Message> $results
     */
    public function __construct(
        #[Groups(Message::API_LIST_GROUP)]
        public int $count,
        #[Groups(Message::API_LIST_GROUP)]
        public ?int $next = null,
        #[Groups(Message::API_LIST_GROUP)]
        public ?int $previous = null,
        #[Groups(Message::API_LIST_GROUP)]
        public array $results = [],
    ) {
    }
}
