<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\GraphQL;

use Brick\DateTime\LocalDate;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\DTO\Common\ShortCutRef;
use VsPoint\MoneyS3\Enum\PriceType;
use VsPoint\MoneyS3\GraphQL\RawGraphQL;
use VsPoint\MoneyS3\GraphQL\ValueEncoder;

#[CoversClass(ValueEncoder::class)]
final class ValueEncoderTest extends TestCase
{
    private ValueEncoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new ValueEncoder();
    }

    public function testScalars(): void
    {
        self::assertSame('null', $this->encoder->encode(null));
        self::assertSame('true', $this->encoder->encode(true));
        self::assertSame('false', $this->encoder->encode(false));
        self::assertSame('42', $this->encoder->encode(42));
        self::assertSame('3.14', $this->encoder->encode(3.14));
        self::assertSame('100', $this->encoder->encode(100.0));
    }

    public function testStringsAreQuotedAndEscaped(): void
    {
        self::assertSame('"hello"', $this->encoder->encode('hello'));
        self::assertSame('"say \"hi\""', $this->encoder->encode('say "hi"'));
        self::assertSame('"Příliš"', $this->encoder->encode('Příliš'));
    }

    public function testEnumsRenderAsBareTokens(): void
    {
        self::assertSame('WITHOUT_VAT', $this->encoder->encode(PriceType::withoutVat));
    }

    public function testBrickTypes(): void
    {
        self::assertSame('123.45', $this->encoder->encode(BigDecimal::of('123.45')));
        self::assertSame('"2026-03-02"', $this->encoder->encode(LocalDate::of(2026, 3, 2)));
    }

    public function testRawGraphQLPassesThrough(): void
    {
        self::assertSame('SOME_TOKEN', $this->encoder->encode(new RawGraphQL('SOME_TOKEN')));
    }

    public function testList(): void
    {
        self::assertSame('[1, 2, 3]', $this->encoder->encode([1, 2, 3]));
    }

    public function testInputObjectOmitsNullFields(): void
    {
        $address = new ShortCutRef('BAN');
        self::assertSame('{ shortCut: "BAN" }', $this->encoder->encode($address));
    }

    public function testNestedObjectsAndNullOmission(): void
    {
        $encoded = $this->encoder->encode([
            'name' => 'Seyfor',
            'skipped' => null,
            'nested' => [
                'code' => 'CZ',
            ],
        ]);

        self::assertSame('{ name: "Seyfor", nested: { code: "CZ" } }', $encoded);
    }
}
