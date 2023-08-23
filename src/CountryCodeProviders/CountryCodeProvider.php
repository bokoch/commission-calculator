<?php

namespace Bokoch\CommissionCalculator\CountryCodeProviders;

use Bokoch\CommissionCalculator\Exceptions\CountryCodeNotFoundException;

interface CountryCodeProvider
{
    /**
     * @param string $bin
     * @return string
     * @throws CountryCodeNotFoundException
     */
    public function getCountryCode(string $bin): string;
}
