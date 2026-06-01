<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Wage;

use Brick\DateTime\LocalDate;
use Brick\Math\BigNumber;
use VsPoint\MoneyS3\Enum\AbsenceType;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * An absence record within an employment relationship of a wage.
 */
final readonly class Absence implements InputObject
{
    public function __construct(
        public LocalDate|\DateTimeInterface|string $dateOfStart,
        public LocalDate|\DateTimeInterface|string $dateOfEnd,
        public ?AbsenceType $type = null,
        public ?string $name = null,
        public int|float|string|BigNumber|null $workingDays = null,
        public int|float|string|BigNumber|null $workingHours = null,
        public int|float|string|BigNumber|null $calendarDays = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'dateOfStart' => $this->dateOfStart,
            'dateOfEnd' => $this->dateOfEnd,
            'workingDays' => $this->workingDays,
            'workingHours' => $this->workingHours,
            'calendarDays' => $this->calendarDays,
            'type' => $this->type,
            'name' => $this->name,
        ];
    }
}
