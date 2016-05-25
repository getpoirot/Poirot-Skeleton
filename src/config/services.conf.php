<?php
/**
 * container builder options
 * @see \Poirot\Ioc\Container\BuildContainer
 */
return array(
    'implementations' =>
     // services interface implementation contract
        array(
            'config' => '\Poirot\Std\Interfaces\Struct\iDataEntity',
            #'config' => \Poirot\Std\Interfaces\Struct\iDataEntity::class,

            'sapi'   => '\Poirot\Application\Interfaces\iApplication',
            #'sapi'   => \Poirot\Application\Interfaces\iApplication::class,
        ),
    'services' =>
        array(
            'sapi' =>
            // Application Sapi Service Factory
                array(
                    ':class' => 'Poirot\Application\SapiService',
                    #':class' => \Poirot\Application\SapiService::class ,
                    'config' => 'sapi.settings'
                    // config can be (string) as registered service
                    // or \Traversable|array instance
                ),
            'sapi.settings' =>
            // Implement of \Traversable|array, defined as service so it can be
            // replaced with other to load config from DB in exp.
                array(
                    ':class'      => 'Poirot\Application\ConfigService',
                    #':class'      => \Poirot\Application\ConfigService::class,
                    'sapi_config' => \Poirot\Config\load(PT_DIR_CONFIG.'/sapi'),
                ),
        ),
);
