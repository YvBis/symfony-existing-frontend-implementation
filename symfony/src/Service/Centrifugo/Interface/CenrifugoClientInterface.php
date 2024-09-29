<?php

namespace App\Service\Centrifugo\Interface;

use App\Service\Centrifugo\Exception\CentrifugoClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

interface CenrifugoClientInterface extends HttpClientInterface
{
    /**
     * @throws CentrifugoClientException
     * @param array<mixed> $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface;

    /**
     * @throws CentrifugoClientException
     */
    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface;

    /**
     * @param array<mixed> $options
     */
    public function withOptions(array $options): static;
}
