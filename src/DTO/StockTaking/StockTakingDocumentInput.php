<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\StockTaking;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Strictly typed input for creating a stock-taking document (inventurní doklad).
 *
 * @see \VsPoint\MoneyS3\Agenda\StockTakingDocumentService
 */
final readonly class StockTakingDocumentInput implements InputObject
{
    /**
     * @param list<StockTakingItem> $items
     */
    public function __construct(
        public array $items,
        public ?string $description = null,
        public ?int $stockTakingDocumentId = null,
        public ?int $stockTakingId = null,
        public ?string $checkedByEmployee = null,
        public ?string $note = null,
        public ?string $guid = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'guid' => $this->guid,
            'description' => $this->description,
            'items' => $this->items,
            'stockTakingDocumentId' => $this->stockTakingDocumentId,
            'stockTakingId' => $this->stockTakingId,
            'checkedByEmployee' => $this->checkedByEmployee,
            'note' => $this->note,
        ];
    }
}
