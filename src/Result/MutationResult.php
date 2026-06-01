<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Result;

use VsPoint\MoneyS3\Exception\MutationFailedException;

/**
 * The standard Money S3 write-mutation payload: the import-queue `guid` of the queued
 * document and the `isSuccess` flag indicating it was accepted.
 *
 * Note that Money S3 writes are asynchronous — `isSuccess: true` means the document was
 * accepted into the import queue, which is then processed sequentially.
 */
final readonly class MutationResult
{
    public function __construct(
        public bool $isSuccess,
        public ?string $guid = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ($data['isSuccess'] ?? false) === true,
            \is_string($data['guid'] ?? null) ? $data['guid'] : null,
        );
    }

    public function assertSuccess(): self
    {
        if (!$this->isSuccess) {
            throw new MutationFailedException('Money S3 rejected the document (isSuccess: false).', $this->guid);
        }

        return $this;
    }
}
