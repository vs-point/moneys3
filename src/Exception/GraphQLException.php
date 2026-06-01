<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Exception;

/**
 * Thrown when the GraphQL response carries a top-level `errors` array.
 */
final class GraphQLException extends MoneyS3Exception
{
    /**
     * @param list<array<string, mixed>> $errors
     */
    public function __construct(
        string $message,
        private readonly array $errors = [],
    ) {
        parent::__construct($message);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param list<array<string, mixed>> $errors
     */
    public static function fromErrors(array $errors): self
    {
        $messages = array_map(
            static fn (array $error): string => \is_string(
                $error['message'] ?? null
            ) ? $error['message'] : 'Unknown GraphQL error',
            $errors,
        );

        return new self(implode('; ', $messages) ?: 'GraphQL request failed.', $errors);
    }
}
