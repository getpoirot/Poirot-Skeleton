<?php
namespace Poirot\Skeleton\Services;

use Poirot\Application\Sapi;
use Poirot\Application\SapiCli;
use Poirot\Application\SapiHttp;
use Poirot\Ioc\Container\Service\aServiceContainer;


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

        $modules = $setting['modules'] ?? [];
        if ( \Poirot\isCommandLine() ) {
            $sapi = new SapiCli( $this->services() );

            $modules = array_merge(
                ['Foundation', 'CliFoundation', ],
                $modules
            );

        } else {
            $sapi = new SapiHttp( $this->services() );

            $modules = array_merge(
                ['Foundation', 'HttpFoundation', 'HttpRenderer', ],
                $modules
            );
        }


        ## Sapi Settings as a Builder Extension
        #
        $builder = $setting['sapi'] ?? [];
        if (! empty($builder) ) {
            $sapi->register(new Sapi\Extensions\ConfigBuilderExtension(
                $builder
            ));
        }

        ## Module Manager Extension
        #
        $moduleManager = $setting['module_manager'] ?? [];
        $modularExt = (new Sapi\Extensions\ModularSapiExtension)
            ->setModules($modules)
            ->setModuleManager(
                new Sapi\ModuleManager($moduleManager)
            );

        $sapi->register($modularExt);


        return $sapi;
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
