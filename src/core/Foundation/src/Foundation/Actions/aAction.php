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

    
    abstract function __invoke();


    /**
     * Call to neighbors module actions in container
     *
     * note: for more readability all magic calls start with
     *       upper case character.
     *
     * - call neighbor nested actions
     *   $this->Authorize()
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws \Exception
     */
    function __call($method, $args)
    {
        try {
            $callable = $this->__get($method);
            if (is_callable($callable))
                return call_user_func_array($callable, $args);
        } catch (\Exception $e) {
            
        }
        
        trigger_error('Call to undefined method '.__CLASS__.'::'.$method.'()', E_USER_ERROR);
    }

    /**
     * @param $name
     * @return callable|mixed
     * 
     * @throws \Exception
     */
    function __get($name)
    {
        # Nested Neighbor Actions
        if ($this->services()->has($name)) {
            // nested actions
            return $this->services()->get($name);
        }

        # Retrieve Nested Services
        if ( substr($name, 0, strlen('Get')) === 'Get' && substr($name, -(strlen('Service'))) === 'Service' ) {
            // Attain Module Nested Service
            $service    = substr($name, strlen('Get'), strlen($name)-strlen('Service')-strlen('Get') );
            $moduleName = $this->_getModuleName();
            try {
                $s = $this->services()->from("/module/{$moduleName}/services");
                return $s->get($service);
            } catch (\Exception $e) {
                throw new \Exception(sprintf(
                    'Error While Retrieve (%s) Service From Nested Module (%s).'
                    , $service, $moduleName
                ), 0, $e);
            }
        }
        
        throw new \Exception(sprintf('(%s) Not found as any action or service.'));
    }

    /**
     * 
     * exp.
     * GetModuleServices()->get('repository/clients')
     * 
     * @return false|Container
     *
     * @throws \Exception
     */
    function ModuleServices($moduleName = null)
    {
        if ($moduleName === null)
            $moduleName = $this->_getModuleName();
        
        $s = $this->services()->from("/module/{$moduleName}/services");
        return $s;
    }
    
    // Implement iCService

    /**
     * Get Module Actions Container
     *
     * - this actions also is member of Module Actions Container
     *
     * @return ContainerForFeatureActions
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


    // ..

    function _getModuleName()
    {
        $path   = $this->services()->getPath();
        $exPath = explode('/', $path);

        $moduleName = $exPath[count($exPath)-2];
        return $moduleName;
    }
}
