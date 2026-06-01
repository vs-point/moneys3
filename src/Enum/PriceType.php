<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Enum;

use VsPoint\MoneyS3\GraphQL\GraphQLEnumValue;

/**
 * Interpretation of an item unit price (`priceType`).
 */
enum PriceType: string implements GraphQLEnumValue
{
    case withoutVat = 'WITHOUT_VAT';
    case onlyBase = 'ONLY_BASE';
    case withVat = 'WITH_VAT';
    case onlyVat = 'ONLY_VAT';

    public function graphQLValue(): string
    {
        return $this->value;
    }
}
