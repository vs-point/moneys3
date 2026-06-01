<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\StockTaking;

use Brick\DateTime\LocalDate;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A serial number entry on a stock-taking item (`itemSerialNumbers`).
 */
final readonly class ItemSerialNumber implements InputObject
{
    public function __construct(
        public ?string $description = null,
        public ?string $barCode = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfProduction = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'barCode' => $this->barCode,
            'description' => $this->description,
            'dateOfProduction' => $this->dateOfProduction,
        ];
    }
}
