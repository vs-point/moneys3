<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Exception\MutationFailedException;
use VsPoint\MoneyS3\Result\MutationResult;

#[CoversClass(MutationResult::class)]
final class MutationResultTest extends TestCase
{
    public function testFromArraySuccess(): void
    {
        $result = MutationResult::fromArray([
            'guid' => 'ABC',
            'isSuccess' => true,
        ]);

        self::assertTrue($result->isSuccess);
        self::assertSame('ABC', $result->guid);
        self::assertSame($result, $result->assertSuccess());
    }

    public function testFromArrayFailureDefaults(): void
    {
        $result = MutationResult::fromArray([]);

        self::assertFalse($result->isSuccess);
        self::assertNull($result->guid);
    }

    public function testAssertSuccessThrows(): void
    {
        $this->expectException(MutationFailedException::class);

        MutationResult::fromArray([
            'isSuccess' => false,
            'guid' => 'X',
        ])->assertSuccess();
    }
}
