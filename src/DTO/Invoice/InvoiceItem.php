<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Invoice;

use Brick\Math\BigNumber;
use VsPoint\MoneyS3\DTO\Common\StockItem;
use VsPoint\MoneyS3\Enum\PriceType;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * An invoice line item (issued or received invoice).
 *
 * A non-stock item carries `description` + price fields; a stock item additionally binds
 * to a warehouse article via {@see StockItem}.
 */
final readonly class InvoiceItem implements InputObject
{
    public function __construct(
        public ?string $description = null,
        public int|float|string|BigNumber|null $amount = null,
        public int|float|string|BigNumber|null $unitPriceHc = null,
        public int|float|string|BigNumber|null $vatRate = null,
        public ?PriceType $priceType = null,
        public ?StockItem $stockItem = null,
        public ?string $catalogue = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'unitPriceHc' => $this->unitPriceHc,
            'vatRate' => $this->vatRate,
            'priceType' => $this->priceType,
            'stockItem' => $this->stockItem,
            'catalogue' => $this->catalogue,
        ];
    }
}
