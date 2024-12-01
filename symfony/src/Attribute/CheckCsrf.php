<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class CheckCsrf
{
    public function __construct(
        public string $id,
        public string $tokenKey = 'x-csrf',
    ) {
    }
}
