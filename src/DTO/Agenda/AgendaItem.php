<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Agenda;

use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;

/**
 * An agenda (accounting unit / company) as returned by the `agendas` query.
 */
final readonly class AgendaItem
{
    public function __construct(
        public ?string $name,
        public ?string $guid,
    ) {
    }

    public static function fromData(Data $data): self
    {
        return new self($data->nullableString('name'), $data->nullableString('guid'));
    }

    /**
     * @return list<Field>
     */
    public static function fields(): array
    {
        return [Field::leaf('name'), Field::leaf('guid')];
    }
}
