<?php

namespace Bokoch\CommissionCalculator\CommissionRateProviders;

interface CommissionRateProvider
{
    public function getCommissionRateForBin(string $bin): float;
}
