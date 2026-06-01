<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Enum;

use VsPoint\MoneyS3\GraphQL\GraphQLEnumValue;

/**
 * VAT entry mode (`vatMode`) — Money S3 SK only.
 */
enum VatMode: string implements GraphQLEnumValue
{
    case afterPayment = 'AFTER_PAYMENT';
    case issuingDocument = 'ISSUING_DOCUMENT';

    public function graphQLValue(): string
    {
        return $this->value;
    }
}
