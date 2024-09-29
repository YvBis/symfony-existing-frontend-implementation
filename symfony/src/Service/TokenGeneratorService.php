<?php

namespace App\Service;

use App\Enum\JwtAlgorithms;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class TokenGeneratorService
{
    public function __construct(
        #[Autowire('%env(CENTRIFUGO_TOKEN_SECRET)%')]
        private readonly string $tokenSecret,
        #[Autowire('%env(CENTRIFUGO_TOKEN_TTL)%')]
        private readonly int $tokenTtl,
    ) {
    }
    public function getSubscriptionToken(int|string $userIdentifier, string $channel): string
    {
        $payload = [
            'sub' => $userIdentifier,
            'exp' => time() + $this->tokenTtl,
            'channel' => $channel,
        ];

        return JWT::encode($payload, $this->tokenSecret, JwtAlgorithms::HS256->value);
    }

    public function getConnectionToken(int|string $userId): string
    {
        $payload = [
            'sub' => $userId,
            'exp' => time() + $this->tokenTtl,
        ];

        return JWT::encode($payload, $this->tokenSecret, JwtAlgorithms::HS256->value);
    }
}
