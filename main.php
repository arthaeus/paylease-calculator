<?php

require __DIR__.'/vendor/autoload.php';

use Pimple\Container as Container;
use Calculator\DI\CalculatorProvider as CalculatorProvider;

$container = new Container();
$container->register( new CalculatorProvider() );

$ICalculator = $container['ICalculator'];

$ICalculator->input( new \stdClass() );
$ICalculator->run();

//
$mathProblem = $ICalculator->getIInput()->getMathProblem();
$mathProblem->x = 700;
$mathProblem->y = 77;
$ICalculator->getIInput()->setMathProblem( $mathProblem );
//

$ICalculator->setIAlgorithm($container['IAlgorithmSoap']);
$ICalculator->run();


$ICalculator->input( new \stdClass() );
$ICalculator->run();
