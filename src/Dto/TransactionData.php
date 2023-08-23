<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\Dto;

final readonly class TransactionData
{
    public function __construct(
        public string $bin,
        public float $amount,
        public string $currency,
    ) {
    }
}