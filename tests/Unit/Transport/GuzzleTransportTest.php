<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Auth\TokenProvider;
use VsPoint\MoneyS3\Exception\TransportException;
use VsPoint\MoneyS3\Transport\GuzzleTransport;

#[CoversClass(GuzzleTransport::class)]
final class GuzzleTransportTest extends TestCase
{
    public function testExecuteSendsAuthenticatedRequestAndDecodesData(): void
    {
        $mock = new MockHandler([
            new Response(200, [], (string) json_encode([
                'data' => [
                    'agendas' => [
                        'items' => [[
                            'name' => 'Demo',
                        ]],
                    ],
                ],
            ])),
        ]);
        $history = [];
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $transport = new GuzzleTransport(
            'https://demo.api.moneys3.eu/graphql/',
            $this->tokenProvider('the-token'),
            new Client([
                'handler' => $stack,
            ]),
        );

        $response = $transport->execute('query { agendas { items { name } } }');

        self::assertSame([[
            'name' => 'Demo',
        ]], $response->field('agendas')['items']);

        /** @var Request $request */
        $request = $history[0]['request'];
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer the-token', $request->getHeaderLine('Authorization'));
        self::assertStringContainsString('query { agendas', (string) $request->getBody());
    }

    public function testNonJsonResponseThrows(): void
    {
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '<html>nope</html>')]));
        $transport = new GuzzleTransport('https://x/graphql/', $this->tokenProvider('t'), new Client([
            'handler' => $stack,
        ]));

        $this->expectException(TransportException::class);
        $transport->execute('query { agendas { items { name } } }');
    }

    private function tokenProvider(string $token): TokenProvider
    {
        return new class($token) implements TokenProvider {
            public function __construct(
                private readonly string $token
            ) {
            }

            public function getAccessToken(): string
            {
                return $this->token;
            }
        };
    }
}
