<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Auth\OAuth2TokenProvider;
use VsPoint\MoneyS3\Transport\GuzzleTransport;

/**
 * Connection to a locally installed Money S3 instance (API module), by default at
 * `http://localhost:85`.
 *
 * Exposes the full {@see MoneyS3Api} surface, e.g. `$client->issuedInvoices->create(...)`.
 */
final class Client extends MoneyS3Api
{
    public function __construct(
        string $appId,
        Credentials $credentials,
        string $host = 'localhost',
        int $port = 85,
        bool $secure = false,
        ?ClientInterface $httpClient = null,
    ) {
        $httpClient ??= new GuzzleClient();

        $baseUrl = \sprintf('%s://%s:%d', $secure ? 'https' : 'http', $host, $port);
        $tokenUrl = \sprintf('%s/connect/token?AppId=%s', $baseUrl, rawurlencode($appId));
        $graphqlUrl = $baseUrl . '/graphql/';

        $tokenProvider = new OAuth2TokenProvider($tokenUrl, $credentials, $httpClient);

        parent::__construct(new GuzzleTransport($graphqlUrl, $tokenProvider, $httpClient));
    }
}
