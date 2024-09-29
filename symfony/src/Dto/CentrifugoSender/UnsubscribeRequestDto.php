<?php

namespace App\Dto\CentrifugoSender;

final readonly class UnsubscribeRequestDto
{
    public function __construct(
        public string $userId,
        public string $channel,
        public ?string $client = null,
        public ?string $session = null,
    ) {
    }
}
