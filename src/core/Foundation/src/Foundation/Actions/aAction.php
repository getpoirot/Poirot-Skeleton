<?php
namespace Module\Foundation\Actions;

use Poirot\Application\Sapi\Module\ContainerForFeatureActions;
use Poirot\Ioc\Container;
use Poirot\Ioc\Interfaces\iContainer;
use Poirot\Ioc\Interfaces\Respec\iServicesAware;
use Poirot\Ioc\Interfaces\Respec\iServicesProvider;

abstract class aAction
    implements iServicesAware
    , iServicesProvider
{
    /** @var ContainerForFeatureActions */
    protected $services;
    
    
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
        kd($this->services);
    }
    
    
    // Implement iCService

    /**
     * Get Module Actions Container
     *
     * - this actions also is member of Module Actions Container
     *
     * @return Container
     */
    function services()
    {
        return $this->services;
    }

    /**
     * Set Service Container
     *
     * @param iContainer $container
     */
    function setServices(iContainer $container)
    {
        $this->services = $container;
    }
}
