<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Journal\JournalEntry;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Result\Collection;

/**
 * Accounting journal (účetní deník). Read-only — exposed through the `journalAccs` query.
 */
final class JournalEntryService extends AbstractAgendaService
{
    /**
     * @param list<Field>|null $fields custom selection; defaults to {@see JournalEntry::fields()}
     * @return Collection<JournalEntry>
     */
    public function query(
        ?Where $where = null,
        ?Order $order = null,
        ?int $skip = null,
        ?int $take = null,
        ?array $fields = null,
    ): Collection {
        return $this->queryCollection(
            'journalAccs',
            $fields ?? JournalEntry::fields(),
            JournalEntry::fromData(...),
            $where,
            $order,
            $skip,
            $take,
        );
    }
}
