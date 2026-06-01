<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Journal;

use VsPoint\MoneyS3\Filter\FieldName;

/**
 * Filterable / orderable fields of the accounting journal (`journalAccs`).
 *
 * Field names verified against the live schema (`IJournalAccFilterInput`).
 *
 * @see \VsPoint\MoneyS3\Agenda\JournalEntryService
 */
enum AccountingJournalAttribute: string implements FieldName
{
    case id = 'id';
    case year = 'year';
    case date = 'date';
    case dateOfTaxing = 'dateOfTaxing';
    case sourceDocumentNumber = 'srcDocumentNumber';
    case amount = 'amount';
    case pairingSymbol = 'pairingSymbol';
    case identificationNumber = 'identificationNumber';
    case sourceDocumentId = 'srcDocumentId';
    case sourceDocumentType = 'srcDocumentType';
    case accountDebit = 'accountDebits.account';
    case accountCredit = 'accountCredits.account';
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
