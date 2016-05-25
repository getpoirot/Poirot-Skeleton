<?php
return [
    /**
     * container builder options
     * @see \Poirot\Ioc\Container\BuildContainer
     */
    'interfaces' =>
        [
            ## program into interfaces

            ### application config
            'config' => '\Poirot\Core\Interfaces\iPoirotEntity',

            'sapi'   => 'Poirot\Application\Interfaces\iApplication',
        ],
    'services'   =>
        [
            ## sapi application
            'Poirot\Application\SapiService' =>
                [
                    '_name_' => 'sapi',
                    'config' => 'sapi.settings' # set registered service as sapi config builder
                ],

            ## Sapi Settings Builder, it can also include base app merged config data
            'Poirot\Application\ConfigService' =>
                [
                    '_name_'  => 'sapi.settings',
                    'options' => new \Poirot\Core\Config(glob(PT_DIR_CONFIG.'/sapi.{,local.}conf.php', GLOB_BRACE)),
                ],
        ],
];
