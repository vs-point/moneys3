<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use VsPoint\MoneyS3\Exception\AuthenticationException;

/**
 * Obtains and caches OAuth2 access tokens from the Money S3 `connect/token` endpoint.
 *
 * Tokens are cached in memory and transparently refreshed shortly before expiry.
 */
final class OAuth2TokenProvider implements TokenProvider
{
    private ?AccessToken $token = null;

    public function __construct(
        private readonly string $tokenUrl,
        private readonly Credentials $credentials,
        private readonly ClientInterface $httpClient = new Client(),
    ) {
    }

    public function getAccessToken(): string
    {
        $token = $this->token;
        if ($token !== null && $token->isValid(time())) {
            return $token->value;
        }

        $token = $this->requestToken();
        $this->token = $token;

        return $token->value;
    }

    private function requestToken(): AccessToken
    {
        try {
            $response = $this->httpClient->request('POST', $this->tokenUrl, [
                'form_params' => $this->credentials->toTokenRequestParameters(),
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (GuzzleException $exception) {
            throw new AuthenticationException(
                'Failed to obtain Money S3 access token: ' . $exception->getMessage(),
                0,
                $exception
            );
        }

        $payload = json_decode((string) $response->getBody(), true);
        if (!\is_array($payload) || !\is_string($payload['access_token'] ?? null)) {
            throw new AuthenticationException('Money S3 token endpoint returned an unexpected response.');
        }

        $expiresIn = \is_int($payload['expires_in'] ?? null) ? $payload['expires_in'] : 3600;

        return new AccessToken($payload['access_token'], time() + $expiresIn);
    }
}
