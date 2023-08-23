<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\CommissionCalculators;

use Bokoch\CommissionCalculator\CommissionRateProviders\CommissionRateProvider;
use Bokoch\CommissionCalculator\CurrencyExchangeRateProviders\CurrencyExchangeRateProvider;
use Bokoch\CommissionCalculator\Dto\TransactionData;
use Bokoch\CommissionCalculator\Exceptions\ExchangeRateNotFoundException;

final readonly class RoundedCentsCommissionCalculator implements CommissionCalculator
{
    public function __construct(
        private CommissionCalculator $commissionCalculator
    ) {
    }

    public function calculate(TransactionData $transactionData): float
    {
        return round($this->commissionCalculator->calculate($transactionData), 2);
    }
}
