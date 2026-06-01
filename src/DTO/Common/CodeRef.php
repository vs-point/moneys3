<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A reference to a record identified by its code, e.g. `{ code: "CZ" }`.
 *
 * Used for countries, currencies and warehouses.
 */
final readonly class CodeRef implements InputObject
{
    public function __construct(
        public string $code,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'code' => $this->code,
        ];
    }
}
