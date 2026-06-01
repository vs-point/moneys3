<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Client\CloudClient;

#[CoversClass(CloudClient::class)]
final class CloudClientTest extends TestCase
{
    public function testResolvesCloudEndpointsAndQueries(): void
    {
        $mock = new MockHandler([
            new Response(200, [], (string) json_encode([
                'access_token' => 'tok',
                'expires_in' => 3600,
            ])),
            new Response(200, [], (string) json_encode([
                'data' => [
                    'agendas' => [
                        'items' => [[
                            'name' => 'Demo',
                            'guid' => 'G',
                        ]],
                    ],
                ],
            ])),
        ]);
        $history = [];
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $client = new CloudClient(
            'demo',
            'APP-123',
            Credentials::clientCredentials('cid', 'secret'),
            new GuzzleClient([
                'handler' => $stack,
            ]),
        );

        $result = $client->agendas->query();

        self::assertCount(1, $result);
        self::assertSame('Demo', $result->first()?->name);

        self::assertSame(
            'https://demo.api.moneys3.eu/connect/token?AppId=APP-123',
            (string) $history[0]['request']->getUri(),
        );
        self::assertSame('https://demo.api.moneys3.eu/graphql/', (string) $history[1]['request']->getUri());
    }
}
