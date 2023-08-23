<?php

namespace Tests\CommissionCalculators;

use Bokoch\CommissionCalculator\CommissionCalculators\DefaultCommissionCalculator;
use Bokoch\CommissionCalculator\CommissionRateProviders\CommissionRateProvider;
use Bokoch\CommissionCalculator\CurrencyExchangeRateProviders\CurrencyExchangeRateProvider;
use Bokoch\CommissionCalculator\Dto\TransactionData;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class DefaultCommissionCalculatorTest extends AbstractTestCase
{
    #[Test]
    public function it_does_not_apply_exchange_rate_when_transaction_and_base_currencies_same(): void
    {
        $transaction = new TransactionData('123', 100, 'foo');
        $baseCurrency = 'foo';

        $commissionRateProviderMock = Mockery::mock(CommissionRateProvider::class);
        $commissionRateProviderMock
            ->shouldReceive('getCommissionRateForBin')
            ->andReturn(1);

        $currencyExchangeRateProviderMock = Mockery::mock(CurrencyExchangeRateProvider::class);

        $commissionCalculator = new DefaultCommissionCalculator(
            $commissionRateProviderMock,
            $currencyExchangeRateProviderMock,
            $baseCurrency
        );

        $this->assertSame(expected: $transaction->amount, actual: $commissionCalculator->calculate($transaction));
    }

    #[Test]
    #[DataProvider('transactionsWithBasedCurrency')]
    public function it_calculate_commission_as_multiplying_amount_on_commission_rate_when_transaction_and_base_currencies_same(
        float $transactionAmount,
        float $commissionRate,
        float $expectedCommission,
    ): void {
        $transaction = new TransactionData('123', $transactionAmount, 'foo');
        $baseCurrency = 'foo';

        $commissionRateProviderMock = Mockery::mock(CommissionRateProvider::class);
        $commissionRateProviderMock
            ->shouldReceive('getCommissionRateForBin')
            ->andReturn($commissionRate);

        $currencyExchangeRateProviderMock = Mockery::mock(CurrencyExchangeRateProvider::class);

        $commissionCalculator = new DefaultCommissionCalculator(
            $commissionRateProviderMock,
            $currencyExchangeRateProviderMock,
            $baseCurrency
        );

        $this->assertSame(expected: $expectedCommission, actual: $commissionCalculator->calculate($transaction));
    }

    #[Test]
    public function it_include_exchange_rate_into_calculation_when_transaction_and_base_currencies_different(): void
    {
        $transaction = new TransactionData('123', 100, 'foo');
        $baseCurrency = 'bar';

        $commissionRateProviderMock = Mockery::mock(CommissionRateProvider::class);
        $commissionRateProviderMock
            ->shouldReceive('getCommissionRateForBin')
            ->andReturn(0.2);

        $currencyExchangeRateProviderMock = Mockery::mock(CurrencyExchangeRateProvider::class);
        $currencyExchangeRateProviderMock
            ->shouldReceive('getExchangeRate')
            ->andReturn(2.456);

        $commissionCalculator = new DefaultCommissionCalculator(
            $commissionRateProviderMock,
            $currencyExchangeRateProviderMock,
            $baseCurrency
        );

        $expectedCommission = 100 / 2.456 * 0.2;
        $this->assertSame(expected: $expectedCommission, actual: $commissionCalculator->calculate($transaction));
    }

    #[Test]
    public function it_exclude_zero_exchange_rate_into_calculation_when_transaction_and_base_currencies_different(): void
    {
        $transaction = new TransactionData('123', 100, 'foo');
        $baseCurrency = 'bar';

        $commissionRateProviderMock = Mockery::mock(CommissionRateProvider::class);
        $commissionRateProviderMock
            ->shouldReceive('getCommissionRateForBin')
            ->andReturn(0.2);

        $currencyExchangeRateProviderMock = Mockery::mock(CurrencyExchangeRateProvider::class);
        $currencyExchangeRateProviderMock
            ->shouldReceive('getExchangeRate')
            ->andReturn(0);

        $commissionCalculator = new DefaultCommissionCalculator(
            $commissionRateProviderMock,
            $currencyExchangeRateProviderMock,
            $baseCurrency
        );

        $expectedCommission = 100 * 0.2;
        $this->assertSame(expected: $expectedCommission, actual: $commissionCalculator->calculate($transaction));
    }

    // DATA PROVIDERS

    public static function transactionsWithBasedCurrency(): array
    {
        return [
            [100, 2, 200],
            [150, 3, 450],
            [20, 5, 100],
            [50, 1, 50],
        ];
    }
}