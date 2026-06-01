<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Order;

use Brick\Math\BigNumber;
use VsPoint\MoneyS3\DTO\Common\ArticleRef;
use VsPoint\MoneyS3\DTO\Common\WarehouseRef;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * An order line item (received or issued order).
 *
 * A stock item binds to an {@see ArticleRef} + {@see WarehouseRef}; a non-stock item is
 * identified by `description` and must carry a `catalogue` so Money S3 treats it as non-stock.
 */
final readonly class OrderItem implements InputObject
{
    public function __construct(
        public ?string $description = null,
        public int|float|string|BigNumber|null $amount = null,
        public int|float|string|BigNumber|null $unitPriceHc = null,
        public int|float|string|BigNumber|null $vatRate = null,
        public ?ArticleRef $article = null,
        public ?WarehouseRef $warehouse = null,
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
            'article' => $this->article,
            'warehouse' => $this->warehouse,
            'catalogue' => $this->catalogue,
        ];
    }
}
