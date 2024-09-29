<?php

namespace App\Dto\CentrifugoSender;

final class BroadcastRequestDto
{
    /**
     * @param list<int|null> $channels
     * @param array<mixed> $data
     * @param array<string, string>|null $tags
     */
    public function __construct(
        public array $channels,
        public array $data,
        public ?bool $skipHistory = null,
        public ?array $tags = null,
        public ?string $binaryData = null,
        public ?string $idempotencyKey = null,
        public ?string $delta = null
    ) {
    }
}
