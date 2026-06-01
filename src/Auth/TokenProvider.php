<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Auth;

/**
 * Supplies a valid OAuth2 bearer token for authenticating GraphQL requests.
 */
interface TokenProvider
{
    public function getAccessToken(): string;
}
