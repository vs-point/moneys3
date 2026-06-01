<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Slip;

use Brick\DateTime\LocalDate;
use VsPoint\MoneyS3\DTO\Common\PartnerAddress;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Strictly typed input for creating a warehouse issued slip (skladová výdejka).
 *
 * @see \VsPoint\MoneyS3\Agenda\IssuedSlipService
 */
final readonly class IssuedSlipInput implements InputObject
{
    /**
     * @param list<SlipItem> $items
     */
    public function __construct(
        public ?string $documentNumber = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfIssue = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfStockMovement = null,
        public ?string $variableSymbol = null,
        public ?PartnerAddress $partnerAddress = null,
        public array $items = [],
        public ?string $guid = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'guid' => $this->guid,
            'documentNumber' => $this->documentNumber,
            'dateOfIssue' => $this->dateOfIssue,
            'dateOfStockMovement' => $this->dateOfStockMovement,
            'variableSymbol' => $this->variableSymbol,
            'partnerAddress' => $this->partnerAddress,
            'items' => $this->items === [] ? null : $this->items,
        ];
    }
}
