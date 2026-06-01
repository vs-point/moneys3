<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Filter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\DTO\Journal\CashJournalAttribute;
use VsPoint\MoneyS3\Filter\FilterOperator;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\ValueEncoder;

#[CoversClass(Where::class)]
final class WhereTest extends TestCase
{
    private ValueEncoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new ValueEncoder();
    }

    public function testTypedAttributeField(): void
    {
        $where = Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026);

        self::assertSame('{ year: { eq: 2026 } }', $this->encoder->encode($where));
    }

    public function testTypedAttributeNestsDottedPath(): void
    {
        $where = Where::field(CashJournalAttribute::companyIdentificationNumber, FilterOperator::eq, '01572377');

        self::assertSame('{ company: { identificationNumber: { eq: "01572377" } } }', $this->encoder->encode($where));
    }

    public function testRawPathEscapeHatch(): void
    {
        $where = Where::path('company.identificationNumber', FilterOperator::eq, '01572377');

        self::assertSame('{ company: { identificationNumber: { eq: "01572377" } } }', $this->encoder->encode($where));
    }

    public function testAndCombinator(): void
    {
        $where = Where::and(
            Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026),
            Where::field(CashJournalAttribute::identificationNumber, FilterOperator::neq, ''),
        );

        self::assertSame(
            '{ and: [{ year: { eq: 2026 } }, { identificationNumber: { neq: "" } }] }',
            $this->encoder->encode($where)
        );
    }

    public function testAllOfWithSingleCriterionDoesNotWrapInAnd(): void
    {
        $where = Where::allOf([[CashJournalAttribute::year, 2026]]);

        self::assertSame('{ year: { eq: 2026 } }', $this->encoder->encode($where));
    }
}
