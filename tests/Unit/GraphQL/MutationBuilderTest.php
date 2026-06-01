<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\GraphQL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\GraphQL\MutationBuilder;

#[CoversClass(MutationBuilder::class)]
final class MutationBuilderTest extends TestCase
{
    public function testDeleteMutation(): void
    {
        $document = (new MutationBuilder())->build('deleteIssuedInvoice', [
            'issuedInvoice' => [
                'id' => 52,
                'year' => 2025,
            ],
        ]);

        self::assertSame(
            'mutation { deleteIssuedInvoice(issuedInvoice: { id: 52, year: 2025 }) { guid isSuccess } }',
            $document,
        );
    }

    public function testNullArgumentsAreDropped(): void
    {
        $document = (new MutationBuilder())->build('createWage', [
            'wage' => [
                'year' => 2026,
            ],
            'definitionXMLTransfer' => null,
        ]);

        self::assertSame('mutation { createWage(wage: { year: 2026 }) { guid isSuccess } }', $document);
    }
}
