<?php
namespace Poirot\Skeleton\Services;

use Poirot\Application\Sapi\BuildSapi;
use Poirot\Application\Sapi;
use Poirot\Ioc\Container\Service\aServiceContainer;


class ServiceSapiApplication
    extends aServiceContainer
{
    /** @var string Service Name */
    protected $name = 'sapi'; // default service name

    /**
     * Indicate to allow overriding service
     * with another service
     *
     * @var boolean
     */
    protected $allowOverride = true;

    // setters

    protected $setting;


    /**
     * @inheritdoc
     * @return Sapi
     */
    function newService()
    {
        $setting = $this->getSetting();
        // Give Current Container As ServiceManager To SAPI Application
        if (! $this->_isCommandLineMode() )
            VOID; // maybe default modules


        $builder = new BuildSapi($setting);
        // We inject same service that application will created on and
        // share this as application main(root) service.
        // also cause that this service be available through IOC::getIOC()
        $app     = new Sapi( $builder, $this->services() );

        return $app;
    }

    /**
     * Set Sapi Config
     *
     * ! String for service attain to settings
     * 
     * @param string|array|\Traversable $setting 
     *
     * @return $this
     */
    function setSetting($setting)
    {
        if (!is_string($setting) && !is_array($setting) && !$setting instanceof \Traversable)
            throw new \InvalidArgumentException(sprintf(
                'Config must be array, Traversable or string as service represent these both; given: (%s).'
                , \Poirot\Std\flatten($setting)
            ));

        $this->setting = $setting;
        return $this;
    }

    /**
     * Get Config
     *
     * @return \Traversable|array
     */
    function getSetting()
    {
        $setting = $this->setting;

        if (is_string($setting)) {
            ## it is service
            if (!$this->services()->has($setting))
                throw new \InvalidArgumentException(sprintf(
                    'Service with name (%s) defined as Sapi Config but not found.'
                ));

            $services = $this->services();
            $setting  = $services->get($setting);
        }

        return $setting;
    }


    // ...

    protected function _isCommandLineMode()
    {
        return ( strpos(php_sapi_name(), 'cli') === 0 );
    }
}
