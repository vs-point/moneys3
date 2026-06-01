<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Auth\OAuth2TokenProvider;
use VsPoint\MoneyS3\Exception\AuthenticationException;

#[CoversClass(OAuth2TokenProvider::class)]
final class OAuth2TokenProviderTest extends TestCase
{
    public function testFetchesAndCachesToken(): void
    {
        $mock = new MockHandler([
            new Response(200, [], (string) json_encode([
                'access_token' => 'tok-1',
                'expires_in' => 3600,
            ])),
        ]);
        $history = [];
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $provider = new OAuth2TokenProvider(
            'https://demo.api.moneys3.eu/connect/token?AppId=APP',
            Credentials::clientCredentials('cid', 'secret'),
            new Client([
                'handler' => $stack,
            ]),
        );

        self::assertSame('tok-1', $provider->getAccessToken());
        // Second call must be served from cache (only one HTTP request was made).
        self::assertSame('tok-1', $provider->getAccessToken());
        self::assertCount(1, $history);

        $body = (string) $history[0]['request']->getBody();
        self::assertStringContainsString('grant_type=client_credentials', $body);
        self::assertStringContainsString('client_id=cid', $body);
    }

    public function testInvalidResponseThrows(): void
    {
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], (string) json_encode([
            'nope' => true,
        ]))]));

        $provider = new OAuth2TokenProvider(
            'https://x/connect/token',
            Credentials::clientCredentials('cid', 'secret'),
            new Client([
                'handler' => $stack,
            ]),
        );

        $this->expectException(AuthenticationException::class);
        $provider->getAccessToken();
    }
}
