<?php

use Bokoch\CommissionCalculator\App;
use Bokoch\CommissionCalculator\AppContainer;

require_once 'vendor/autoload.php';

$app = new App(
    new AppContainer()
);

$commissions = $app->calculateCommissions(__DIR__ . '/resources/input.txt');

var_dump($commissions);
