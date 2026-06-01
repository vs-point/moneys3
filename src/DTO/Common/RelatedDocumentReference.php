<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use Brick\DateTime\LocalDate;
use VsPoint\MoneyS3\Enum\DocumentType;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Identifies an existing document to be linked (e.g. settling a received order through
 * an issued invoice).
 */
final readonly class RelatedDocumentReference implements InputObject
{
    public function __construct(
        public DocumentType $documentType,
        public LocalDate|\DateTimeInterface|string|null $dateOfIssue = null,
        public ?string $variableSymbol = null,
        public ?string $documentNumber = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'documentType' => $this->documentType,
            'dateOfIssue' => $this->dateOfIssue,
            'variableSymbol' => $this->variableSymbol,
            'documentNumber' => $this->documentNumber,
        ];
    }
}
