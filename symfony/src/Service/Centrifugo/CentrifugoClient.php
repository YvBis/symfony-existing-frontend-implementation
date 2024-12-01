<?php

namespace App\Service\Centrifugo;

use App\Service\Centrifugo\Exception\CentrifugoClientException;
use App\Service\Centrifugo\Interface\CenrifugoClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class CentrifugoClient implements CenrifugoClientInterface
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        #[Autowire('%env(CENTRIFUGO_HTTP_API_ENDPOINT)%')]
        private readonly string $baseUrl,
        #[Autowire('%env(CENTRIFUGO_HTTP_API_KEY)%')]
        private readonly string $apiKey,
    ) {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try {
            return $this
                ->httpClient
                ->withOptions([
                    'base_uri' => $this->baseUrl,
                    'headers' => array_merge($options['headers'] ?? [], [
                        'X-API-Key' => $this->apiKey,
                        'X-Centrifugo-Error-Mode' => 'transport',
                    ]),
                ])
                ->request($method, $url, $options);
        } catch (\Throwable $exception) {
            throw $this->processException($exception);
        }
    }

    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        try {
            return $this->httpClient->stream($responses, $timeout);
        } catch (\Throwable $exception) {
            throw $this->processException($exception);
        }
    }

    public function withOptions(array $options): static
    {
        $clone = clone $this;

        $clone->httpClient = $clone->httpClient->withOptions($options);

        return $clone;
    }

    private function processException(\Throwable $exception): CentrifugoClientException
    {
        throw new CentrifugoClientException($exception->getMessage(), $exception->getCode(), $exception);
    }
}
