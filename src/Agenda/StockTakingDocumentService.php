<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\DTO\StockTaking\StockTakingDocumentInput;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;
use VsPoint\MoneyS3\Result\Collection;
use VsPoint\MoneyS3\Result\MutationResult;

/**
 * Stock-taking documents (inventurní doklad). Supports read, create, update and delete.
 */
final class StockTakingDocumentService extends AbstractAgendaService
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
        return $this->queryRaw('stockTakingDocuments', $fields, $where, $order, $skip, $take);
    }

    public function create(
        StockTakingDocumentInput $stockTakingDocument,
        ?DefinitionXMLTransfer $definition = null
    ): MutationResult {
        return $this->mutate('createStockTakingDocument', [
            'stockTakingDocument' => $stockTakingDocument,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function update(
        StockTakingDocumentInput $stockTakingDocument,
        ?DefinitionXMLTransfer $definition = null
    ): MutationResult {
        return $this->mutate('updateStockTakingDocument', [
            'stockTakingDocument' => $stockTakingDocument,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function delete(int $id, int $year): MutationResult
    {
        return $this->mutate('deleteStockTakingDocument', [
            'stockTakingDocument' => [
                'id' => $id,
                'year' => $year,
            ],
        ]);
    }
}
