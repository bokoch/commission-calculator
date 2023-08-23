<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\Dto;

final readonly class ExchangeRateData
{
    public function __construct(
        public string $baseCurrencyCode,
        public string $currencyCode,
        public float $rate,
    ) {
    }
}