<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Warehouse;

use Brick\Math\BigDecimal;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;

/**
 * A warehouse stock card (skladová zásoba), as returned by the `warehouseStocks` query.
 */
final readonly class WarehouseStock
{
    public function __construct(
        public ?string $description,
        public ?string $catalogue,
        public ?string $barCode,
        public ?string $plu,
        public ?BigDecimal $stockQuantity,
        public ?BigDecimal $purchasePrice,
        public ?BigDecimal $purchaseVatRate,
        public ?BigDecimal $lastPurchasePrice,
        public ?BigDecimal $basicSalePrice,
        public ?string $articleGuid,
        public ?string $articleType,
        public ?string $warehouseCode,
        public ?string $warehouseName,
        public ?string $warehouseGuid,
    ) {
    }

    public static function fromData(Data $data): self
    {
        $article = $data->nested('article');
        $warehouse = $data->nested('warehouse');

        return new self(
            $data->nullableString('description'),
            $data->nullableString('catalogue'),
            $data->nullableString('barCode'),
            $data->nullableString('plu'),
            $data->nullableDecimal('stockQuantity'),
            $data->nullableDecimal('purchasePrice'),
            $data->nullableDecimal('purchaseVatRate'),
            $data->nullableDecimal('lastPurchasePrice'),
            $data->nested('definitionBasicSalePrice')?->nullableDecimal('price'),
            $article?->nullableString('guid'),
            $article?->nullableString('articleItemType'),
            $warehouse?->nullableString('code'),
            $warehouse?->nullableString('name'),
            $warehouse?->nullableString('guid'),
        );
    }

    /**
     * @return list<Field>
     */
    public static function fields(): array
    {
        return [
            Field::leaf('description'),
            Field::leaf('catalogue'),
            Field::leaf('barCode'),
            Field::leaf('plu'),
            Field::leaf('stockQuantity'),
            Field::leaf('purchasePrice'),
            Field::leaf('purchaseVatRate'),
            Field::leaf('lastPurchasePrice'),
            Field::nested('definitionBasicSalePrice', ['price']),
            Field::nested('article', ['guid', 'articleItemType', 'description', 'catalogue', 'barCode', 'plu']),
            Field::nested('warehouse', ['code', 'name', 'guid']),
        ];
    }
}
