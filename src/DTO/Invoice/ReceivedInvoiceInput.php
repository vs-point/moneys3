<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Invoice;

use Brick\DateTime\LocalDate;
use VsPoint\MoneyS3\DTO\Common\CodeRef;
use VsPoint\MoneyS3\DTO\Common\NumericalSerie;
use VsPoint\MoneyS3\DTO\Common\PartnerAddress;
use VsPoint\MoneyS3\DTO\Common\RelatedDocument;
use VsPoint\MoneyS3\DTO\Common\ShortCutRef;
use VsPoint\MoneyS3\DTO\Common\VatRateSummary;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Strictly typed input for creating a received invoice (faktura přijatá).
 *
 * @see \VsPoint\MoneyS3\Agenda\ReceivedInvoiceService
 */
final readonly class ReceivedInvoiceInput implements InputObject
{
    /**
     * @param list<InvoiceItem>     $items
     * @param list<RelatedDocument> $relatedDocuments
     */
    public function __construct(
        public LocalDate|\DateTimeInterface|string|null $dateOfIssue = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfTaxing = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfVatApplication = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfMaturity = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfAccountingEvent = null,
        public ?string $documentNumber = null,
        public ?NumericalSerie $numericalSerie = null,
        public ?string $variableSymbol = null,
        public ?string $pairingSymbol = null,
        public ?string $specificSymbol = null,
        public ?string $constantSymbol = null,
        public ?string $receivedDocumentNumber = null,
        public ?string $description = null,
        public ?ShortCutRef $accountAssignment = null,
        public ?ShortCutRef $vatClassification = null,
        public ?ShortCutRef $payFrom = null,
        public ?VatRateSummary $vatRateSummaryHc = null,
        public ?VatRateSummary $vatRateSummary = null,
        public ?CodeRef $currency = null,
        public ?PartnerAddress $partnerAddress = null,
        public ?string $paymentMethod = null,
        public array $items = [],
        public array $relatedDocuments = [],
        public ?string $guid = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'guid' => $this->guid,
            'dateOfIssue' => $this->dateOfIssue,
            'dateOfTaxing' => $this->dateOfTaxing,
            'dateOfVatApplication' => $this->dateOfVatApplication,
            'dateOfMaturity' => $this->dateOfMaturity,
            'dateOfAccountingEvent' => $this->dateOfAccountingEvent,
            'documentNumber' => $this->documentNumber,
            'numericalSerie' => $this->numericalSerie,
            'variableSymbol' => $this->variableSymbol,
            'pairingSymbol' => $this->pairingSymbol,
            'specificSymbol' => $this->specificSymbol,
            'constantSymbol' => $this->constantSymbol,
            'receivedDocumentNumber' => $this->receivedDocumentNumber,
            'description' => $this->description,
            'accountAssignment' => $this->accountAssignment,
            'vatClassification' => $this->vatClassification,
            'payFrom' => $this->payFrom,
            'vatRateSummaryHc' => $this->vatRateSummaryHc,
            'vatRateSummary' => $this->vatRateSummary,
            'currency' => $this->currency,
            'partnerAddress' => $this->partnerAddress,
            'paymentMethod' => $this->paymentMethod,
            'items' => $this->items === [] ? null : $this->items,
            'relatedDocuments' => $this->relatedDocuments === [] ? null : $this->relatedDocuments,
        ];
    }
}
