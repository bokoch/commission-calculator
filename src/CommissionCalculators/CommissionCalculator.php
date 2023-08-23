<?php

namespace Bokoch\CommissionCalculator\CommissionCalculators;

use Bokoch\CommissionCalculator\Dto\TransactionData;

interface CommissionCalculator
{
    public function calculate(TransactionData $transactionData): float;
}
