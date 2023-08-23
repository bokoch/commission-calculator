<?php

use Bokoch\CommissionCalculator\App;
use Bokoch\CommissionCalculator\AppContainer;

require_once 'vendor/autoload.php';

$app = new App(
    new AppContainer()
);

$app->run(__DIR__ . '/resources/input.txt');
