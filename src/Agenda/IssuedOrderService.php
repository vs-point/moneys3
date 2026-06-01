<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\DTO\Order\IssuedOrderInput;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;
use VsPoint\MoneyS3\Result\Collection;
use VsPoint\MoneyS3\Result\MutationResult;

/**
 * Issued orders (objednávka vystavená). Supports read and create.
 */
final class IssuedOrderService extends AbstractAgendaService
{
    /**
     * @param list<Field> $fields
     * @return Collection<Data>
     */
    public function query(
        array $fields,
        ?Where $where = null,
        ?Order $order = null,
        ?int $skip = null,
        ?int $take = null,
    ): Collection {
        return $this->queryRaw('issuedOrders', $fields, $where, $order, $skip, $take);
    }

    public function create(IssuedOrderInput $issuedOrder, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('createIssuedOrder', [
            'issuedOrder' => $issuedOrder,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function update(IssuedOrderInput $issuedOrder, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('updateIssuedOrder', [
            'issuedOrder' => $issuedOrder,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function delete(int $id, int $year): MutationResult
    {
        return $this->mutate('deleteIssuedOrder', [
            'issuedOrder' => [
                'id' => $id,
                'year' => $year,
            ],
        ]);
    }
}
