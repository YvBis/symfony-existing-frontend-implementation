<?php

declare(strict_types=1);

namespace App\Dto\CentrifugoSender;

final readonly class DisconnectRequestDto
{
    /**
     * @param ?list<string>                     $whitelist
     * @param ?array{reason: string, code: int} $disconnectData
     */
    public function __construct(
        public string $userId,
        public ?string $client = null,
        public ?string $session = null,
        public ?array $whitelist = null,
        public ?array $disconnectData = null,
    ) {
    }
}
