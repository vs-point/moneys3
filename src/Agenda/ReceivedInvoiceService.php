<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\DTO\Invoice\ReceivedInvoiceInput;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;
use VsPoint\MoneyS3\Result\Collection;
use VsPoint\MoneyS3\Result\MutationResult;

/**
 * Received invoices (faktura přijatá). Supports read, create and delete.
 */
final class ReceivedInvoiceService extends AbstractAgendaService
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
        return $this->queryRaw('receivedInvoices', $fields, $where, $order, $skip, $take);
    }

    public function create(ReceivedInvoiceInput $invoice, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('createReceivedInvoice', [
            'receivedInvoice' => $invoice,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function update(ReceivedInvoiceInput $invoice, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('updateReceivedInvoice', [
            'receivedInvoice' => $invoice,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function delete(int $id, int $year): MutationResult
    {
        return $this->mutate('deleteReceivedInvoice', [
            'receivedInvoice' => [
                'id' => $id,
                'year' => $year,
            ],
        ]);
    }
}
