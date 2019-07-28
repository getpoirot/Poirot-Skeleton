<?php
namespace Poirot\Skeleton\Services;

use Poirot\Application\Sapi\BuildSapi;
use Poirot\Application\Sapi;
use Poirot\Application\SapiCli;
use Poirot\Application\SapiHttp;
use Poirot\Ioc\Container\Service\aServiceContainer;
use function Poirot\isCommandLine;


class ServiceSapiApplication
    extends aServiceContainer
{
    protected $name = 'sapi'; // default service name
    protected $allowOverride = true;

    protected $setting;


    /**
     * @inheritdoc
     *
     * @return Sapi
     * @throws \Exception
     */
    function newService()
    {
        $setting = $this->getSetting();
        // Give Current Container As ServiceManager To SAPI Application
        // We inject same service that application will created on and
        // share this as application main(root) service.
        // also cause that this service be available through IOC::getIOC()
        if ( isCommandLine() ) {
            $app = new SapiCli( new BuildSapi($setting), $this->services() );
            $app->setEnabledModules(['Foundation', 'CliFoundation', ]);
        } else {
            $app = new SapiHttp( new BuildSapi($setting), $this->services() );
            $app->setEnabledModules(['Foundation', 'HttpFoundation', 'HttpRenderer', ]);
        }

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
     * @throws \Exception
     */
    function getSetting()
    {
        $setting = $this->setting;

        if (is_string($setting)) {
            ## it is service
            if (! $this->services()->has($setting) )
                throw new \InvalidArgumentException(sprintf(
                    'Service with name (%s) defined as Sapi Config but not found.'
                    , $setting
                ));

            $services = $this->services();
            $setting  = $services->get($setting);
        }

        return $setting;
    }
}
