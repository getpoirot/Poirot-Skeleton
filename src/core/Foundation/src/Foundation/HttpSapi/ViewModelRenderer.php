<?php
namespace Module\Foundation\HttpSapi;
use Poirot\Ioc\Interfaces\iContainer;
use Poirot\Ioc\Interfaces\Respec\iServicesAware;
use Poirot\Ioc\Interfaces\Respec\iServicesProvider;
use Poirot\Std\Struct\DataEntity;
use Poirot\View\ViewModel\RendererPhp;

/**
 * @method DataEntity                                          config($key = null)
 * @method \Module\Foundation\Actions\Helper\ViewAction       view($template = null, $variables = null)
 * @method \Module\Foundation\Actions\Helper\UrlAction        url($routeName = null, $params = [])
 * @method \Module\Foundation\Actions\Helper\PathAction       path($arg = null)
 * @method \Module\Foundation\Actions\Helper\CycleAction      cycle($action = null, $steps = 1, $reset = true)
 * @method \Module\Foundation\Actions\Helper\HtmlLinkAction   htmlLink($section = 'inline')
 * @method \Module\Foundation\Actions\Helper\HtmlScriptAction htmlScript($section = 'inline')
 */
class ViewModelRenderer
    extends RendererPhp
    // services injected and accessible
    implements iServicesAware
    , iServicesProvider
{
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
        return call_user_func_array(array($invokableActions, $method), $args);
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

    /**
     * Set Service Container
     *
     * @param iContainer $container
     */
    function setServices(iContainer $container)
    {
        // TODO: Implement setServices() method.
    }

    /**
     * Services Container
     *
     * @return iContainer
     */
    function services()
    {
        // TODO: Implement services() method.
    }
}
