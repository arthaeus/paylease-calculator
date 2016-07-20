<?php

namespace Calculator\DI;

use \Pimple\Container;

class CalculatorProvider implements \Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
 
        $configDir = __DIR__ . '/../config/*.ini';
        $files = glob( $configDir , GLOB_BRACE);
        $settings = \Zend\Config\Factory::fromFiles($files);

        $pimple['IOutput'] = function ($c) use ($settings) {
            $IOutputNamespace  = "\\Output\\";
            $IOutputClass      = $IOutputNamespace . $settings['dev']['application']['IOutput'];
            return  new $IOutputClass();
        };

        $pimple['ICalculator'] = function ($c) use ($settings) {
            $ICalculatorNamespace  = "\\Calculator\\";
            $ICalculatorClass      = $ICalculatorNamespace . "Calculator";
            $ICalculator = new $ICalculatorClass();
            $ICalculator->setIAlgorithm($c['IAlgorithm']);
            $ICalculator->setIOutput($c['IOutput']);
            return $ICalculator;
        };


        $pimple['IAlgorithm'] = function ($c) use ($settings) {
            $IAlgorithmNamespace  = "\\Algorithm\\";
            $IAlgorithmClass      = $IAlgorithmNamespace . $settings['dev']['application']['IAlgorithm'];
            return new $IAlgorithmClass();
        };
    }
}
