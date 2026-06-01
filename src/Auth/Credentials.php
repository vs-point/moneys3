<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Auth;

/**
 * OAuth2 credentials for obtaining a Money S3 access token.
 *
 * Two grant types are supported, matching the Money S3 API key settings:
 *  - Client credentials: {@see self::clientCredentials()}
 *  - Resource owner password credentials: {@see self::password()}
 */
final readonly class Credentials
{
    private function __construct(
        public GrantType $grantType,
        public string $clientId,
        public string $clientSecret,
        public ?string $username = null,
        public ?string $password = null,
    ) {
    }

    public static function clientCredentials(string $clientId, string $clientSecret): self
    {
        return new self(GrantType::clientCredentials, $clientId, $clientSecret);
    }

    public static function password(string $clientId, string $clientSecret, string $username, string $password): self
    {
        return new self(GrantType::password, $clientId, $clientSecret, $username, $password);
    }

    /**
     * @return array<string, string>
     */
    public function toTokenRequestParameters(): array
    {
        $parameters = [
            'grant_type' => $this->grantType->value,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        if ($this->grantType === GrantType::password) {
            $parameters['username'] = $this->username ?? '';
            $parameters['password'] = $this->password ?? '';
        }

        return $parameters;
    }
}
