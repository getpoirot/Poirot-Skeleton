<?php
/**
 * container builder options
 * @see \Poirot\Ioc\Container\BuildContainer
 */

use Poirot\Ioc\Container\BuildContainer;
use Poirot\Skeleton\Services\ServiceSapiApplication;
use Poirot\Skeleton\Services\ServiceSapiConfigDefault;

return [
    'implementations' =>
     // services interface implementation contract
        [
            'sapi'   => \Poirot\Application\Interfaces\iApplication::class,
            'config' => \Poirot\Std\Interfaces\Struct\iDataEntity::class,
        ],
    'services' =>
        [
            'sapi' =>
            // Application Sapi Service Factory
                [
                    BuildContainer::INST => ServiceSapiApplication::class,
                    'setting' => 'sapi.setting'
                    // config can be (string) as registered service
                    // or \Traversable|array instance
                ],
            'sapi.setting' =>
            // Implement of \Traversable|array, defined as service so it can be
            // replaced with other to load config from DB in exp.
            // exp. load maintain modules if system is on maintain
            //      or load specific modules for domain name, etc..
                [
                    BuildContainer::INST => ServiceSapiConfigDefault::class,
                    'setting' => \Poirot\Std\catchIt(
                        function () {
                            $config = PT_DIR_CONFIG.'/sapi_default';
                            if ($conf = \Poirot\Config\load($config))
                                return $conf;

                            throw new \RuntimeException(sprintf(
                                'Merged Config Named (%s) Has Error And Not Loaded.'
                                , $config
                            ));
                        }
                    ),
                ],
        ],
];