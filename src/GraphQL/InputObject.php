<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\GraphQL;

/**
 * A strictly typed object that can be serialized into a GraphQL input object literal.
 *
 * Implementations return a map of GraphQL field name => value. Values may be scalars,
 * native enums (rendered as bare GraphQL enum tokens), {@see GraphQLEnumValue},
 * other {@see InputObject}s, brick numbers/dates, {@see RawGraphQL} or lists thereof.
 *
 * Fields whose value is `null` are omitted from the rendered literal, so optional
 * arguments simply stay unset.
 */
interface InputObject
{
    /**
     * @return array<string, mixed>
     */
    public function toGraphQL(): array;
}
