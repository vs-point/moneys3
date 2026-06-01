<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Agenda\AgendaItem;
use VsPoint\MoneyS3\Result\Collection;

/**
 * Agendas / accounting units (`agendas`). Read-only.
 */
final class AgendaListService extends AbstractAgendaService
{
    /**
     * @return Collection<AgendaItem>
     */
    public function query(?int $skip = null, ?int $take = null): Collection
    {
        return $this->queryCollection(
            'agendas',
            AgendaItem::fields(),
            AgendaItem::fromData(...),
            null,
            null,
            $skip,
            $take,
        );
    }
}
