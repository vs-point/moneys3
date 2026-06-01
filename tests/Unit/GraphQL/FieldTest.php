<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\GraphQL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\GraphQL\Field;

#[CoversClass(Field::class)]
final class FieldTest extends TestCase
{
    public function testLeafRendersName(): void
    {
        self::assertSame('documentNumber', Field::leaf('documentNumber')->render());
    }

    public function testNestedRenders(): void
    {
        $field = Field::nested('company', ['identificationNumber', Field::nested('deliveryAddress', ['name'])]);

        self::assertSame('company { identificationNumber deliveryAddress { name } }', $field->render());
    }

    public function testRenderAll(): void
    {
        self::assertSame('id year', Field::renderAll([Field::leaf('id'), Field::leaf('year')]));
    }
}
