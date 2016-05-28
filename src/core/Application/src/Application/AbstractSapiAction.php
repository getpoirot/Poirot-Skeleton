<?php
namespace Application;

use Poirot\AaResponder\AbstractAResponder;
use Poirot\Container\Interfaces\Respec\iCServiceAware;
use Poirot\Container\Interfaces\Respec\iCServiceProvider;
use Poirot\Container\Plugins\PluginsInvokable;

abstract class AbstractSapiAction extends AbstractAResponder
    implements iCServiceAware
    , iCServiceProvider
{
    // Access registered modules actions
    use ActionComplexTrait;

    /**
     * Call to neighbors module actions in container
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    function __call($method, $args)
    {
        // forward proxy call to invokablePlugins instantiate
        if (!$this->__cache_callProxyInvokable)
            $this->__cache_callProxyInvokable = new PluginsInvokable($this->services());

        return $this->__cache_callProxyInvokable->__call($method, $args);
    }
}
