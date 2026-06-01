<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Schema;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\DTO\Invoice\IssuedInvoiceAttribute;
use VsPoint\MoneyS3\DTO\Journal\AccountingJournalAttribute;
use VsPoint\MoneyS3\DTO\Journal\CashJournalAttribute;
use VsPoint\MoneyS3\DTO\Warehouse\WarehouseStockAttribute;
use VsPoint\MoneyS3\Enum\AbsenceType;
use VsPoint\MoneyS3\Enum\PriceType;
use VsPoint\MoneyS3\Filter\FieldName;
use VsPoint\MoneyS3\Filter\OrderDirection;

/**
 * Contract test that validates the library against the **live Money S3 GraphQL schema**
 * via introspection (no OAuth token needed for `__schema`/`__type`).
 *
 * It asserts that every query root field, every create/update/delete mutation, and every
 * enum token the library emits actually exist in the published schema. If the endpoint is
 * unreachable (offline CI, network policy), the test self-skips instead of failing.
 *
 * Endpoint: https://s3api.api.moneys3.eu/graphql/
 */
#[Group('schema')]
final class LiveSchemaContractTest extends TestCase
{
    private const string ENDPOINT = 'https://s3api.api.moneys3.eu/graphql/';

    /**
     * @var array<string, mixed>
     */
    private static array $schema = [];

    public static function setUpBeforeClass(): void
    {
        $query = <<<'GQL'
            query {
                Query: __type(name: "Query") { fields { name } }
                Mutation: __type(name: "Mutation") { fields { name } }
                PriceType: __type(name: "PriceType") { enumValues { name } }
                AbsenceType: __type(name: "AbsenceType") { enumValues { name } }
                Sort: __type(name: "SortEnumType") { enumValues { name } }
                IssuedInvoiceInput: __type(name: "IssuedInvoiceInput") { inputFields { name } }
                CashJournalFilter: __type(name: "IJournalTrFilterInput") { inputFields { name } }
                AccountingJournalFilter: __type(name: "IJournalAccFilterInput") { inputFields { name } }
                WarehouseStockFilter: __type(name: "IWarehouseStockFilterInput") { inputFields { name } }
                IssuedInvoiceFilter: __type(name: "IIssuedInvoiceFilterInput") { inputFields { name } }
            }
            GQL;

        try {
            $response = (new Client())->request('POST', self::ENDPOINT, [
                'timeout' => 15,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => (string) json_encode([
                    'query' => $query,
                ]),
            ]);
        } catch (GuzzleException $exception) {
            return; // leave $schema empty -> tests skip
        }

        $payload = json_decode((string) $response->getBody(), true);
        if (\is_array($payload) && \is_array($payload['data'] ?? null)) {
            /** @var array<string, mixed> $data */
            $data = $payload['data'];
            self::$schema = $data;
        }
    }

    protected function setUp(): void
    {
        if (self::$schema === []) {
            self::markTestSkipped('Money S3 GraphQL endpoint is not reachable.');
        }
    }

    public function testAllQueryRootFieldsExist(): void
    {
        $available = $this->names('Query', 'fields');

        foreach ([
            'issuedInvoices', 'receivedInvoices', 'bankStatements', 'cashVouchers',
            'receivedOrders', 'issuedOrders', 'receivedSlips', 'issuedSlips',
            'stockTakingDocuments', 'journalTrs', 'journalAccs', 'warehouseStocks', 'agendas',
        ] as $field) {
            self::assertContains($field, $available, "Query.{$field} is missing from the live schema");
        }
    }

    public function testAllMutationsExist(): void
    {
        $available = $this->names('Mutation', 'fields');

        $cud = [
            'IssuedInvoice', 'ReceivedInvoice', 'BankStatement', 'CashVoucher',
            'ReceivedOrder', 'IssuedOrder', 'ReceivedSlip', 'IssuedSlip',
            'StockTakingDocument', 'Wage',
        ];

        foreach ($cud as $entity) {
            foreach (['create', 'update', 'delete'] as $verb) {
                self::assertContains(
                    $verb . $entity,
                    $available,
                    "Mutation.{$verb}{$entity} is missing from the live schema"
                );
            }
        }
    }

    public function testPriceTypeTokensAreValid(): void
    {
        $valid = $this->names('PriceType', 'enumValues');

        foreach (PriceType::cases() as $case) {
            self::assertContains($case->value, $valid, "PriceType token {$case->value} is not in the live schema");
        }
    }

    public function testAbsenceTypeTokensAreValid(): void
    {
        $valid = $this->names('AbsenceType', 'enumValues');

        foreach (AbsenceType::cases() as $case) {
            self::assertContains($case->value, $valid, "AbsenceType token {$case->value} is not in the live schema");
        }
    }

    public function testOrderDirectionTokensAreValid(): void
    {
        $valid = $this->names('Sort', 'enumValues');

        foreach (OrderDirection::cases() as $case) {
            self::assertContains($case->value, $valid, "OrderDirection token {$case->value} is not in the live schema");
        }
    }

    public function testIssuedInvoiceInputFieldsExist(): void
    {
        $available = $this->names('IssuedInvoiceInput', 'inputFields');

        foreach ([
            'guid', 'dateOfIssue', 'dateOfTaxing', 'dateOfMaturity', 'dateOfAccountingEvent',
            'documentNumber', 'numericalSerie', 'variableSymbol', 'pairingSymbol', 'specificSymbol',
            'constantSymbol', 'description', 'accountAssignment', 'vatClassification', 'payOn',
            'vatRateSummaryHc', 'vatRateSummary', 'currency', 'partnerAddress', 'paymentMethod',
            'vatMode', 'items', 'relatedDocuments',
        ] as $field) {
            self::assertContains($field, $available, "IssuedInvoiceInput.{$field} is missing from the live schema");
        }
    }

    public function testCashJournalAttributesAreFilterable(): void
    {
        $this->assertAttributesFilterable('CashJournalFilter', CashJournalAttribute::cases());
    }

    public function testAccountingJournalAttributesAreFilterable(): void
    {
        $this->assertAttributesFilterable('AccountingJournalFilter', AccountingJournalAttribute::cases());
    }

    public function testWarehouseStockAttributesAreFilterable(): void
    {
        $this->assertAttributesFilterable('WarehouseStockFilter', WarehouseStockAttribute::cases());
    }

    public function testIssuedInvoiceAttributesAreFilterable(): void
    {
        $this->assertAttributesFilterable('IssuedInvoiceFilter', IssuedInvoiceAttribute::cases());
    }

    /**
     * Every attribute's root segment (before the first dot) must be a real filterable
     * field of the agenda's filter input type.
     *
     * @param list<FieldName> $attributes
     */
    private function assertAttributesFilterable(string $filterType, array $attributes): void
    {
        $available = $this->names($filterType, 'inputFields');

        foreach ($attributes as $attribute) {
            $root = explode('.', $attribute->graphQLName())[0];
            self::assertContains($root, $available, "Field '{$root}' is not filterable in {$filterType}");
        }
    }

    /**
     * @return list<string>
     */
    private function names(string $type, string $collection): array
    {
        $typeData = self::$schema[$type] ?? null;
        self::assertIsArray($typeData, "Type {$type} was not returned by introspection");

        $entries = $typeData[$collection] ?? null;
        self::assertIsArray($entries, "{$type}.{$collection} was not returned by introspection");

        $names = [];
        foreach ($entries as $entry) {
            if (\is_array($entry) && \is_string($entry['name'] ?? null)) {
                $names[] = $entry['name'];
            }
        }

        return $names;
    }
}
