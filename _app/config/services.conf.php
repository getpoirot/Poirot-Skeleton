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
        'Poirot\Application\ConfigService' => ['_name_' => 'app.config', 'options' => APP_DIR_CONFIG.'/sapi.conf.php' ],

        ## sapi application
        'Poirot\Application\SapiService' => ['_name_' => 'sapi', 'config' => 'app.config' ], # using service as config
    ],
];
