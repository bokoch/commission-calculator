<?php

use Bokoch\CommissionCalculator\App;
use Bokoch\CommissionCalculator\SimpleContainer;

require_once 'vendor/autoload.php';

$app = new App(
    new SimpleContainer()
);

$inputFilePath = $argv[1] ?? null;

if (empty($inputFilePath)) {
    throw new InvalidArgumentException('Input file path is not defined.');
}

$commissions = $app->calculateCommissions($inputFilePath);

var_dump($commissions);
