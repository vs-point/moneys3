<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Cash;

use Brick\DateTime\LocalDate;
use VsPoint\MoneyS3\DTO\Common\CodeRef;
use VsPoint\MoneyS3\DTO\Common\NumericalSerie;
use VsPoint\MoneyS3\DTO\Common\PartnerAddress;
use VsPoint\MoneyS3\DTO\Common\ShortCutRef;
use VsPoint\MoneyS3\DTO\Common\VatRateSummary;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Strictly typed input for creating a cash document (pokladní doklad).
 *
 * `isExpense: true` marks a cash expense, `false` a cash receipt.
 *
 * @see \VsPoint\MoneyS3\Agenda\CashVoucherService
 */
final readonly class CashVoucherInput implements InputObject
{
    public function __construct(
        public ?string $description = null,
        public ?NumericalSerie $numericalSerie = null,
        public ?string $documentNumber = null,
        public ?string $variableSymbol = null,
        public ?string $pairingSymbol = null,
        public ?string $specificSymbol = null,
        public ?string $constantSymbol = null,
        public ?string $receivedDocumentNumber = null,
        public ?bool $isExpense = null,
        public ?ShortCutRef $cashBox = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfIssue = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfTaxing = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfAccountingEvent = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfVatApplication = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfPayment = null,
        public ?ShortCutRef $accountAssignment = null,
        public ?ShortCutRef $vatClassification = null,
        public ?PartnerAddress $partnerAddress = null,
        public ?VatRateSummary $vatRateSummaryHc = null,
        public ?VatRateSummary $vatRateSummary = null,
        public ?CodeRef $currency = null,
        public ?string $guid = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'guid' => $this->guid,
            'description' => $this->description,
            'numericalSerie' => $this->numericalSerie,
            'documentNumber' => $this->documentNumber,
            'variableSymbol' => $this->variableSymbol,
            'pairingSymbol' => $this->pairingSymbol,
            'specificSymbol' => $this->specificSymbol,
            'constantSymbol' => $this->constantSymbol,
            'receivedDocumentNumber' => $this->receivedDocumentNumber,
            'isExpense' => $this->isExpense,
            'cashBox' => $this->cashBox,
            'dateOfIssue' => $this->dateOfIssue,
            'dateOfTaxing' => $this->dateOfTaxing,
            'dateOfAccountingEvent' => $this->dateOfAccountingEvent,
            'dateOfVatApplication' => $this->dateOfVatApplication,
            'dateOfPayment' => $this->dateOfPayment,
            'accountAssignment' => $this->accountAssignment,
            'vatClassification' => $this->vatClassification,
            'partnerAddress' => $this->partnerAddress,
            'vatRateSummaryHc' => $this->vatRateSummaryHc,
            'vatRateSummary' => $this->vatRateSummary,
            'currency' => $this->currency,
        ];
    }
}
