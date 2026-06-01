<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Hydration;

use Brick\DateTime\LocalDate;
use Brick\Math\BigDecimal;

/**
 * Typed, null-safe reader over a decoded GraphQL object.
 *
 * Keeps the `fromArray()` hydration of response DTOs concise and type-safe, so PHPStan
 * sees concrete scalar/object types rather than `mixed` array access.
 */
final readonly class Data
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        private array $values,
    ) {
    }

    public function string(string $key): string
    {
        $value = $this->values[$key] ?? null;

        return \is_string($value) ? $value : '';
    }

    public function nullableString(string $key): ?string
    {
        $value = $this->values[$key] ?? null;

        return \is_string($value) ? $value : null;
    }

    public function int(string $key): int
    {
        $value = $this->values[$key] ?? null;

        return \is_int($value) ? $value : (int) (\is_numeric($value) ? $value : 0);
    }

    public function nullableInt(string $key): ?int
    {
        $value = $this->values[$key] ?? null;

        if ($value === null) {
            return null;
        }

        return \is_int($value) ? $value : (int) (\is_numeric($value) ? $value : 0);
    }

    public function bool(string $key): bool
    {
        return ($this->values[$key] ?? null) === true;
    }

    public function nullableDecimal(string $key): ?BigDecimal
    {
        $value = $this->values[$key] ?? null;

        if ($value === null || (\is_string($value) && $value === '')) {
            return null;
        }

        if (\is_int($value) || \is_float($value) || \is_string($value)) {
            return BigDecimal::of($value);
        }

        return null;
    }

    public function nullableLocalDate(string $key): ?LocalDate
    {
        $value = $this->values[$key] ?? null;
        if (!\is_string($value) || $value === '') {
            return null;
        }

        // Money S3 returns dates either as YYYY-MM-DD or with a time component.
        return LocalDate::parse(substr($value, 0, 10));
    }

    /**
     * A nested object as a child {@see Data}, or null when absent.
     */
    public function nested(string $key): ?self
    {
        $value = $this->values[$key] ?? null;
        if (!\is_array($value) || array_is_list($value)) {
            return null;
        }

        /** @var array<string, mixed> $value */
        return new self($value);
    }

    /**
     * A list of nested objects mapped through the given factory.
     *
     * @template T
     * @param callable(self): T $factory
     * @return list<T>
     */
    public function listOf(string $key, callable $factory): array
    {
        $value = $this->values[$key] ?? null;
        if (!\is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (\is_array($item)) {
                /** @var array<string, mixed> $item */
                $result[] = $factory(new self($item));
            }
        }

        return $result;
    }
}
