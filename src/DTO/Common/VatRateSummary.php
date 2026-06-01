<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\DTO\Common;

use Brick\Math\BigNumber;
use VsPoint\MoneyS3\GraphQL\InputObject;

/**
 * A document-header VAT summary line (`vatRateSummary` / `vatRateSummaryHc`): the VAT
 * rate together with the base and VAT amounts.
 *
 * Use `vatRateSummaryHc` for the home (domestic) currency and `vatRateSummary` for a
 * foreign currency — the agenda input exposes both.
 */
final readonly class VatRateSummary implements InputObject
{
    public function __construct(
        public int|float|string|BigNumber $vatRate,
        public int|float|string|BigNumber $totalWithoutVat,
        public int|float|string|BigNumber $totalVat,
    ) {
    }

    public function toGraphQL(): array
    {
        return [
            'vatRate' => $this->vatRate,
            'totalWithoutVat' => $this->totalWithoutVat,
            'totalVat' => $this->totalVat,
        ];
    }
}
