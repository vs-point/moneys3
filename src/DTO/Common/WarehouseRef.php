<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Reference to a warehouse (sklad) by its code (EAN), name and/or GUID.
 */
final readonly class WarehouseRef implements InputObject
{
    public function __construct(
        public ?string $code = null,
        public ?string $name = null,
        public ?string $guid = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'guid' => $this->guid,
        ];
    }
}
