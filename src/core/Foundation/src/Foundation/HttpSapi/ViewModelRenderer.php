<?php
namespace Module\Foundation\HttpSapi;

use Application\tActionComplex;
use Poirot\Container\Interfaces\Respec\iCServiceAware;
use Poirot\Container\Interfaces\Respec\iCServiceProvider;
use Poirot\View\Interpreter\IsoRenderer;

/**
 * @method \Poirot\Core\Config                          config($key = null)
 * @method \Application\Actions\Helper\ViewAction       view($template = null, $variables = null)
 * @method \Application\Actions\Helper\UrlAction        url($routeName = null, $params = [])
 * @method \Application\Actions\Helper\PathAction       path($arg = null)
 * @method \Application\Actions\Helper\CycleAction      cycle($action = null, $steps = 1, $reset = true)
 * @method \Application\Actions\Helper\HtmlLinkAction   htmlLink($section = 'inline')
 * @method \Application\Actions\Helper\HtmlScriptAction htmlScript($section = 'inline')
 */
class ViewModelRenderer extends IsoRenderer
    // services injected and accessible
    implements iCServiceAware
    , iCServiceProvider
{
    use tActionComplex;

    /** @var bool  */
    protected static $isInitialized = false;

    /**
     * Construct
     *
     */
    function __construct()
    {
        if (!static::$isInitialized) {
            ## Avoid display white screen on fatal errors on render
            $self = $this;
            register_shutdown_function(function () use ($self) {
                $self->__alertError();
            });

            static::$isInitialized = true;
        }
    }

    /**
     * Proxy Call to default Action`s container
     *
     * exp. $this->action('application')->url(..)
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

    protected function __alertError()
    {
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
}
