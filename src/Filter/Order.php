<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Filter;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Strictly typed `order` argument, e.g. `order: { date: DESC }`.
 *
 * Sort keys are referenced through per-agenda {@see FieldName} attribute enums. Dotted
 * paths nest (`{ company: { name: DESC } }`); multiple keys keep their insertion order.
 */
final class Order implements InputObject
{
    /**
     * @param list<array{string, OrderDirection}> $keys
     */
    private function __construct(
        private readonly array $keys,
    ) {
    }

    public static function by(FieldName $field, OrderDirection $direction = OrderDirection::asc): self
    {
        return new self([[$field->graphQLName(), $direction]]);
    }

    /**
     * Raw field-path escape hatch — prefer {@see self::by()} with an attribute enum.
     */
    public static function path(string $path, OrderDirection $direction = OrderDirection::asc): self
    {
        return new self([[$path, $direction]]);
    }

    public function thenBy(FieldName $field, OrderDirection $direction = OrderDirection::asc): self
    {
        return new self([...$this->keys, [$field->graphQLName(), $direction]]);
    }

    public function thenByPath(string $path, OrderDirection $direction = OrderDirection::asc): self
    {
        return new self([...$this->keys, [$path, $direction]]);
    }

    public function toGraphQL(): array
    {
        $tree = [];
        foreach ($this->keys as [$path, $direction]) {
            $node = $direction;
            foreach (array_reverse(explode('.', $path)) as $segment) {
                $node = [
                    $segment => $node,
                ];
            }

            /** @var array<string, mixed> $node */
            $tree = array_replace_recursive($tree, $node);
        }

        return $tree;
    }
}
