<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Auth;

/**
 * OAuth2 grant types supported by the Money S3 token endpoint.
 */
enum GrantType: string
{
    case clientCredentials = 'client_credentials';
    case password = 'password';
}
