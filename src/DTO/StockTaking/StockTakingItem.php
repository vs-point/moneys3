<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\StockTaking;

use Brick\Math\BigNumber;
use VsPoint\MoneyS3\DTO\Common\ArticleRef;
use VsPoint\MoneyS3\DTO\Common\WarehouseRef;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A stock-taking document line item.
 */
final readonly class StockTakingItem implements InputObject
{
    public function __construct(
        public ArticleRef $article,
        public int|float|string|BigNumber|null $inventoryAmount = null,
        public ?WarehouseRef $warehouse = null,
        public ?ItemSerialNumber $itemSerialNumbers = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'article' => $this->article,
            'itemSerialNumbers' => $this->itemSerialNumbers,
            'inventoryAmount' => $this->inventoryAmount,
            'warehouse' => $this->warehouse,
        ];
    }
}
