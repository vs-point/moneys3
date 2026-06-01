<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Auth\OAuth2TokenProvider;
use VsPoint\MoneyS3\Transport\GuzzleTransport;

/**
 * Connection to a cloud-hosted Money S3 instance at `https://{domain}.api.moneys3.eu`.
 *
 * Exposes the full {@see MoneyS3Api} surface, e.g. `$client->issuedInvoices->create(...)`.
 */
final class CloudClient extends MoneyS3Api
{
    public function __construct(
        string $domain,
        string $appId,
        Credentials $credentials,
        ?ClientInterface $httpClient = null,
    ) {
        $httpClient ??= new GuzzleClient();

        $baseUrl = \sprintf('https://%s.api.moneys3.eu', $domain);
        $tokenUrl = \sprintf('%s/connect/token?AppId=%s', $baseUrl, rawurlencode($appId));
        $graphqlUrl = $baseUrl . '/graphql/';

        $tokenProvider = new OAuth2TokenProvider($tokenUrl, $credentials, $httpClient);

        parent::__construct(new GuzzleTransport($graphqlUrl, $tokenProvider, $httpClient));
    }
}
