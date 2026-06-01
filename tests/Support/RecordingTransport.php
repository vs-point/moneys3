<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Support;

use VsPoint\MoneyS3\Transport\GraphQLResponse;
use VsPoint\MoneyS3\Transport\Transport;

/**
 * In-memory {@see Transport} test double. Records the last document executed and replays
 * a queue of preset responses.
 */
final class RecordingTransport implements Transport
{
    public ?string $lastDocument = null;

    /**
     * @var list<string>
     */
    public array $documents = [];

    /**
     * @var list<GraphQLResponse>
     */
    private array $responses;

    public function __construct(GraphQLResponse ...$responses)
    {
        $this->responses = array_values($responses);
    }

    public function execute(string $document, array $variables = []): GraphQLResponse
    {
        $this->lastDocument = $document;
        $this->documents[] = $document;

        return array_shift($this->responses) ?? new GraphQLResponse([]);
    }
}
