<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Transport;

/**
 * Sends GraphQL documents to a Money S3 endpoint and returns the decoded response.
 */
interface Transport
{
    /**
     * @param array<string, mixed> $variables
     */
    public function execute(string $document, array $variables = []): GraphQLResponse;
}
