<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Slip;

use Brick\Math\BigNumber;
use VsPoint\MoneyS3\DTO\Common\ArticleRef;
use VsPoint\MoneyS3\DTO\Common\WarehouseRef;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A stock-slip line item (warehouse received/issued slip).
 */
final readonly class SlipItem implements InputObject
{
    public function __construct(
        public ?WarehouseRef $warehouse = null,
        public int|float|string|BigNumber|null $unitPrice = null,
        public int|float|string|BigNumber|null $unitOfMeasure = null,
        public ?ArticleRef $article = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'warehouse' => $this->warehouse,
            'unitPrice' => $this->unitPrice,
            'unitOfMeasure' => $this->unitOfMeasure,
            'article' => $this->article,
        ];
    }
}
