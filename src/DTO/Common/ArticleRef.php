<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Reference to a stock article (kmenová karta) by catalogue number and/or GUID.
 */
final readonly class ArticleRef implements InputObject
{
    public function __construct(
        public ?string $catalogue = null,
        public ?string $guid = null,
        public ?string $barCode = null,
        public ?bool $isRecordSerialNumber = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'catalogue' => $this->catalogue,
            'guid' => $this->guid,
            'barCode' => $this->barCode,
            'isRecordSerialNumber' => $this->isRecordSerialNumber,
        ];
    }
}
