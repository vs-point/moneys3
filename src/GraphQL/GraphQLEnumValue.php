<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\GraphQL;

/**
 * Marker for enums whose rendered GraphQL token differs from their PHP case name.
 *
 * A backed enum that implements this interface controls exactly which bare token is
 * emitted into the GraphQL document (e.g. `DESC`, `WITHOUT_VAT`). Enums that do not
 * implement it are rendered using their case name.
 */
interface GraphQLEnumValue
{
    public function graphQLValue(): string;
}
