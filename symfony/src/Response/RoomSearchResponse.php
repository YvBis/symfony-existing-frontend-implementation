<?php

namespace App\Response;

final readonly class RoomSearchResponse
{
    /**
     * @param array<array{id: int, name: string, createdAt: \DateTimeImmutable, is_member: 1|0}> $results>
     */
    public function __construct(
        public int $count,
        public ?int $next = null,
        public ?int $previous = null,
        public array $results = [],
    ) {
    }
}
