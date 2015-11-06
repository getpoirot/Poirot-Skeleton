<?php
return [
    /**
     * container builder options
     * @see \Poirot\Container\ContainerBuilder
     */
    'interfaces' => [
        ## program into interfaces

        ### application config
        'config' => '\Poirot\Core\Interfaces\iPoirotEntity',

        'sapi'   => 'Poirot\Application\Interfaces\iApplication',
    ],
    'services'   => [
        ## config
        'Poirot\Application\ConfigService'
            => [
                '_name_'  => 'app.config',
                'options' => new \Poirot\Core\Config(glob(PR_DIR_CONFIG.'/sapi.{,local.}conf.php', GLOB_BRACE)),
            ],

        ## sapi application
        'Poirot\Application\SapiService'
            => [
                '_name_' => 'sapi',
                'config' => 'app.config' # set registered service as sapi config builder
            ],
    ],
];
