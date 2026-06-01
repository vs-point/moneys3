<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Filter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\DTO\Journal\CashJournalAttribute;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\OrderDirection;
use VsPoint\MoneyS3\GraphQL\ValueEncoder;

#[CoversClass(Order::class)]
final class OrderTest extends TestCase
{
    public function testSingleTypedKey(): void
    {
        $order = Order::by(CashJournalAttribute::date, OrderDirection::desc);

        self::assertSame('{ date: DESC }', (new ValueEncoder())->encode($order));
    }

    public function testMultipleKeysPreserveOrder(): void
    {
        $order = Order::by(CashJournalAttribute::year, OrderDirection::desc)
            ->thenBy(CashJournalAttribute::date, OrderDirection::asc);

        self::assertSame('{ year: DESC, date: ASC }', (new ValueEncoder())->encode($order));
    }

    public function testDottedPathNests(): void
    {
        $order = Order::by(CashJournalAttribute::companyIdentificationNumber, OrderDirection::asc);

        self::assertSame('{ company: { identificationNumber: ASC } }', (new ValueEncoder())->encode($order));
    }

    public function testRawPathEscapeHatch(): void
    {
        $order = Order::path('date', OrderDirection::desc);

        self::assertSame('{ date: DESC }', (new ValueEncoder())->encode($order));
    }
}
