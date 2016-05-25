<?php
/**
 * container builder options
 * @see \Poirot\Ioc\Container\BuildContainer
 */
return array(
    'implementations' =>
     // services interface implementation contract
        array(
            'sapi'   => '\Poirot\Application\Interfaces\iApplication',
            #'sapi'   => \Poirot\Application\Interfaces\iApplication::class,

            'config' => '\Poirot\Std\Interfaces\Struct\iDataEntity',
            #'config' => \Poirot\Std\Interfaces\Struct\iDataEntity::class,
        ),
    'services' =>
        array(
            'sapi' =>
            // Application Sapi Service Factory
                array(
                    ':class'  => 'Poirot\Application\SapiService',
                    #':class'  => \Poirot\Application\SapiService::class ,
                    'setting' => 'sapi.setting'
                    // config can be (string) as registered service
                    // or \Traversable|array instance
                ),
            'sapi.setting' =>
            // Implement of \Traversable|array, defined as service so it can be
            // replaced with other to load config from DB in exp.
                array(
                    ':class'  => 'Poirot\Application\ServiceConfigSapiDefault',
                    #':class'  => \Poirot\Application\ServiceConfigSapiDefault::class,
                    'setting' => \Poirot\Config\load(PT_DIR_CONFIG.'/sapi'),
                ),
        ),
);
