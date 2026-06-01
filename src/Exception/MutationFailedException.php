<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Exception;

/**
 * Thrown when a write mutation is accepted by GraphQL but Money S3 reports `isSuccess: false`.
 */
final class MutationFailedException extends MoneyS3Exception
{
    public function __construct(
        string $message,
        public readonly ?string $guid = null,
    ) {
        parent::__construct($message);
    }
}
