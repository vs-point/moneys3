<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Tests\Unit\Transport;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VsPoint\MoneyS3\Exception\GraphQLException;
use VsPoint\MoneyS3\Exception\UnexpectedResponseException;
use VsPoint\MoneyS3\Transport\GraphQLResponse;

#[CoversClass(GraphQLResponse::class)]
final class GraphQLResponseTest extends TestCase
{
    public function testField(): void
    {
        $response = new GraphQLResponse([
            'agendas' => [
                'items' => [],
            ],
        ]);

        self::assertSame([
            'items' => [],
        ], $response->field('agendas'));
    }

    public function testMissingFieldThrows(): void
    {
        $this->expectException(UnexpectedResponseException::class);

        (new GraphQLResponse([]))->field('agendas');
    }

    public function testThrowOnError(): void
    {
        $response = new GraphQLResponse([], [[
            'message' => 'Boom',
        ]]);

        self::assertTrue($response->hasErrors());

        $this->expectException(GraphQLException::class);
        $this->expectExceptionMessage('Boom');
        $response->throwOnError();
    }
}
