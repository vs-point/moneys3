<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Agenda;

use Brick\Math\BigDecimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Agenda\JournalTransactionService;
use VsPoint\MoneyS3\DTO\Journal\CashJournalAttribute;
use VsPoint\MoneyS3\DTO\Journal\JournalTransaction;
use VsPoint\MoneyS3\Filter\FilterOperator;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\Tests\Support\RecordingTransport;
use VsPoint\MoneyS3\Transport\GraphQLResponse;

#[CoversClass(JournalTransactionService::class)]
#[CoversClass(JournalTransaction::class)]
final class JournalTransactionServiceTest extends TestCase
{
    public function testQueryHydratesTypedDtos(): void
    {
        $transport = new RecordingTransport(new GraphQLResponse([
            'journalTrs' => [
                'items' => [
                    [
                        'id' => 7,
                        'year' => 2026,
                        'isDeleted' => false,
                        'date' => '2026-03-15',
                        'description' => 'Platba',
                        'accountMovement' => [
                            'shortCut' => 'BV',
                        ],
                        'amountHc' => '1234.50',
                        'currency' => [
                            'code' => 'CZK',
                        ],
                        'company' => [
                            'identificationNumber' => '01572377',
                            'deliveryAddress' => [
                                'name' => 'Seyfor, a. s.',
                            ],
                        ],
                        'centre' => [
                            'shortCut' => 'STR',
                        ],
                    ],
                ],
            ],
        ]));
        $service = new JournalTransactionService($transport);

        $result = $service->query(Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026), take: 10);

        self::assertCount(1, $result);
        $row = $result->first();
        self::assertInstanceOf(JournalTransaction::class, $row);
        self::assertSame(7, $row->id);
        self::assertSame('Platba', $row->description);
        self::assertSame('BV', $row->accountMovementShortCut);
        self::assertTrue(BigDecimal::of('1234.50')->isEqualTo($row->amountHc ?? BigDecimal::zero()));
        self::assertSame('CZK', $row->currencyCode);
        self::assertSame('01572377', $row->companyIdentificationNumber);
        self::assertSame('Seyfor, a. s.', $row->companyName);
        self::assertSame('STR', $row->centreShortCut);

        self::assertNotNull($transport->lastDocument);
        self::assertStringContainsString(
            'query { journalTrs(where: { year: { eq: 2026 } }, take: 10)',
            $transport->lastDocument
        );
    }
}
