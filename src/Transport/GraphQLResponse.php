<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Transport;

use VsPoint\MoneyS3\Exception\GraphQLException;
use VsPoint\MoneyS3\Exception\UnexpectedResponseException;

/**
 * A decoded GraphQL response: the `data` object and any top-level `errors`.
 */
final readonly class GraphQLResponse
{
    /**
     * @param array<string, mixed>       $data
     * @param list<array<string, mixed>> $errors
     */
    public function __construct(
        public array $data,
        public array $errors = [],
    ) {
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    public function throwOnError(): self
    {
        if ($this->hasErrors()) {
            throw GraphQLException::fromErrors($this->errors);
        }

        return $this;
    }

    /**
     * Return the value of a single top-level field of `data` as an array.
     *
     * @return array<string, mixed>
     */
    public function field(string $name): array
    {
        $value = $this->data[$name] ?? null;
        if (!\is_array($value)) {
            throw new UnexpectedResponseException(\sprintf('GraphQL response is missing the "%s" field.', $name));
        }

        /** @var array<string, mixed> $value */
        return $value;
    }
}
