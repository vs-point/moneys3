<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A reference to a list/code-book record identified by its short cut, e.g. `{ shortCut: "BAN" }`.
 *
 * Used for account assignments (předkontace), VAT classifications, "pay on/from" methods,
 * centres, job orders, operations, cash boxes, shipping types, etc.
 */
final readonly class ShortCutRef implements InputObject
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
