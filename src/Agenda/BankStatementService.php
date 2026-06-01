<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Bank\BankStatementInput;
use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;
use VsPoint\MoneyS3\Result\Collection;
use VsPoint\MoneyS3\Result\MutationResult;

/**
 * Bank documents (bankovní doklad). Supports read and create.
 */
final class BankStatementService extends AbstractAgendaService
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
        return $this->queryRaw('bankStatements', $fields, $where, $order, $skip, $take);
    }

    public function create(BankStatementInput $bankStatement, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('createBankStatement', [
            'bankStatement' => $bankStatement,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function update(BankStatementInput $bankStatement, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('updateBankStatement', [
            'bankStatement' => $bankStatement,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function delete(int $id, int $year): MutationResult
    {
        return $this->mutate('deleteBankStatement', [
            'bankStatement' => [
                'id' => $id,
                'year' => $year,
            ],
        ]);
    }
}
