<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\GraphQL;

/**
 * Escape hatch for emitting a raw, already-formatted GraphQL value fragment.
 *
 * Use sparingly — it bypasses the strict typing the rest of the builder enforces.
 */
final readonly class RawGraphQL
{
    public function __construct(
        public string $graphql,
    ) {
    }
}
