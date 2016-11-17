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
    /** @var string */
    protected $_currModule;


    abstract function __invoke();


    /**
     * Switch Another Module Actions
     *
     * @param null $moduleName
     *
     * @return aAction
     */
    function withModule($moduleName = null)
    {
        $self = clone $this;
        $self->_currModule = $moduleName;

        return $self;
    }

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
            # Nested Neighbor Actions
            $root   = '/module/'.$this->_getModuleName();
            $action = $root.'/actions/'.$method;
            if ($this->services()->has($action)) {
                $callable = $this->services()->get($action);
                return call_user_func_array($callable, $args);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        
        trigger_error(
            sprintf('Call to undefined method from Action: (%s).', $action)
            , E_USER_ERROR
        );
    }

    /**
     * Retrieve Service From Module Namespace
     *
     * @return Container
     */
    function IoC()
    {
        $root = '/module/'.$this->_getModuleName();
        $s    = $this->services()->from($root);
        return $s;
    }


    // Implement iCService

    /**
     * Get Module Actions Container
     *
     * @return ContainerForFeatureActions
     */
    function services()
    {
        if (!$services = $this->services)
            throw new \RuntimeException('Services Container Not Set.');

        return $services;
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

    protected function _getModuleName()
    {
        if ($this->_currModule)
            return $this->_currModule;

        $path   = $this->services()->getPath();
        $exPath = explode('/', $path);

        $moduleName = $exPath[count($exPath)-2];
        return $this->_currModule = $moduleName;
    }
}
