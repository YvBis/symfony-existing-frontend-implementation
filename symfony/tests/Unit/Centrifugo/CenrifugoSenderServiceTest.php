<?php

namespace App\Tests\Unit\Centrifugo;

use App\Dto\CentrifugoSender\BroadcastRequestDto;
use App\Dto\CentrifugoSender\DisconnectRequestDto;
use App\Dto\CentrifugoSender\PublishRequestDto;
use App\Dto\CentrifugoSender\SubscribeRequestDto;
use App\Dto\CentrifugoSender\UnsubscribeRequestDto;
use App\Service\Centrifugo\CentrifugoSenderService;
use App\Service\Centrifugo\Interface\CenrifugoClientInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CenrifugoSenderServiceTest extends TestCase
{
    public function testPublish(): void
    {
        $centrifugoClient = $this->createMock(CenrifugoClientInterface::class);
        $centrifugoClient->expects($this->once())->method('request')->with(
            Request::METHOD_POST,
            'publish',
            [
                'json' => [
                    'channel' => 'testChannel',
                    'data' => ['testData'],
                    'skip_history' => true,
                    'tags' => ['tag1' => 'tag1 data', 'tag2' => 'tag 2 data'],
                    'binary_data' => 'testBinaryData',
                    'idempotency_key' => 'testIdempotencyKey',
                    'delta' => 'some delta',
                ],
            ]
        );

        $sut = new CentrifugoSenderService($centrifugoClient);

        $sut->publish(new PublishRequestDto(
            'testChannel',
            ['testData'],
            true,
            ['tag1' => 'tag1 data', 'tag2' => 'tag 2 data'],
            'testBinaryData',
            'testIdempotencyKey',
            'some delta'
        ));
    }

    public function testBroadcast(): void
    {
        $centrifugoClient = $this->createMock(CenrifugoClientInterface::class);
        $centrifugoClient->expects($this->once())->method('request')->with(
            Request::METHOD_POST,
            'broadcast',
            [
                'json' => [
                    'channels' => ['testChannel', 'testChannel2'],
                    'data' => ['testData'],
                ],
            ]
        );

        $sut = new CentrifugoSenderService($centrifugoClient);

        $sut->broadcast(new BroadcastRequestDto(
            ['testChannel', 'testChannel2'],
            ['testData']
        ));
    }

    public function testSubscribe(): void
    {
        $centrifugoClient = $this->createMock(CenrifugoClientInterface::class);
        $centrifugoClient->expects($this->once())->method('request')->with(
            Request::METHOD_POST,
            'subscribe',
            [
                'json' => [
                    'user_id' => 'testUserId',
                    'channel' => 'testChannel',
                ],
            ]
        );

        $sut = new CentrifugoSenderService($centrifugoClient);

        $sut->subscribe(new SubscribeRequestDto(
            'testUserId',
            'testChannel',
        ));
    }

    public function testUnsubscribe(): void
    {
        $centrifugoClient = $this->createMock(CenrifugoClientInterface::class);
        $centrifugoClient->expects($this->once())->method('request')->with(
            Request::METHOD_POST,
            'unsubscribe',
            [
                'json' => [
                    'user_id' => 'testUserId',
                    'channel' => 'testChannel',
                ],
            ]
        );

        $sut = new CentrifugoSenderService($centrifugoClient);

        $sut->unsubscribe(new UnsubscribeRequestDto(
            'testUserId',
            'testChannel',
        ));
    }

    public function testDisconnect(): void
    {
        $centrifugoClient = $this->createMock(CenrifugoClientInterface::class);
        $centrifugoClient->expects($this->once())->method('request')->with(
            Request::METHOD_POST,
            'disconnect',
            [
                'json' => [
                    'user_id' => 'testUserId',
                ],
            ]
        );

        $sut = new CentrifugoSenderService($centrifugoClient);

        $sut->disconnect(new DisconnectRequestDto(
            'testUserId',
        ));
    }
}
