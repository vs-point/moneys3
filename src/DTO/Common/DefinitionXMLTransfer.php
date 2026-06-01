<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * The import (XML transfer) definition selecting how Money S3 processes the document
 * from the import queue, e.g. `{ shortCut: "_FP+FV" }`.
 */
final readonly class DefinitionXMLTransfer implements InputObject
{
    public function __construct(
        public string $shortCut,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'shortCut' => $this->shortCut,
        ];
    }
}
