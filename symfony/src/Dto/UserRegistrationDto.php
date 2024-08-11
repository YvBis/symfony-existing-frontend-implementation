<?php

namespace App\Dto;

use App\Entity\User;
use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationDto implements DtoInterface
{
    public function __construct(
        #[Assert\NotBlank()]
        public readonly string $plainPassword,
        #[Assert\NotBlank()]
        #[Assert\Email()]
        #[AppAssert\UniqueEntityField(entity: User::class, field: 'email')]
        public readonly string $email,
        #[Assert\NotBlank()]
        #[AppAssert\UniqueEntityField(entity: User::class, field: 'username')]
        public readonly string $username,
    ) {
    }
}
