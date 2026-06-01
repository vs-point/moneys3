<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Enum;

use VsPoint\MoneyS3\GraphQL\GraphQLEnumValue;

/**
 * Document type used when linking documents together (`relatedDocuments`).
 */
enum DocumentType: string implements GraphQLEnumValue
{
    case issuedInvoice = 'ISSUED_INVOICE';
    case receivedInvoice = 'RECEIVED_INVOICE';
    case issuedOrder = 'ISSUED_ORDER';
    case receivedOrder = 'RECEIVED_ORDER';
    case receivedSlip = 'RECEIVED_SLIP';
    case issuedSlip = 'ISSUED_SLIP';

    public function graphQLValue(): string
    {
        return $this->value;
    }
}
