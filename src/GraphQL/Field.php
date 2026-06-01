<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\GraphQL;

/**
 * A single node in a GraphQL selection set — either a scalar leaf (`documentNumber`)
 * or a nested object selection (`company { identificationNumber }`).
 *
 * Selections are composed as immutable trees, which keeps the requested shape and the
 * response DTO shape aligned at the type level.
 */
final class Field
{
    /**
     * @var list<self>
     */
    public readonly array $children;

    /**
     * @param list<self|string> $children
     */
    public function __construct(
        public readonly string $name,
        array $children = [],
    ) {
        $this->children = array_map(
            static fn (self|string $child): self => $child instanceof self ? $child : new self($child),
            $children,
        );
    }

    public static function leaf(string $name): self
    {
        return new self($name);
    }

    /**
     * @param list<self|string> $children
     */
    public static function nested(string $name, array $children): self
    {
        return new self($name, $children);
    }

    public function render(): string
    {
        if ($this->children === []) {
            return $this->name;
        }

        $children = implode(' ', array_map(static fn (self $child): string => $child->render(), $this->children));

        return $this->name . ' { ' . $children . ' }';
    }

    /**
     * @param list<self> $fields
     */
    public static function renderAll(array $fields): string
    {
        return implode(' ', array_map(static fn (self $field): string => $field->render(), $fields));
    }
}
