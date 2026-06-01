<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\GraphQL;

use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\Math\BigNumber;
use UnitEnum;
use VsPoint\MoneyS3\Exception\EncodingException;

/**
 * Encodes strictly typed PHP values into GraphQL literal syntax.
 *
 * Unlike JSON, GraphQL object keys and enum values are bare identifiers; this encoder
 * produces valid GraphQL document fragments (not JSON), which is what allows the typed
 * {@see InputObject} DTOs to flow straight into a query/mutation document.
 */
final class ValueEncoder
{
    public function encode(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (\is_int($value)) {
            return (string) $value;
        }

        if (\is_float($value)) {
            return $this->encodeFloat($value);
        }

        if (\is_string($value)) {
            return $this->encodeString($value);
        }

        if ($value instanceof GraphQLEnumValue) {
            return $value->graphQLValue();
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        if ($value instanceof RawGraphQL) {
            return $value->graphql;
        }

        if ($value instanceof BigNumber) {
            return (string) $value;
        }

        if ($value instanceof LocalDate || $value instanceof LocalDateTime) {
            return $this->encodeString((string) $value);
        }

        if ($value instanceof \DateTimeInterface) {
            return $this->encodeString($value->format('Y-m-d'));
        }

        if ($value instanceof InputObject) {
            return $this->encodeObject($value->toGraphQL());
        }

        if (\is_array($value)) {
            return array_is_list($value) ? $this->encodeList($value) : $this->encodeObject($value);
        }

        throw new EncodingException(\sprintf(
            'Cannot encode value of type "%s" into a GraphQL literal.',
            get_debug_type($value)
        ));
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function encodeObject(array $fields): string
    {
        $parts = [];
        foreach ($fields as $key => $value) {
            if ($value === null) {
                continue;
            }

            $parts[] = $key . ': ' . $this->encode($value);
        }

        return '{ ' . implode(', ', $parts) . ' }';
    }

    /**
     * @param list<mixed> $items
     */
    public function encodeList(array $items): string
    {
        return '[' . implode(', ', array_map(fn (mixed $item): string => $this->encode($item), $items)) . ']';
    }

    private function encodeString(string $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private function encodeFloat(float $value): string
    {
        $formatted = rtrim(rtrim(sprintf('%.10F', $value), '0'), '.');

        return $formatted === '' || $formatted === '-' ? '0' : $formatted;
    }
}
