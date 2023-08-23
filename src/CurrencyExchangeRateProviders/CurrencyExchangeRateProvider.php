<?php

namespace Bokoch\CommissionCalculator\CurrencyExchangeRateProviders;

use Bokoch\CommissionCalculator\Dto\ExchangeRateData;
use Bokoch\CommissionCalculator\Exceptions\ExchangeRateNotFoundException;

interface CurrencyExchangeRateProvider
{
    /**
     * @param string $baseCurrency
     * @param string $targetCurrency
     * @return float
     * @throws ExchangeRateNotFoundException
     */
    public function getExchangeRate(string $baseCurrency, string $targetCurrency): float;
}
