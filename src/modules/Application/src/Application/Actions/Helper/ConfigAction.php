<?php
namespace Application\Actions\Helper;

use Poirot\AaResponder\AbstractAResponder;
use Poirot\Application\aSapi;
use Poirot\Application\Sapi\Module\ModuleActionsContainer;
use Poirot\Container\Interfaces\iContainer;
use Poirot\Container\Interfaces\Respec\iCServiceAware;
use Poirot\Core\Config;

// TODO maybe restricted conf key needed, exp. db or passwords

class ConfigAction extends AbstractAResponder
    implements iCServiceAware ## inject service container
{
    /** @var ModuleActionsContainer */
    protected $services;
    /** @var  Config */
    protected $config;

    /**
     * Invoke Config
     *
     * @return $this
     */
    function exec($confKey = null)
    {
        $config = $this->_getConfig();
        if ($confKey !== null)
            $config = $config->get($confKey);

        return $config;
    }

    protected function _getConfig()
    {
        if (!$this->config) {
            /** @var aSapi $sapi */
            $sapi = $this->services->from('/')->get('sapi');
            $this->config = $sapi->config();
        }

        return $this->config;
    }

    /**
     * Set Service Container
     *
     * @param iContainer $container
     */
    function setServiceContainer(iContainer $container)
    {
        $this->services = $container;
    }
}
