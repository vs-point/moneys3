<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A single postal address (business / billing / delivery) on a document.
 *
 * Only the fields you set are sent — unset (null) fields are omitted from the document.
 */
final readonly class Address implements InputObject
{
    public function __construct(
        public ?string $name = null,
        public ?CodeRef $country = null,
        public ?string $countryName = null,
        public ?string $municipality = null,
        public ?PostalCode $municipalityPostalCode = null,
        public ?string $street = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'name' => $this->name,
            'country' => $this->country,
            'countryName' => $this->countryName,
            'municipality' => $this->municipality,
            'municipalityPostalCode' => $this->municipalityPostalCode,
            'street' => $this->street,
        ];
    }
}
