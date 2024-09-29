<?php

namespace App\Service\Centrifugo\Interface;

use App\Dto\CentrifugoSender\BroadcastRequestDto;
use App\Dto\CentrifugoSender\DisconnectRequestDto;
use App\Dto\CentrifugoSender\PublishRequestDto;
use App\Dto\CentrifugoSender\SubscribeRequestDto;
use App\Dto\CentrifugoSender\UnsubscribeRequestDto;

interface CentrifugoServiceInterface
{
    public function publish(PublishRequestDto $dto): void;

    public function broadcast(BroadcastRequestDto $dto): void;

    public function subscribe(SubscribeRequestDto $dto): void;

    public function unsubscribe(UnsubscribeRequestDto $dto): void;

    public function disconnect(DisconnectRequestDto $dto): void;
}
