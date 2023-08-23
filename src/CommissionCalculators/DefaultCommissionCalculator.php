<?php

namespace Bokoch\CommissionCalculator\CommissionCalculators;

use Bokoch\CommissionCalculator\CommissionRateProviders\CommissionRateProvider;
use Bokoch\CommissionCalculator\CurrencyExchangeRateProviders\CurrencyExchangeRateProvider;
use Bokoch\CommissionCalculator\Dto\TransactionData;
use Bokoch\CommissionCalculator\Exceptions\ExchangeRateNotFoundException;

final readonly class DefaultCommissionCalculator implements CommissionCalculator // TODO: rename
{
    public function __construct(
        private CommissionRateProvider $commissionRateProvider,
        private CurrencyExchangeRateProvider $currencyExchangeRateProvider,
        private string $baseCurrency,
    ) {
    }

    /**
     * @throws ExchangeRateNotFoundException
     */
    public function calculate(TransactionData $transactionData): float
    {
        $amountIncludingCommissionRate = $this->getAmountIncludingCommissionRate($transactionData);
        $commissionRate = $this->commissionRateProvider->getCommissionRateForBin($transactionData->bin);

        return $amountIncludingCommissionRate * $commissionRate;
    }

    /**
     * @throws ExchangeRateNotFoundException
     */
    private function getAmountIncludingCommissionRate(TransactionData $transactionData): float
    {
        if ($transactionData->currency === $this->baseCurrency) {
            return $transactionData->amount;
        }

        $exchangeRate = $this->currencyExchangeRateProvider->getExchangeRate($this->baseCurrency, $transactionData->currency);
        if ($exchangeRate > 0) {
            return $transactionData->amount / $exchangeRate;
        }

        return $transactionData->amount;
    }
}
