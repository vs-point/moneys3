<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Wage;

use Brick\Math\BigNumber;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * One employment relationship (pracovní poměr) within a wage. An employee with multiple
 * relationships must list each one.
 */
final readonly class EmploymentRelationship implements InputObject
{
    /**
     * @param list<Absence> $absences
     */
    public function __construct(
        public int $id,
        public array $absences = [],
        public int|float|string|BigNumber|null $workedHours = null,
        public int|float|string|BigNumber|null $workingHours = null,
        public int|float|string|BigNumber|null $workingDays = null,
        public int|float|string|BigNumber|null $workedDays = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'id' => $this->id,
            'absences' => $this->absences === [] ? null : $this->absences,
            'workedHours' => $this->workedHours,
            'workingHours' => $this->workingHours,
            'workingDays' => $this->workingDays,
            'workedDays' => $this->workedDays,
        ];
    }
}
