<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\TokenGeneratorService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPUnit\Framework\TestCase;

final class TokenGeneratorServceTest extends TestCase
{
    public function testGenerateSubscriptionToken(): void
    {
        $tokenGeneratorService = new TokenGeneratorService('test-secret', 3600);
        $tokenFromIntId = $tokenGeneratorService->getSubscriptionToken(123, 'test-channel');
        $tokenFromStringId = $tokenGeneratorService->getSubscriptionToken('123-test', 'test-channel');
        $tokenFromIntIdPayload = JWT::decode(
            $tokenFromIntId,
            new Key('test-secret', 'HS256'),
        );
        $tokenFromStringIdPayload = JWT::decode(
            $tokenFromStringId,
            new Key('test-secret', 'HS256'),
        );

        $this->assertIsString($tokenFromStringId);
        $this->assertIsString($tokenFromIntId);
        $this->assertSame(123, $tokenFromIntIdPayload->sub);
        $this->assertSame('test-channel', $tokenFromIntIdPayload->channel);
        $this->assertSame('123-test', $tokenFromStringIdPayload->sub);
        $this->assertSame('test-channel', $tokenFromStringIdPayload->channel);
    }
}
