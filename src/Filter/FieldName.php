<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Filter;

/**
 * A strictly typed reference to a queryable/filterable field of a specific agenda.
 *
 * Implemented by per-agenda attribute enums (e.g. {@see \VsPoint\MoneyS3\DTO\Journal\CashJournalAttribute}),
 * so a field can only be referenced through a real, existing enum case — never a free-form
 * string that could contain a typo or a non-existent field.
 *
 * The returned name is a GraphQL field path; a dotted path such as
 * `company.identificationNumber` is expanded into the nested filter/order object.
 */
interface FieldName
{
    public function graphQLName(): string;
}
