<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\GraphQL;

/**
 * Assembles a Money S3 write mutation of the shape:
 *
 *     mutation { <name>(<arg>: {…}, definitionXMLTransfer: {…}) { guid isSuccess } }
 *
 * Arguments are passed as a typed map (commonly the agenda {@see InputObject} plus the
 * optional `definitionXMLTransfer` import definition). `null` arguments are dropped.
 */
final class MutationBuilder
{
    public function __construct(
        private readonly ValueEncoder $encoder = new ValueEncoder(),
    ) {
    }

    /**
     * @param array<string, mixed> $arguments
     * @param list<Field|string>   $returnFields
     */
    public function build(string $mutation, array $arguments, array $returnFields = ['guid', 'isSuccess']): string
    {
        $rendered = [];
        foreach ($arguments as $name => $value) {
            if ($value === null) {
                continue;
            }

            $rendered[] = $name . ': ' . $this->encoder->encode($value);
        }

        $argumentList = '(' . implode(', ', $rendered) . ')';

        $selection = implode(' ', array_map(
            static fn (Field|string $field): string => $field instanceof Field ? $field->render() : $field,
            $returnFields,
        ));

        return 'mutation { ' . $mutation . $argumentList . ' { ' . $selection . ' } }';
    }
}
