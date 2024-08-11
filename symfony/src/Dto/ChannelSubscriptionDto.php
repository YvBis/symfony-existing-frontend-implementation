<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ChannelSubscriptionDto implements DtoInterface
{
    public function __construct(
        #[Assert\NotBlank()]
        public readonly string $channelName,
    ) {
    }
}
