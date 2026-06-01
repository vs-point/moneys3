<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Auth;

/**
 * An OAuth2 bearer token together with its absolute expiry timestamp.
 */
final readonly class AccessToken
{
    public function __construct(
        public string $value,
        public int $expiresAt,
    ) {
    }

    /**
     * @param int $leewaySeconds treat the token as expired this many seconds early
     */
    public function isValid(int $now, int $leewaySeconds = 30): bool
    {
        return $this->expiresAt - $leewaySeconds > $now;
    }
}
