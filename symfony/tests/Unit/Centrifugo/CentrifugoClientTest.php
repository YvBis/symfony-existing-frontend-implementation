<?php

namespace App\Tests\Unit\Centrifugo;

use App\Service\Centrifugo\CentrifugoClient;
use App\Service\Centrifugo\Exception\CentrifugoClientException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CentrifugoClientTest extends TestCase
{
    public function testSuccessfulRequest(): void
    {
        $sut = new CentrifugoClient(new MockHttpClient([new MockResponse()]), 'http://localhost', 'secret');
        $response = $sut->request(Request::METHOD_GET, '/test');

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testErrorResponse(): void
    {
        $response = new MockResponse('some error', ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR]);

        $sut = new CentrifugoClient(new MockHttpClient([$response]), 'http://localhost', 'secret');
        $response = $sut->request(Request::METHOD_GET, '/test');

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::expectException(ServerException::class);
        $response->getContent();
    }

    public function testClientException(): void
    {
        $httpClient = self::createMock(HttpClientInterface::class);
        $httpClient->method('withOptions')
            ->willReturn($httpClient);
        $httpClient
            ->method('request')
            ->with(Request::METHOD_GET, '/test')
            ->willThrowException(new \Exception('generic client error'));

        $sut = new CentrifugoClient($httpClient, 'http://localhost', 'secret');
        self::expectException(CentrifugoClientException::class);
        self::expectExceptionMessage('generic client error');
        $sut->request(Request::METHOD_GET, '/test');
    }
}
