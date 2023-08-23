<?php

namespace Bokoch\CommissionCalculator\CommissionRateProviders;

use Bokoch\CommissionCalculator\CountryCodeProviders\CountryCodeProvider;
use Bokoch\CommissionCalculator\Exceptions\CountryCodeNotFoundException;

final readonly class CountryBasedCommissionRateProvider implements CommissionRateProvider
{
    public function __construct(
        private CountryCodeProvider $countryCodeProvider,
    ) {
    }

    /**
     * @throws CountryCodeNotFoundException
     */
    public function getCommissionRateForBin(string $bin): float
    {
        $countryCode = $this->countryCodeProvider->getCountryCode($bin);

        return $this->isEuCountryCode($countryCode) ? 0.01 : 0.02;
    }

    private function isEuCountryCode(string $countryCode): bool
    {
        return in_array($countryCode, $this->getEuCountryCodes());
    }

    /**
     * @return string[]
     */
    private function getEuCountryCodes(): array
    {
        return [
            'AT',
            'BE',
            'BG',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GR',
            'HR',
            'HU',
            'IE',
            'IT',
            'LT',
            'LU',
            'LV',
            'MT',
            'NL',
            'PO',
            'PT',
            'RO',
            'SE',
            'SI',
            'SK',
        ];
    }
}
