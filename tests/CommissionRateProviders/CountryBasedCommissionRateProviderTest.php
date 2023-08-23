<?php

namespace Tests\CommissionRateProviders;

use Bokoch\CommissionCalculator\CommissionRateProviders\CountryBasedCommissionRateProvider;
use Bokoch\CommissionCalculator\CountryCodeProviders\CountryCodeProvider;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class CountryBasedCommissionRateProviderTest extends AbstractTestCase
{
    #[Test]
    #[DataProvider('euCountryCodes')]
    public function it_returns_eu_country_specific_commission_rate(string $countryCode): void
    {
        $countryCodeProviderMock = Mockery::mock(CountryCodeProvider::class);
        $countryCodeProviderMock
            ->shouldReceive('getCountryCode')
            ->andReturn($countryCode);

        $commissionRateProvider = new CountryBasedCommissionRateProvider($countryCodeProviderMock);

        $this->assertSame(expected: 0.01, actual: $commissionRateProvider->getCommissionRateForBin('123'));
    }

    #[Test]
    #[DataProvider('nonEuCountryCodes')]
    public function it_returns_non_eu_country_specific_commission_rate(string $countryCode): void
    {
        $countryCodeProviderMock = Mockery::mock(CountryCodeProvider::class);
        $countryCodeProviderMock
            ->shouldReceive('getCountryCode')
            ->andReturn($countryCode);

        $commissionRateProvider = new CountryBasedCommissionRateProvider($countryCodeProviderMock);

        $this->assertSame(expected: 0.02, actual: $commissionRateProvider->getCommissionRateForBin('123'));
    }

    // DATA PROVIDERS

    public static function euCountryCodes(): array
    {
        return [
            ['AT'],
            ['BE'],
            ['BG'],
            ['CY'],
            ['CZ'],
            ['DE'],
            ['DK'],
            ['EE'],
            ['ES'],
            ['FI'],
            ['FR'],
            ['GR'],
            ['HR'],
            ['HU'],
            ['IE'],
            ['IT'],
            ['LT'],
            ['LU'],
            ['LV'],
            ['MT'],
            ['NL'],
            ['PO'],
            ['PT'],
            ['RO'],
            ['SE'],
            ['SI'],
            ['SK'],
        ];
    }

    public static function nonEuCountryCodes(): array
    {
        return [
            ['foo'],
            ['bar'],
            ['xyz'],
            ['abc'],
        ];
    }
}