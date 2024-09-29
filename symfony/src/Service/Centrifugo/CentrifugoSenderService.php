<?php

namespace App\Service\Centrifugo;

use App\Dto\CentrifugoSender\BroadcastRequestDto;
use App\Dto\CentrifugoSender\DisconnectRequestDto;
use App\Dto\CentrifugoSender\PublishRequestDto;
use App\Dto\CentrifugoSender\SubscribeRequestDto;
use App\Dto\CentrifugoSender\UnsubscribeRequestDto;
use App\Service\Centrifugo\Interface\CenrifugoClientInterface;
use Symfony\Component\HttpFoundation\Request;

final class CentrifugoSenderService
{
    public function __construct(
        private readonly CenrifugoClientInterface $cenrifugoClient,
    ) {
    }

    public function publish(PublishRequestDto $dto): void
    {
        $payload = array_filter([
            'channel' => $dto->channel,
            'data' => $dto->data,
            'skip_history' => $dto->skipHistory,
            'tags' => $dto->tags,
            'binary_data' => $dto->binaryData,
            'idempotency_key' => $dto->idempotencyKey,
            'delta' => $dto->delta,
        ]);

        $this->cenrifugoClient->request(Request::METHOD_POST, 'publish', ['json' => $payload]);
    }

    public function broadcast(BroadcastRequestDto $dto): void
    {
        $payload = array_filter([
            'channels' => $dto->channels,
            'data' => $dto->data,
            'skip_history' => $dto->skipHistory,
            'tags' => $dto->tags,
            'binary_data' => $dto->binaryData,
            'idempotency_key' => $dto->idempotencyKey,
            'delta' => $dto->delta,
        ]);

        $this->cenrifugoClient->request(Request::METHOD_POST, 'broadcast', ['json' => $payload]);
    }

    public function subscribe(SubscribeRequestDto $dto): void
    {
        $payload = array_filter([
            'user_id' => $dto->userId,
            'channel' => $dto->channel,
            'info' => $dto->info,
            'data' => $dto->data,
            'client' => $dto->client,
            'session' => $dto->session,
        ]);

        $this->cenrifugoClient->request(Request::METHOD_POST, 'subscribe', ['json' => $payload]);
    }

    public function unsubscribe(UnsubscribeRequestDto $dto): void
    {
        $payload = array_filter([
            'user_id' => $dto->userId,
            'channel' => $dto->channel,
            'client' => $dto->client,
            'session' => $dto->session,
        ]);

        $this->cenrifugoClient->request(Request::METHOD_POST, 'unsubscribe', ['json' => $payload]);
    }

    public function disconnect(DisconnectRequestDto $dto): void
    {
        $payload = array_filter([
            'user_id' => $dto->userId,
            'client' => $dto->client,
            'session' => $dto->session,
        ]);

        $this->cenrifugoClient->request(Request::METHOD_POST, 'disconnect', ['json' => $payload]);
    }
}
