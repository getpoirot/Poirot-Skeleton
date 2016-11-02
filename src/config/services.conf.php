<?php
/**
 * container builder options
 * @see \Poirot\Ioc\Container\BuildContainer
 */
return array(
    'implementations' =>
     // services interface implementation contract
        array(
            // TODO use ::class instead
            'sapi'   => '\Poirot\Application\Interfaces\iApplication',

            'config' => '\Poirot\Std\Interfaces\Struct\iDataEntity',
        ),
    'services' =>
        array(
            'sapi' =>
            // Application Sapi Service Factory
                array(
                    \Poirot\Ioc\Container\BuildContainer::INST => 'Poirot\Application\ServiceSapiApplication',
                    'setting' => 'sapi.setting'
                    // config can be (string) as registered service
                    // or \Traversable|array instance
                ),
            'sapi.setting' =>
            // Implement of \Traversable|array, defined as service so it can be
            // replaced with other to load config from DB in exp.
            // exp. load maintain modules if system is on maintain
            //      or load specific modules for domain name, etc..
                array(
                    \Poirot\Ioc\Container\BuildContainer::INST => 'Poirot\Application\ServiceSapiConfigDefault',
                    'setting' => \Poirot\Config\load(PT_DIR_CONFIG.'/sapi_default'),
                ),
        ),
);
