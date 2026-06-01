<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A single entry of a document's `relatedDocuments` list, wrapping a {@see RelatedDocumentReference}.
 */
final readonly class RelatedDocument implements InputObject
{
    public function __construct(
        public RelatedDocumentReference $document,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'document' => $this->document,
        ];
    }
}
