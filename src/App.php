<?php

namespace Bokoch\CommissionCalculator;

use Bokoch\CommissionCalculator\CommissionCalculators\CommissionCalculator;
use Bokoch\CommissionCalculator\CommissionCalculators\DefaultCommissionCalculator;
use Bokoch\CommissionCalculator\CommissionCalculators\RoundedCentsCommissionCalculator;
use Bokoch\CommissionCalculator\CommissionRateProviders\CommissionRateProvider;
use Bokoch\CommissionCalculator\CommissionRateProviders\CountryBasedCommissionRateProvider;
use Bokoch\CommissionCalculator\ConfigRepositories\ConfigRepository;
use Bokoch\CommissionCalculator\ConfigRepositories\InMemoryConfigRepository;
use Bokoch\CommissionCalculator\CountryCodeProviders\BinListCountryCodeProvider;
use Bokoch\CommissionCalculator\CountryCodeProviders\CountryCodeProvider;
use Bokoch\CommissionCalculator\CurrencyExchangeRateProviders\ApiCurrencyExchangeRateProvider;
use Bokoch\CommissionCalculator\CurrencyExchangeRateProviders\CurrencyExchangeRateProvider;

final readonly class App
{
    public function __construct(
        public Container $container
    ) {
        $this->bootstrap();
    }

    public function run(string $inputFilePath): void
    {
        $reader = $this->container->make(TransactionDataFileInputReader::class);
        $transactions = $reader->getTransactions($inputFilePath);

        $commissionCalculator = $this->container->make(CommissionCalculator::class);
        $commissions = [];
        foreach ($transactions as $transaction) {
            $commissions[] = $commissionCalculator->calculate($transaction);
        }

        var_dump($commissions);
    }

    public function bootstrap(): void
    {
        $this->container->register(CommissionCalculator::class, function (Container $container): CommissionCalculator {
//            return new RoundedCentsCommissionCalculator(
//                new DefaultCommissionCalculator(
//                    $container->make(CommissionRateProvider::class),
//                    $container->make(CurrencyExchangeRateProvider::class),
//                    $container->make(ConfigRepository::class)->get('base_currency'),
//                )
//            );

            return new DefaultCommissionCalculator(
                $container->make(CommissionRateProvider::class),
                $container->make(CurrencyExchangeRateProvider::class),
                $container->make(ConfigRepository::class)->get('base_currency'),
            );
        });

        $this->container->register(CommissionRateProvider::class, function (Container $container): CommissionRateProvider {
            return new CountryBasedCommissionRateProvider(
                $container->make(CountryCodeProvider::class),
            );
        });

        $this->container->register(CountryCodeProvider::class, function (Container $container): CountryCodeProvider {
            return new BinListCountryCodeProvider(
                $container->make(ConfigRepository::class)->get('bin_list.base_uri'),
            );
        });

        $this->container->register(CurrencyExchangeRateProvider::class, function (Container $container): CurrencyExchangeRateProvider {
            return new ApiCurrencyExchangeRateProvider(
                $container->make(ConfigRepository::class)->get('exchange_rates_api.base_uri'),
                $container->make(ConfigRepository::class)->get('exchange_rates_api.access_key'),
            );
        });

        $this->container->register(TransactionDataFileInputReader::class, function (Container $container): TransactionDataFileInputReader {
            return new TransactionDataFileInputReader();
        });

        $this->container->registerSingleton(ConfigRepository::class, function (Container $container): ConfigRepository {
            return new InMemoryConfigRepository();
        });
    }
}