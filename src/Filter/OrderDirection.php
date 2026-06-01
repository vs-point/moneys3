<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Filter;

use VsPoint\MoneyS3\GraphQL\GraphQLEnumValue;

enum OrderDirection: string implements GraphQLEnumValue
{
    case asc = 'ASC';
    case desc = 'DESC';

    public function graphQLValue(): string
    {
        return $this->value;
    }
}
