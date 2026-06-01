<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A postal code reference, e.g. `{ postalCode: "60200" }`.
 */
final readonly class PostalCode implements InputObject
{
    public function __construct(
        public string $postalCode,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'postalCode' => $this->postalCode,
        ];
    }
}
