<?php

namespace Bokoch\CommissionCalculator\Dto;

readonly class TransactionData
{
    public function __construct(
        public string $bin,
        public float $amount,
        public string $currency,
    ) {
    }
}