<?php

namespace App\Tests\Doubles;

use App\Service\Centrifugo\Interface\CenrifugoClientInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CentrifugoClientDouble extends MockHttpClient implements CenrifugoClientInterface
{
    public function __construct()
    {
        parent::__construct($this->initResponseFactory(...));
    }

    private function initResponseFactory(string $method, string $url, array $options): MockResponse
    {
        return new MockResponse();
    }
}
