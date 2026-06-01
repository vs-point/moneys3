<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Filter;

/**
 * Comparison operators supported by the Money S3 GraphQL `where` filters
 * (HotChocolate filtering convention).
 */
enum FilterOperator: string
{
    case eq = 'eq';
    case neq = 'neq';
    case gt = 'gt';
    case gte = 'gte';
    case lt = 'lt';
    case lte = 'lte';
    case in = 'in';
    case nin = 'nin';
    case contains = 'contains';
    case ncontains = 'ncontains';
    case startsWith = 'startsWith';
    case nstartsWith = 'nstartsWith';
    case endsWith = 'endsWith';
    case nendsWith = 'nendsWith';
}
