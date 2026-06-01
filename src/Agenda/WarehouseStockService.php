<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Warehouse\WarehouseStock;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Result\Collection;

/**
 * Warehouse stock cards (skladová zásoba). Read-only — exposed through `warehouseStocks`.
 */
final class WarehouseStockService extends AbstractAgendaService
{
    /**
     * @param list<Field>|null $fields custom selection; defaults to {@see WarehouseStock::fields()}
     * @return Collection<WarehouseStock>
     */
    public function query(
        ?Where $where = null,
        ?Order $order = null,
        ?int $skip = null,
        ?int $take = null,
        ?array $fields = null,
    ): Collection {
        return $this->queryCollection(
            'warehouseStocks',
            $fields ?? WarehouseStock::fields(),
            WarehouseStock::fromData(...),
            $where,
            $order,
            $skip,
            $take,
        );
    }
}
