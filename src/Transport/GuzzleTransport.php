<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use VsPoint\MoneyS3\Auth\TokenProvider;
use VsPoint\MoneyS3\Exception\TransportException;

/**
 * Guzzle-backed GraphQL transport. Authenticates each request with a bearer token
 * supplied by the {@see TokenProvider}.
 */
final class GuzzleTransport implements Transport
{
    public function __construct(
        private readonly string $endpoint,
        private readonly TokenProvider $tokenProvider,
        private readonly ClientInterface $httpClient = new Client(),
    ) {
    }

    public function execute(string $document, array $variables = []): GraphQLResponse
    {
        $body = [
            'query' => $document,
        ];
        if ($variables !== []) {
            $body['variables'] = $variables;
        }

        try {
            $response = $this->httpClient->request('POST', $this->endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->tokenProvider->getAccessToken(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        } catch (GuzzleException $exception) {
            throw new TransportException('Money S3 GraphQL request failed: ' . $exception->getMessage(), 0, $exception);
        }

        $payload = json_decode((string) $response->getBody(), true);
        if (!\is_array($payload)) {
            throw new TransportException('Money S3 GraphQL endpoint returned a non-JSON response.');
        }

        /** @var array<string, mixed> $data */
        $data = \is_array($payload['data'] ?? null) ? $payload['data'] : [];
        /** @var list<array<string, mixed>> $errors */
        $errors = \is_array($payload['errors'] ?? null) ? array_values($payload['errors']) : [];

        return new GraphQLResponse($data, $errors);
    }
}
