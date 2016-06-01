<?php
namespace Module\Foundation\HttpSapi;

use Poirot\Ioc\Container;
use Poirot\Ioc\Interfaces\iContainer;
use Poirot\Ioc\Interfaces\Respec\iServicesAware;
use Poirot\Ioc\Interfaces\Respec\iServicesProvider;
use Poirot\Std\Struct\DataEntity;
use Poirot\View\ViewModel\RendererPhp;

/**
 * @method DataEntity                                         config($key = null)
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
    /** @var Container */
    protected $sc;


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

        $foundationActions = $this->action();
        if ($foundationActions->has($method))
            $method = $foundationActions->get($method);

        return call_user_func_array($method, $args);
    }

    /**
     * Proxy Call to Action`s container
     * exp. $this->action('foundation')->url(..)
     *
     * @param string $module
     *
     * @return mixed
     */
    function action($module = 'foundation')
    {
        $foundationActions = $this->services()->from('/modules/'.$module);
        return $foundationActions;
    }

    
    // Implement Services Aware:

    /**
     * Set Service Container
     *
     * @param iContainer $container
     *
     * @return $this
     */
    function setServices(iContainer $container)
    {
        $this->sc = $container;
        return $this;
    }

    /**
     * Services Container
     *
     * @return Container
     */
    function services()
    {
        return $this->sc;
    }
}
