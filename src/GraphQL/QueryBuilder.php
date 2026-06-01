<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\GraphQL;

use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;

/**
 * Assembles a Money S3 collection query of the shape:
 *
 *     query { <rootField>(where: {…}, order: {…}, skip: N, take: N) { items { …fields } } }
 *
 * The field selection is supplied as a strictly typed {@see Field} tree, and filtering
 * / ordering as typed {@see Where} / {@see Order} value objects.
 */
final class QueryBuilder
{
    public function __construct(
        private readonly ValueEncoder $encoder = new ValueEncoder(),
    ) {
    }

    /**
     * @param list<Field> $fields
     */
    public function build(
        string $rootField,
        array $fields,
        ?Where $where = null,
        ?Order $order = null,
        ?int $skip = null,
        ?int $take = null,
    ): string {
        $arguments = [];

        if ($where !== null) {
            $arguments[] = 'where: ' . $this->encoder->encode($where);
        }

        if ($order !== null) {
            $arguments[] = 'order: ' . $this->encoder->encode($order);
        }

        if ($skip !== null) {
            $arguments[] = 'skip: ' . $skip;
        }

        if ($take !== null) {
            $arguments[] = 'take: ' . $take;
        }

        $argumentList = $arguments === [] ? '' : '(' . implode(', ', $arguments) . ')';

        return 'query { ' . $rootField . $argumentList . ' { items { ' . Field::renderAll($fields) . ' } } }';
    }
}
