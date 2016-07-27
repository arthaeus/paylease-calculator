<?php

require __DIR__.'/vendor/autoload.php';

use Pimple\Container as Container;
use Calculator\DI\CalculatorProvider as CalculatorProvider;

$container = new Container();
$container->register( new CalculatorProvider() );

$ICalculator = $container['ICalculator'];

$ICalculator->input( new \stdClass() );
$ICalculator->run();


//change the math problem at runtime (9 5 3 + 2 4 ^ - +) = 1
$postfix = new \stdClass();
$postfix->expression = "9 5 3 + 2 4 ^ - +";
$ICalculator->getIInput()->setMathProblem( $postfix );
$ICalculator->run();


//change the math problem.  call input() and it will again read from the ini file for the math problem
$ICalculator->input( new \stdClass() );
$ICalculator->run();


//change the algorithm at runtime.  instead of rpn, use the soap math algorithm (you can add, subtract, multiply, and divide).  the math problem is read from the ini file
$ICalculator->setIAlgorithm($container['IAlgorithmSoap']);
$ICalculator->input( new \stdClass() );
$ICalculator->run();


//change the math problem at runtime.  manually create the math problem, and set it to the input
$mathProblem = $ICalculator->getIInput()->getMathProblem();
$mathProblem->x = 700;
$mathProblem->y = 77;
$mathProblem->operation = "-";
$ICalculator->getIInput()->setMathProblem( $mathProblem );
$ICalculator->run();


//change the algorithm back to rpn.  read from input ini file
$ICalculator->setIAlgorithm($container['IAlgorithmRpn']);
$ICalculator->input( new \stdClass() );
$ICalculator->run();
