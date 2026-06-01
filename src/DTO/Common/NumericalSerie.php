<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Numerical series reference used for automatic document numbering (`numericalSerie`).
 *
 * When you supply an explicit `documentNumber` you do not need a numerical series.
 */
final readonly class NumericalSerie implements InputObject
{
    public function __construct(
        public string $prefix,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'prefix' => $this->prefix,
        ];
    }
}
