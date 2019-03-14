<?php
namespace Poirot\Skeleton\Services;

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
        $config = $this->getSettings();
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

    function getSettings()
    {
        if ($this->options)
            return $this->options;


        $config = \Poirot\Std\Type\StdString::safeJoin(DS, PT_DIR_CONFIG, 'sapi_default');
        if (false === $conf = \Poirot\Config\load($config))
            throw new \RuntimeException(sprintf(
                'Merged Config Named (%s) Has Error And Not Loaded.'
                , $config
            ));


        return $conf;
    }
}
