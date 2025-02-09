<?php

namespace App\Dto\CentrifugoSender;

final readonly class SubscribeRequestDto
{
    /**
     * @param array<mixed>|null $info
     * @param array<mixed>|null $data
     */
    public function __construct(
        public string $userId,
        public string $channel,
        public ?array $info = null,
        public ?string $b64info = null,
        public ?string $client = null,
        public ?string $session = null,
        public ?array $data = null,
        public ?string $b64data = null,
    ) {
    }
}
