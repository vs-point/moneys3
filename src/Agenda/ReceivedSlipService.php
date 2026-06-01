<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\DTO\Slip\ReceivedSlipInput;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;
use VsPoint\MoneyS3\Result\Collection;
use VsPoint\MoneyS3\Result\MutationResult;

/**
 * Warehouse received slips (skladová příjemka). Supports read, create, update and delete.
 */
final class ReceivedSlipService extends AbstractAgendaService
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
        return $this->queryRaw('receivedSlips', $fields, $where, $order, $skip, $take);
    }

    public function create(ReceivedSlipInput $receivedSlip, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('createReceivedSlip', [
            'receivedSlip' => $receivedSlip,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function update(ReceivedSlipInput $receivedSlip, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('updateReceivedSlip', [
            'receivedSlip' => $receivedSlip,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function delete(int $id, int $year): MutationResult
    {
        return $this->mutate('deleteReceivedSlip', [
            'receivedSlip' => [
                'id' => $id,
                'year' => $year,
            ],
        ]);
    }
}
