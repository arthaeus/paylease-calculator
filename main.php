<?php

require __DIR__.'/vendor/autoload.php';

use Pimple\Container as Container;
use Calculator\DI\CalculatorProvider as CalculatorProvider;

$container = new Container();
$container->register( new CalculatorProvider() );

$ICalculator = $container['ICalculator'];
//$IAlgorithm = new Algorithm\RPN\Rpn();

$ICalculator->run();
