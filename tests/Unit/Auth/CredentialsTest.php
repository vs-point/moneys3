<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Auth\GrantType;

#[CoversClass(Credentials::class)]
final class CredentialsTest extends TestCase
{
    public function testClientCredentialsParameters(): void
    {
        $credentials = Credentials::clientCredentials('cid', 'secret');

        self::assertSame(GrantType::clientCredentials, $credentials->grantType);
        self::assertSame([
            'grant_type' => 'client_credentials',
            'client_id' => 'cid',
            'client_secret' => 'secret',
        ], $credentials->toTokenRequestParameters());
    }

    public function testPasswordGrantParameters(): void
    {
        $credentials = Credentials::password('cid', 'secret', 'user', 'pass');

        self::assertSame([
            'grant_type' => 'password',
            'client_id' => 'cid',
            'client_secret' => 'secret',
            'username' => 'user',
            'password' => 'pass',
        ], $credentials->toTokenRequestParameters());
    }
}
