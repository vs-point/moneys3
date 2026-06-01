<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Journal;

use VsPoint\MoneyS3\Filter\FieldName;

/**
 * Filterable / orderable fields of the cash journal (`journalTrs`).
 *
 * Field names verified against the live schema (`IJournalTrFilterInput`).
 *
 * @see \VsPoint\MoneyS3\Agenda\JournalTransactionService
 */
enum CashJournalAttribute: string implements FieldName
{
    case id = 'id';
    case year = 'year';
    case date = 'date';
    case sourceDocumentNumber = 'srcDocumentNumber';
    case amount = 'amount';
    case variableSymbol = 'variableSymbol';
    case identificationNumber = 'identificationNumber';
    case type = 'type';
    case accountMovement = 'accountMovement.shortCut';
    case currencyCode = 'currency.code';
    case companyIdentificationNumber = 'company.identificationNumber';
    case centre = 'centre.shortCut';
    case jobOrder = 'jobOrder.shortCut';
    case operation = 'operation.shortCut';

    public function graphQLName(): string
    {
        return $this->value;
    }
}
