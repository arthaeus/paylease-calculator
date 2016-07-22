<?php

namespace Calculator\DI;

use \Pimple\Container;

class CalculatorProvider implements \Pimple\ServiceProviderInterface
{
    public static function config()
    {
        $configDir = __DIR__ . '/../config/*.ini';
        $files     = glob( $configDir , GLOB_BRACE);
        $settings  = \Zend\Config\Factory::fromFiles($files);
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

        /**
         * i think that this should just return the IniInput class.  i thought about populating the mathProblem in here, but I think that it would make more sense, and be more consistent to make the class responsible for gathering the input.
         * What if we are doing input from the web? it would not be possible for the container to construct the mathProblem.  so for consistency, i'll construct the math problem in the IInput class.
         */
        $pimple['IniInput'] = function ($c) use ($settings) {
            $iniInput = new \Input\IniInput();
            $iniInput->setMathProblem( new \stdClass() );
            return $iniInput;
        };

        $pimple['ICalculator'] = function ($c) use ($settings) {
            $ICalculatorNamespace  = "\\Calculator\\";
            $ICalculatorClass      = $ICalculatorNamespace . "Calculator";
            $ICalculator = new $ICalculatorClass();

            /**
             * if the input is IniInput, then read the values from the ini file for the mathProblem
             */
            if( $settings['dev']['application']['IInput'] == "IniInput" )
            {
                $iniInput =  $c['IniInput'];
                $iniInput->setICalculator( $ICalculator );
                $ICalculator->setIInput( $iniInput );
            }

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
