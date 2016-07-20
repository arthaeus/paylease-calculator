<?php

namespace Calculator\DI;

use \Pimple\Container;

class CalculatorProvider implements \Pimple\ServiceProviderInterface
{
    public static function config()
    {
        $configDir = __DIR__ . '/../config/*.ini';
        $files = glob( $configDir , GLOB_BRACE);
        $settings = \Zend\Config\Factory::fromFiles($files);
        return $settings;
    }

    public function register(Container $pimple)
    {

        $settings = self::config();
 
        $pimple['IOutput']          = function ($c) use ($settings) {
            $IOutputNamespace       = "\\Output\\";
            $IOutputClassArray      = $settings['dev']['application']['IOutput'];
            $IOutputArray = array();
            //application.IOutput.EmailOutput.EmailClass

            foreach( $IOutputClassArray as $key => $IOutputClassName )
            {
                $IOutputClass = $IOutputNamespace . $IOutputClassName;

                /**
                 * if the class implements IEmail, then we need to set its emailer class
                 */

                $IOut = new $IOutputClass();

                if( in_array( 'Output\IEmail' ,class_implements( $IOut ) ) )
                {
                    $IEmailClassName = $settings['email']['application']['EmailClass'];
                    $IEmailClass = new $IEmailClassName();
                    $IOut->setEmailer( $IEmailClass );
                }

                $IOutputArray[] = $IOut;
            }
            return $IOutputArray;
        };

        $pimple['ICalculator'] = function ($c) use ($settings) {
            $ICalculatorNamespace  = "\\Calculator\\";
            $ICalculatorClass      = $ICalculatorNamespace . "Calculator";
            $ICalculator = new $ICalculatorClass();
            $mathProblem = new \stdClass();

            if( $settings['dev']['application']['IAlgorithm'] == "Rpn" )
            {
                $mathProblem->expression = $settings['dev']['application']['MathProblem'];
            }
            else if( $settings['dev']['application']['IAlgorithm'] == "Soap" )
            {
                $mathProblem->x = $settings['dev']['application']['Soap']['addX'];
                $mathProblem->y = $settings['dev']['application']['Soap']['addY'];
            }

            $ICalculator->setMathProblem( $mathProblem );
            $ICalculator->setIAlgorithm($c['IAlgorithm']);
            $IOutputs = $c['IOutput'];
            foreach( $IOutputs as $key => $IOutput )
            {
                $ICalculator->setIOutput( $IOutput );
            }

            return $ICalculator;
        };


        $pimple['IAlgorithm'] = function ($c) use ($settings) {
            $IAlgorithmNamespace  = "\\Algorithm\\";
            $IAlgorithmClass      = $IAlgorithmNamespace . $settings['dev']['application']['IAlgorithm'];
            return new $IAlgorithmClass();
        };
    }
}
