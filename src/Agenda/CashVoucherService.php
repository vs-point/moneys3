<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Cash\CashVoucherInput;
use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;
use VsPoint\MoneyS3\Result\Collection;
use VsPoint\MoneyS3\Result\MutationResult;

/**
 * Cash documents (pokladní doklad). Supports read and create.
 */
final class CashVoucherService extends AbstractAgendaService
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
        return $this->queryRaw('cashVouchers', $fields, $where, $order, $skip, $take);
    }

    public function create(CashVoucherInput $cashVoucher, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('createCashVoucher', [
            'cashVoucher' => $cashVoucher,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function update(CashVoucherInput $cashVoucher, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('updateCashVoucher', [
            'cashVoucher' => $cashVoucher,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function delete(int $id, int $year): MutationResult
    {
        return $this->mutate('deleteCashVoucher', [
            'cashVoucher' => [
                'id' => $id,
                'year' => $year,
            ],
        ]);
    }
}
