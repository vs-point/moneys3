<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Agenda;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Agenda\IssuedInvoiceService;
use VsPoint\MoneyS3\DTO\Invoice\IssuedInvoiceInput;
use VsPoint\MoneyS3\Tests\Support\RecordingTransport;
use VsPoint\MoneyS3\Transport\GraphQLResponse;

#[CoversClass(IssuedInvoiceService::class)]
final class IssuedInvoiceServiceTest extends TestCase
{
    public function testCreateBuildsMutationAndParsesResult(): void
    {
        $transport = new RecordingTransport(
            new GraphQLResponse([
                'createIssuedInvoice' => [
                    'guid' => 'GUID-1',
                    'isSuccess' => true,
                ],
            ]),
        );
        $service = new IssuedInvoiceService($transport);

        $result = $service->create(new IssuedInvoiceInput(dateOfIssue: '2026-03-02', documentNumber: '20260203'));

        self::assertTrue($result->isSuccess);
        self::assertSame('GUID-1', $result->guid);
        self::assertNotNull($transport->lastDocument);
        self::assertStringContainsString('mutation { createIssuedInvoice(issuedInvoice: {', $transport->lastDocument);
        self::assertStringContainsString('documentNumber: "20260203"', $transport->lastDocument);
    }

    public function testDeleteBuildsKeyedMutation(): void
    {
        $transport = new RecordingTransport(
            new GraphQLResponse([
                'deleteIssuedInvoice' => [
                    'guid' => null,
                    'isSuccess' => true,
                ],
            ]),
        );
        $service = new IssuedInvoiceService($transport);

        $result = $service->delete(52, 2025);

        self::assertTrue($result->isSuccess);
        self::assertSame(
            'mutation { deleteIssuedInvoice(issuedInvoice: { id: 52, year: 2025 }) { guid isSuccess } }',
            $transport->lastDocument,
        );
    }
}
