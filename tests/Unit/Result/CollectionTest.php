<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Result\Collection;

#[CoversClass(Collection::class)]
final class CollectionTest extends TestCase
{
    public function testAccessors(): void
    {
        $collection = new Collection(['a', 'b', 'c']);

        self::assertCount(3, $collection);
        self::assertSame('a', $collection->first());
        self::assertFalse($collection->isEmpty());
        self::assertSame(['a', 'b', 'c'], iterator_to_array($collection));
    }

    public function testEmpty(): void
    {
        $collection = new Collection([]);

        self::assertTrue($collection->isEmpty());
        self::assertNull($collection->first());
    }
}
