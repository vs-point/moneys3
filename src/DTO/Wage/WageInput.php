<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Wage;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Strictly typed input for creating a wage (mzda).
 *
 * @see \VsPoint\MoneyS3\Agenda\WageService
 */
final readonly class WageInput implements InputObject
{
    /**
     * @param list<EmploymentRelationship> $employmentRelationships
     */
    public function __construct(
        public EmployeeRef $employee,
        public int $month,
        public int $year,
        public array $employmentRelationships = [],
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'employee' => $this->employee,
            'month' => $this->month,
            'year' => $this->year,
            'employmentRelationships' => $this->employmentRelationships === [] ? null : $this->employmentRelationships,
        ];
    }
}
