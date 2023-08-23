<?php

namespace Tests\CommissionCalculators;

use Bokoch\CommissionCalculator\CommissionCalculators\DefaultCommissionCalculator;
use Bokoch\CommissionCalculator\CommissionCalculators\RoundedCentsCommissionCalculator;
use Bokoch\CommissionCalculator\CommissionRateProviders\CommissionRateProvider;
use Bokoch\CommissionCalculator\CurrencyExchangeRateProviders\CurrencyExchangeRateProvider;
use Bokoch\CommissionCalculator\Dto\TransactionData;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class RoundedCentsCommissionCalculatorTest extends AbstractTestCase
{
    #[Test]
    public function it_returns_rounded_calculated_commission(): void
    {
        $transaction = new TransactionData('123', 100, 'foo');
        $baseCurrency = 'bar';

        $commissionRateProviderMock = Mockery::mock(CommissionRateProvider::class);
        $commissionRateProviderMock
            ->shouldReceive('getCommissionRateForBin')
            ->andReturn(0.2134);

        $currencyExchangeRateProviderMock = Mockery::mock(CurrencyExchangeRateProvider::class);
        $currencyExchangeRateProviderMock
            ->shouldReceive('getExchangeRate')
            ->andReturn(2.12);

        $commissionCalculator = new RoundedCentsCommissionCalculator(
            new DefaultCommissionCalculator(
                $commissionRateProviderMock,
                $currencyExchangeRateProviderMock,
                $baseCurrency
            )
        );

        $expectedCommission = round(100 / 2.12 * 0.2134, 2);
        $this->assertSame(expected: $expectedCommission, actual: $commissionCalculator->calculate($transaction));
    }
}