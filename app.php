<?php

use Bokoch\CommissionCalculator\App;
use Bokoch\CommissionCalculator\AppContainer;

require_once 'vendor/autoload.php';

$app = new App(
    new AppContainer()
);

$inputFilePath = $argv[1] ?? null;

if (empty($inputFilePath)) {
    throw new InvalidArgumentException('Input file path is not defined.');
}

$commissions = $app->calculateCommissions($inputFilePath);

var_dump($commissions);
