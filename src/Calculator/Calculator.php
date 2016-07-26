<?php

namespace Calculator;

use Pimple\Container as Container;
use Calculator\DI\CalculatorProvider as CalculatorProvider;

class Calculator implements \SPLSubject
{

    protected $IAlgorithm  = null;
    protected $IOutput     = null;
    protected $IInput      = null;
    protected $message     = null;


    public function __construct()
    {
        $container = new Container();
        $container->register( new CalculatorProvider() );
        $this->IOutput = new \SPLObjectStorage();
    }

    public function setIInput( \Input\IInput $IInput )
    {
        $this->IInput = $IInput;
        return $this;
    }

    public function getIInput()
    {
        return $this->IInput;
    }

    public function setMessage( $message )
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage( )
    {
        return $this->message;
    }

    public function setIAlgorithm( \Algorithm\IAlgorithm $IAlgorithm )
    {
        $this->IAlgorithm = $IAlgorithm;
        return $this;
    }

    public function setIOutput( \Output\IOutput $IOutput )
    {
        $this->IOutput->attach( $IOutput );
        return $this;
    }

    public function getIAlgorithm( )
    {
        return $this->IAlgorithm;
    }

    public function getIOutput( )
    {
        return $this->IOutput;
    }

    public function input( \stdClass $input )
    {
        /**
         * at this point, run the input class input() method and this will populate the math problem to the input
         */
        $this->IInput->input($input);
    }

    public function run()
    {
        $this->message = $this->IAlgorithm->calculate($this->IInput->getMathProblem());
        $this->notify();
    }

    public function attach( \SPLObserver $observer )
    {
        $this->IOutput->attach($observer);
    }

    public function detach ( \SplObserver $observer )
    {
        $this->IOutput->detach($observer);
    }

    public function notify()
    {
        foreach ($this->IOutput as $obj) 
        {
            $obj->update($this);
        }
    }
}

