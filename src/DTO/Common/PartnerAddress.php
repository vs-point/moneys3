<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * The trading partner block of a document (`partnerAddress`): the business, billing and
 * delivery addresses plus the partner's identification details.
 */
final readonly class PartnerAddress implements InputObject
{
    public function __construct(
        public ?Address $businessAddress = null,
        public ?Address $billingAddress = null,
        public ?Address $deliveryAddress = null,
        public ?string $identificationNumber = null,
        public ?string $vatIdentificationNumber = null,
        public ?string $vatIdentificationNumberSk = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'businessAddress' => $this->businessAddress,
            'billingAddress' => $this->billingAddress,
            'deliveryAddress' => $this->deliveryAddress,
            'identificationNumber' => $this->identificationNumber,
            'vatIdentificationNumber' => $this->vatIdentificationNumber,
            'vatIdentificationNumberSk' => $this->vatIdentificationNumberSk,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
        ];
    }
}
