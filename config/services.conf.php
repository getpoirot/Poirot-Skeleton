<?php
/**
 * container builder options
 * @see \Poirot\Ioc\Container\BuildContainer
 */
use Poirot\Skeleton\Services\ServiceSapiApplication;
use Poirot\Skeleton\Services\ServiceSapiConfigDefault;

return [
    'implementations' => [
        // services interface implementation contract
        'sapi'   => \Poirot\Application\Interfaces\iApplication::class,
    ],
    'services' => [
        'sapi' => new \Poirot\Ioc\instance(
            ServiceSapiApplication::class
            , [
                'setting' => 'sapi.setting'
                // config can be (string) as registered service
                // or \Traversable|array instance
            ]
        ),

        // Implement of \Traversable|array, defined as service so it can be
        // replaced with other to load config from DB in exp.
        // exp. load maintain modules if system is on maintain
        //      or load specific modules for domain name, etc..
        'sapi.setting' => new \Poirot\Ioc\instance(
            ServiceSapiConfigDefault::class
        ),
    ],
];
