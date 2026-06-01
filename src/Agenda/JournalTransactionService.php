<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Journal\JournalTransaction;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Result\Collection;

/**
 * Cash journal (peněžní deník). Read-only — exposed through the `journalTrs` query.
 */
final class JournalTransactionService extends AbstractAgendaService
{
    /**
     * @param list<Field>|null $fields custom selection; defaults to {@see JournalTransaction::fields()}
     * @return Collection<JournalTransaction>
     */
    public function query(
        ?Where $where = null,
        ?Order $order = null,
        ?int $skip = null,
        ?int $take = null,
        ?array $fields = null,
    ): Collection {
        return $this->queryCollection(
            'journalTrs',
            $fields ?? JournalTransaction::fields(),
            JournalTransaction::fromData(...),
            $where,
            $order,
            $skip,
            $take,
        );
    }
}
