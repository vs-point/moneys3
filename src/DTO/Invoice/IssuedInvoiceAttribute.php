<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Invoice;

use VsPoint\MoneyS3\Filter\FieldName;

/**
 * Filterable / orderable fields of issued invoices (`issuedInvoices`).
 *
 * Field names verified against the live schema (`IIssuedInvoiceFilterInput`).
 *
 * @see \VsPoint\MoneyS3\Agenda\IssuedInvoiceService
 */
enum IssuedInvoiceAttribute: string implements FieldName
{
    case id = 'id';
    case year = 'year';
    case guid = 'guid';
    case documentNumber = 'documentNumber';
    case description = 'description';
    case variableSymbol = 'variableSymbol';
    case orderNumber = 'orderNumber';
    case dateOfIssue = 'dateOfIssue';
    case dateOfAccountingEvent = 'dateOfAccountingEvent';
    case dateOfTaxing = 'dateOfTaxing';
    case dateOfMaturity = 'dateOfMaturity';
    case dateOfPayment = 'dateOfPayment';
    case totalWithVatHc = 'totalWithVatHc';
    case amountToPayHc = 'amountToPayHc';
    case remainingAmountToPayHc = 'remainingAmountToPayHc';
    case amountToPay = 'amountToPay';
    case remainingAmountToPay = 'remainingAmountToPay';
    case currencyCode = 'currency.code';
    case centre = 'centre.shortCut';
    case jobOrder = 'jobOrder.shortCut';
    case operation = 'operation.shortCut';

    public function graphQLName(): string
    {
        return $this->value;
    }
}
