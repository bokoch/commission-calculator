<?php

namespace Bokoch\CommissionCalculator\Dto;

readonly class ExchangeRateData
{
    public function __construct(
        public string $baseCurrencyCode,
        public string $currencyCode,
        public float $rate,
    ) {
    }
}