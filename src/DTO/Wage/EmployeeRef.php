<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Wage;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Reference to an employee by personal number (`{ personalNumber: "00001" }`).
 */
final readonly class EmployeeRef implements InputObject
{
    public function __construct(
        public string $personalNumber,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'personalNumber' => $this->personalNumber,
        ];
    }
}
