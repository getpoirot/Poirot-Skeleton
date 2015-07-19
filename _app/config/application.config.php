<?php
/**
 * Options pass to Application as SetterBuilder
 *
 * @see \Poirot\Application\Sapi::__construct
 */
return [
    'modules' =>
        /**
         * container builder options
         * @see Poirot\Application\ModuleManager\ModuleManagerOpts
         */
        [
            'enabled' => [
                'Application',
            ],
            'dir_map' => [
                # directory that application module folder exists
                'Application' => APP_DIR_APPLICATION.'/modules'
            ],
        ],

    // set default container service
    'container' =>
        /**
         * container builder options
         * @see \Poirot\Container\ContainerBuilder
         */
        [

        ],
];
