<?php
namespace Module\Foundation\Actions;

use Poirot\Application\Interfaces\Sapi\iSapiServer;
use Poirot\Application\Sapi\Module\ContainerForFeatureActions;
use Poirot\Application\SapiCli;
use Poirot\Application\SapiHttp;
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
     * @param string $moduleName
     *
     * @return aAction
     */
    function withModule($moduleName)
    {
        $self = clone $this;
        $self->_currModule = $moduleName;

        return $self;
    }

    /**
     * Retrieve Service From Module Namespace
     *
     * @return Container
     */
    function moduleServices()
    {
        $root = '/module/'.$this->_getModuleName();
        $s    = $this->services()->from($root);
        return $s;
    }

    /**
     * Sapi Server
     *
     * @return iSapiServer|SapiHttp|SapiCli
     */
    function sapi()
    {
        $sapi = $this->services()->get('/sapi');
        return $sapi;
    }

    /**
     * Call to neighbors module actions in container
     *
     * note: for more readability all magic calls start with
     *       upper case character.
     *
     * - call neighbor nested actions (actions registered within current module)
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
            $callable = null;

            # Nested Neighbor Actions From Module:
            $root   = '/module/'.$this->_getModuleName();
            $action = $root.'/actions/'.$method;
            if ($this->services()->has($action))
                $callable = $this->services()->get($action);

            # Neighbor Action From Services Path:
            if (!$callable) {
                $action = $this->services()->getPath().'/'.$method;
                if ($this->services()->has($action))
                    $callable = $this->services()->get($action);
            }

            # From Nested Level One Of Container
            if (!$callable) {
                $service = $this->services();
                foreach ($service->listNested() as $nested) {
                    $nContainer = $service->from($nested);
                    if ($nContainer->has($method)) {
                        $callable = $nContainer->get($method);
                        break;
                    }
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }

        if ($callable)
            return call_user_func_array($callable, $args);

        trigger_error(
            sprintf('Call to undefined method from Action: (%s).', $action)
            , E_USER_ERROR
        );
    }


    // Implement iCService

    /**
     * Get Module Actions Container
     *
     * note: container nested to main container during module
     *       loading
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
        if (!$exPath[1] === 'module')
            throw new \Exception("Module Container Structure is Unknown By Actions. given: ($path).");

        $moduleName = $exPath[2];
        return $this->_currModule = $moduleName;
    }
}
