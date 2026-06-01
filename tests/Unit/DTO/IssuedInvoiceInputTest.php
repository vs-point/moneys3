<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\DTO\Common\Address;
use VsPoint\MoneyS3\DTO\Common\CodeRef;
use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\DTO\Common\PartnerAddress;
use VsPoint\MoneyS3\DTO\Common\PostalCode;
use VsPoint\MoneyS3\DTO\Common\ShortCutRef;
use VsPoint\MoneyS3\DTO\Common\VatRateSummary;
use VsPoint\MoneyS3\DTO\Invoice\InvoiceItem;
use VsPoint\MoneyS3\DTO\Invoice\IssuedInvoiceInput;
use VsPoint\MoneyS3\Enum\PriceType;
use VsPoint\MoneyS3\GraphQL\MutationBuilder;

/**
 * Verifies that the strictly typed invoice input renders the same GraphQL structure as
 * the official Money S3 mutation example (faktura vystavená s neskladovou položkou).
 */
#[CoversClass(IssuedInvoiceInput::class)]
#[CoversClass(InvoiceItem::class)]
final class IssuedInvoiceInputTest extends TestCase
{
    public function testRendersDocumentedExample(): void
    {
        $invoice = new IssuedInvoiceInput(
            dateOfIssue: '2026-03-02',
            dateOfTaxing: '2026-03-02',
            dateOfMaturity: '2026-03-12',
            documentNumber: '20260203',
            variableSymbol: '202603',
            description: 'Popis faktury',
            accountAssignment: new ShortCutRef('FV001'),
            vatClassification: new ShortCutRef('19Ř01,02'),
            payOn: new ShortCutRef('BAN'),
            vatRateSummaryHc: new VatRateSummary(vatRate: 21, totalWithoutVat: 100, totalVat: 50),
            partnerAddress: new PartnerAddress(
                businessAddress: new Address(
                    name: 'Seyfor, a. s.',
                    country: new CodeRef('CZ'),
                    municipality: 'Brno',
                    municipalityPostalCode: new PostalCode('60200'),
                    street: 'Drobného 555/49',
                ),
                identificationNumber: '01572377',
                vatIdentificationNumber: 'CZ01572377',
            ),
            paymentMethod: 'převodem',
            items: [
                new InvoiceItem(
                    description: 'Popis / název položky',
                    amount: 1,
                    unitPriceHc: 100,
                    vatRate: 0,
                    priceType: PriceType::withoutVat,
                ),
            ],
        );

        $document = (new MutationBuilder())->build('createIssuedInvoice', [
            'issuedInvoice' => $invoice,
            'definitionXMLTransfer' => new DefinitionXMLTransfer('_FP+FV'),
        ]);

        self::assertStringContainsString('createIssuedInvoice(issuedInvoice: {', $document);
        self::assertStringContainsString('dateOfIssue: "2026-03-02"', $document);
        self::assertStringContainsString('accountAssignment: { shortCut: "FV001" }', $document);
        self::assertStringContainsString('payOn: { shortCut: "BAN" }', $document);
        self::assertStringContainsString(
            'vatRateSummaryHc: { vatRate: 21, totalWithoutVat: 100, totalVat: 50 }',
            $document
        );
        self::assertStringContainsString(
            'businessAddress: { name: "Seyfor, a. s.", country: { code: "CZ" }',
            $document
        );
        self::assertStringContainsString('municipalityPostalCode: { postalCode: "60200" }', $document);
        self::assertStringContainsString(
            'items: [{ description: "Popis / název položky", amount: 1, unitPriceHc: 100, vatRate: 0, priceType: WITHOUT_VAT }]',
            $document
        );
        self::assertStringContainsString('definitionXMLTransfer: { shortCut: "_FP+FV" }', $document);
        self::assertStringEndsWith('{ guid isSuccess } }', $document);
    }
}
