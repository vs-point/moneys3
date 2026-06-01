<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Exception;

/**
 * Thrown when the HTTP transport fails (connection error, non-2xx status, malformed body).
 */
final class TransportException extends MoneyS3Exception
{
}
