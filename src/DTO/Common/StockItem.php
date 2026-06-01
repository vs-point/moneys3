<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * The stock binding of a document item (`stockItem`): the warehouse and the stock article.
 */
final readonly class StockItem implements InputObject
{
    public function __construct(
        public ?WarehouseRef $warehouse = null,
        public ?ArticleRef $article = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'warehouse' => $this->warehouse,
            'article' => $this->article,
        ];
    }
}
