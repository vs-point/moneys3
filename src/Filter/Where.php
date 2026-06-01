<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Filter;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Composable, strictly typed `where` filter for collection queries.
 *
 * Fields are referenced through per-agenda {@see FieldName} attribute enums, so you cannot
 * filter on a field that does not exist:
 *
 *     Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026)
 *     Where::and(
 *         Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026),
 *         Where::field(CashJournalAttribute::companyIdentificationNumber, FilterOperator::eq, '01572377'),
 *     )
 *
 * Dotted field paths (e.g. `company.identificationNumber`) nest automatically into the
 * HotChocolate filter shape `{ company: { identificationNumber: { eq: … } } }`.
 *
 * {@see self::path()} is a raw escape hatch for fields not yet covered by an attribute enum.
 */
final class Where implements InputObject
{
    /**
     * @param array<string, mixed> $tree
     */
    private function __construct(
        private readonly array $tree,
    ) {
    }

    public static function field(FieldName $field, FilterOperator $operator, mixed $value): self
    {
        return self::buildPath($field->graphQLName(), $operator, $value);
    }

    /**
     * Raw field-path escape hatch — prefer {@see self::field()} with an attribute enum.
     */
    public static function path(string $path, FilterOperator $operator, mixed $value): self
    {
        return self::buildPath($path, $operator, $value);
    }

    public static function and(self $first, self ...$rest): self
    {
        return new self([
            'and' => array_map(static fn (self $w): array => $w->toGraphQL(), [$first, ...$rest]),
        ]);
    }

    public static function or(self $first, self ...$rest): self
    {
        return new self([
            'or' => array_map(static fn (self $w): array => $w->toGraphQL(), [$first, ...$rest]),
        ]);
    }

    /**
     * Build a conjunction from a list of typed equality criteria.
     *
     * @param array<int, array{FieldName, mixed}> $criteria field/value pairs compared with the given operator
     */
    public static function allOf(array $criteria, FilterOperator $operator = FilterOperator::eq): self
    {
        $conditions = [];
        foreach ($criteria as [$field, $value]) {
            $conditions[] = self::field($field, $operator, $value);
        }

        if ($conditions === []) {
            return new self([]);
        }

        return \count($conditions) === 1 ? $conditions[0] : self::and(...$conditions);
    }

    public function toGraphQL(): array
    {
        return $this->tree;
    }

    private static function buildPath(string $path, FilterOperator $operator, mixed $value): self
    {
        $condition = [
            $operator->value => $value,
        ];

        foreach (array_reverse(explode('.', $path)) as $segment) {
            $condition = [
                $segment => $condition,
            ];
        }

        return new self($condition);
    }
}
