<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Journal;

use Brick\DateTime\LocalDate;
use Brick\Math\BigDecimal;
use VsPoint\MoneyS3\GraphQL\Field;
use VsPoint\MoneyS3\Hydration\Data;

/**
 * A row of the cash journal (peněžní deník), as returned by the `journalTrs` query.
 */
final readonly class JournalTransaction
{
    public function __construct(
        public int $id,
        public int $year,
        public bool $isDeleted,
        public ?LocalDate $date,
        public ?string $description,
        public ?string $accountMovementShortCut,
        public ?string $srcDocumentNumber,
        public ?BigDecimal $amountHc,
        public ?BigDecimal $amount,
        public ?BigDecimal $exchangeRate,
        public ?string $currencyCode,
        public ?string $companyIdentificationNumber,
        public ?string $companyName,
        public ?string $identificationNumber,
        public ?string $centreShortCut,
        public ?string $jobOrderShortCut,
        public ?string $operationShortCut,
    ) {
    }

    public static function fromData(Data $data): self
    {
        $company = $data->nested('company');

        return new self(
            $data->int('id'),
            $data->int('year'),
            $data->bool('isDeleted'),
            $data->nullableLocalDate('date'),
            $data->nullableString('description'),
            $data->nested('accountMovement')?->nullableString('shortCut'),
            $data->nullableString('srcDocumentNumber'),
            $data->nullableDecimal('amountHc'),
            $data->nullableDecimal('amount'),
            $data->nullableDecimal('exchangeRate'),
            $data->nested('currency')?->nullableString('code'),
            $company?->nullableString('identificationNumber'),
            $company?->nested('deliveryAddress')?->nullableString('name'),
            $data->nullableString('identificationNumber'),
            $data->nested('centre')?->nullableString('shortCut'),
            $data->nested('jobOrder')?->nullableString('shortCut'),
            $data->nested('operation')?->nullableString('shortCut'),
        );
    }

    /**
     * Default field selection covering every property of this DTO.
     *
     * @return list<Field>
     */
    public static function fields(): array
    {
        return [
            Field::leaf('isDeleted'),
            Field::leaf('id'),
            Field::leaf('year'),
            Field::leaf('date'),
            Field::leaf('description'),
            Field::nested('accountMovement', ['shortCut']),
            Field::leaf('srcDocumentNumber'),
            Field::leaf('amountHc'),
            Field::nested('currency', ['code']),
            Field::leaf('amount'),
            Field::leaf('exchangeRate'),
            Field::nested('company', ['identificationNumber', Field::nested('deliveryAddress', ['name'])]),
            Field::leaf('identificationNumber'),
            Field::nested('centre', ['shortCut']),
            Field::nested('jobOrder', ['shortCut']),
            Field::nested('operation', ['shortCut']),
        ];
    }
}
