<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\GraphQL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Filter\FilterOperator;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\OrderDirection;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\GraphQL\QueryBuilder;

#[CoversClass(QueryBuilder::class)]
final class QueryBuilderTest extends TestCase
{
    public function testMinimalQuery(): void
    {
        $document = (new QueryBuilder())->build('agendas', [Field::leaf('name'), Field::leaf('guid')]);

        self::assertSame('query { agendas { items { name guid } } }', $document);
    }

    public function testFullQueryWithArguments(): void
    {
        $document = (new QueryBuilder())->build(
            'journalTrs',
            [Field::leaf('id'), Field::leaf('year')],
            Where::path('year', FilterOperator::eq, 2026),
            Order::path('date', OrderDirection::desc),
            50,
            10,
        );

        self::assertSame(
            'query { journalTrs(where: { year: { eq: 2026 } }, order: { date: DESC }, skip: 50, take: 10) { items { id year } } }',
            $document,
        );
    }
}
