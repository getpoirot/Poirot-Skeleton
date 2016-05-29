<?php
namespace Module\Foundation;

use Poirot\AaResponder\AbstractAResponder;

use Poirot\Ioc\Interfaces\Respec\iServicesAware;
use Poirot\Ioc\Interfaces\Respec\iServicesProvider;

abstract class aActionSapi
    extends AbstractAResponder
    implements iServicesAware
    , iServicesProvider
{
    // Access registered modules actions
    use tActionComplex;

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
