<?php
namespace Poirot\Application;

use Poirot\Ioc\Container\Service\aServiceContainer;

class ServiceSapiConfigDefault
    extends aServiceContainer
{
    /** @var string Service Name */
    protected $name = 'sapi.setting';

    /**
     * Indicate to allow overriding service
     * with another service
     *
     * @var boolean
     */
    protected $allowOverride = false;


    // setters:

    protected $options;


    /**
     * Create Service
     * @see SapiService::setSetting
     *
     * @return mixed
     */
    function newService()
    {
        $config = $this->options;
        return $config;
    }

    
    // options

    /**
     * Set Config Options
     * @see ServiceSapiApplication::setSetting
     *
     * @param string|array|\Traversable $settings
     *
     * @return $this
     */
    function setSetting($settings)
    {
        // validating data not necessary here!
        // it will pass to ServiceSapiApplication
        
        $this->options = $settings;
        return $this;
    }
}
