<?php

namespace App\Dto;

use App\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserLoginDto implements DtoInterface
{
    public function __construct(
        #[Assert\NotBlank()]
        public readonly string $username,
        #[Assert\NotBlank()]
        public readonly string $password,
    )
    {
    }
}