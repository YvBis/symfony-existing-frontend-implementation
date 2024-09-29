<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class NewMessageDto implements DtoInterface
{
    public function __construct(
        #[Assert\NotBlank()]
        public readonly string $content,
    ) {
    }
}
