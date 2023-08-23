<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\CommissionRateProviders;

interface CommissionRateProvider
{
    public function getCommissionRateForBin(string $bin): float;
}
