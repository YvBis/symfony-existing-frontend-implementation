<?php

namespace App\Dto\CentrifugoSender;

final readonly class PublishRequestDto
{
    /**
     * @param array<mixed>               $data
     * @param array<string, string>|null $tags
     */
    public function __construct(
        public string $channel,
        public array $data,
        public ?bool $skipHistory = null,
        public ?array $tags = null,
        public ?string $binaryData = null,
        public ?string $idempotencyKey = null,
        public ?string $delta = null
    ) {
    }
}
