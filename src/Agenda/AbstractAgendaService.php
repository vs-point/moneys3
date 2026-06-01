<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\Where;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\GraphQL\MutationBuilder;
use VsPoint\MoneyS3\GraphQL\QueryBuilder;
use VsPoint\MoneyS3\Hydration\Data;
use VsPoint\MoneyS3\Result\Collection;
use VsPoint\MoneyS3\Result\MutationResult;
use VsPoint\MoneyS3\Transport\Transport;

/**
 * Base class for every agenda service. Provides the three building blocks shared by all
 * agendas: collection queries, write mutations, and delete mutations — each rendered as a
 * strictly typed GraphQL document and executed over the {@see Transport}.
 */
abstract class AbstractAgendaService
{
    public function __construct(
        protected readonly Transport $transport,
        protected readonly QueryBuilder $queryBuilder = new QueryBuilder(),
        protected readonly MutationBuilder $mutationBuilder = new MutationBuilder(),
    ) {
    }

    /**
     * Run a collection query and hydrate each returned item through the given factory.
     *
     * @template T
     * @param list<Field>       $fields
     * @param callable(Data): T $factory
     * @return Collection<T>
     */
    protected function queryCollection(
        string $rootField,
        array $fields,
        callable $factory,
        ?Where $where = null,
        ?Order $order = null,
        ?int $skip = null,
        ?int $take = null,
    ): Collection {
        $document = $this->queryBuilder->build($rootField, $fields, $where, $order, $skip, $take);
        $response = $this->transport->execute($document)
            ->throwOnError();

        $root = $response->field($rootField);
        $items = \is_array($root['items'] ?? null) ? $root['items'] : [];

        $hydrated = [];
        foreach ($items as $item) {
            if (\is_array($item)) {
                /** @var array<string, mixed> $item */
                $hydrated[] = $factory(new Data($item));
            }
        }

        return new Collection($hydrated);
    }

    /**
     * Run a collection query returning each row as a typed {@see Data} reader.
     *
     * Useful for document agendas whose full read schema is not modelled as a dedicated
     * DTO — you choose the field selection and read values type-safely.
     *
     * @param list<Field> $fields
     * @return Collection<Data>
     */
    protected function queryRaw(
        string $rootField,
        array $fields,
        ?Where $where = null,
        ?Order $order = null,
        ?int $skip = null,
        ?int $take = null,
    ): Collection {
        return $this->queryCollection(
            $rootField,
            $fields,
            static fn (Data $data): Data => $data,
            $where,
            $order,
            $skip,
            $take
        );
    }

    /**
     * Execute a write mutation and return its {@see MutationResult}.
     *
     * @param array<string, mixed> $arguments
     */
    protected function mutate(string $mutation, array $arguments): MutationResult
    {
        $document = $this->mutationBuilder->build($mutation, $arguments);
        $response = $this->transport->execute($document)
            ->throwOnError();

        return MutationResult::fromArray($response->field($mutation));
    }
}
