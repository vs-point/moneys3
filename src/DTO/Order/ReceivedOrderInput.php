<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Order;

use Brick\DateTime\LocalDate;
use VsPoint\MoneyS3\DTO\Common\PartnerAddress;
use VsPoint\MoneyS3\DTO\Common\ShortCutRef;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * Strictly typed input for creating a received order (objednávka přijatá).
 *
 * @see \VsPoint\MoneyS3\Agenda\ReceivedOrderService
 */
final readonly class ReceivedOrderInput implements InputObject
{
    /**
     * @param list<OrderItem> $items
     */
    public function __construct(
        public ?string $description = null,
        public ?string $documentNumber = null,
        public ?string $variableSymbol = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfIssue = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfSettleFirst = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfSettled = null,
        public LocalDate|\DateTimeInterface|string|null $dateOfSettleBy = null,
        public ?ShortCutRef $centre = null,
        public ?ShortCutRef $jobOrder = null,
        public ?ShortCutRef $operation = null,
        public ?string $shippingMethod = null,
        public ?ShortCutRef $shippingType = null,
        public ?ShortCutRef $shipping = null,
        public ?PartnerAddress $partnerAddress = null,
        public array $items = [],
        public ?string $guid = null,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'guid' => $this->guid,
            'description' => $this->description,
            'documentNumber' => $this->documentNumber,
            'variableSymbol' => $this->variableSymbol,
            'dateOfIssue' => $this->dateOfIssue,
            'dateOfSettleFirst' => $this->dateOfSettleFirst,
            'dateOfSettled' => $this->dateOfSettled,
            'dateOfSettleBy' => $this->dateOfSettleBy,
            'centre' => $this->centre,
            'jobOrder' => $this->jobOrder,
            'operation' => $this->operation,
            'shippingMethod' => $this->shippingMethod,
            'shippingType' => $this->shippingType,
            'shipping' => $this->shipping,
            'partnerAddress' => $this->partnerAddress,
            'items' => $this->items === [] ? null : $this->items,
        ];
    }
}
