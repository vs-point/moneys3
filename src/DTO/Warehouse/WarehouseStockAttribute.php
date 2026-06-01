<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Warehouse;

use VsPoint\MoneyS3\Filter\FieldName;

/**
 * Filterable / orderable fields of warehouse stock cards (`warehouseStocks`).
 *
 * Field names verified against the live schema (`IWarehouseStockFilterInput`).
 *
 * @see \VsPoint\MoneyS3\Agenda\WarehouseStockService
 */
enum WarehouseStockAttribute: string implements FieldName
{
    case id = 'id';
    case shortCut = 'shortCut';
    case description = 'description';
    case plu = 'plu';
    case catalogue = 'catalogue';
    case barCode = 'barCode';
    case lastChangeDate = 'lastChangeDate';
    case lastChangeTime = 'lastChangeTime';
    case articleCatalogue = 'article.catalogue';
    case warehouseCode = 'warehouse.code';
    case warehouseGroup = 'warehouseGroup.shortCut';

    public function graphQLName(): string
    {
        return $this->value;
    }
}
