<?php
namespace Application\HttpSapi;

use Application\ActionComplexTrait;
use Poirot\Container\Interfaces\Respec\iCServiceAware;
use Poirot\Container\Interfaces\Respec\iCServiceProvider;
use Poirot\View\Interpreter\IsoRenderer;

class ViewModelRenderer extends IsoRenderer
    // services injected and accessible
    implements iCServiceAware
    , iCServiceProvider
{
    use ActionComplexTrait;

    /**
     * Call to default actions in container
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    function __call($method, array $args)
    {
        if ($this->hasMethod($method))
            return parent::__call($method, $args);

        $invokableActions = $this->action();
        return call_user_func_array([$invokableActions, $method], $args);
    }
}

// Fatal Errors don`t display white screen
register_shutdown_function(
    function () {
        $errfile = "unknown file";
        $errstr  = "shutdown";
        $errno   = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if( $error !== NULL) {
            $errno   = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr  = $error["message"];

            $message = "( ! )  ". $errstr.' '.$errfile.' line:'.$errline;
            $message = implode(" '+ '", explode("\n", addslashes($message)));

            echo "<script type=\"text/javascript\">alert('{$message}')</script>";
        }
    }
);