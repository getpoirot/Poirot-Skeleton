<?php
/**
 * Default Sapi Application Options
 *
 * - pass to Application as SetterBuilder
 *   @see \Poirot\Application\Sapi::__construct
 *
 * - merged config will set as service with (config) name
 */

return [
    /**
     * @see \Poirot\Application\Sapi\SapiAppBuilder::build
     */

    'modules' =>
        /**
         * this list used by Module Manager To Load Modules
         * @see \Poirot\Application\ModuleManager\AbstractModuleManager::loadModule
         */
        [
            // Enabled Application Module(s)
            'Application',

            # instance direct load module
            ## 'Application' => new Module()

            # instance module loading with module manager options
            #                   module manager option .....................
            # 'Application' => ['dir_map' => APP_DIR_APPLICATION.'/modules'],
        ],
    'module_manager' =>
        /**
         * build module manager:
         * @see \Poirot\Application\Sapi\SapiAppBuilder::build
         */
        [
            # options setter
            'options' => [
                /** @see Poirot\Application\Sapi\SapiModuleManagerOpts */
                'dir_map' => [
                    # directory that application module folder exists
                    # 'myModule' => APP_DIR_APPLICATION.'/modules',
                ],

                'modules_dir' => [
                    # default modules directory
                    APP_DIR_APPLICATION.'/modules',
                ],
            ],
            'events' => [
                /** @see Poirot\Events\EventBuilder */
                #'listeners' => [
                   // ...
                #],
                #'then' => []// ...,
            ],
        ],
    'container' =>
        /**
         * container builder options
         * @see \Poirot\Container\ContainerBuilder
         */

        [
            'interfaces' => [
                ### program into interface
                'sapi.server' => '\Poirot\Application\Interfaces\Sapi\iSapiServer',
                ## application config
                'sapi.config' => '\Poirot\Core\Interfaces\iPoirotEntity',
            ],
            'services'   => [
                ## sapi server application
                /** @see \Poirot\Application\Sapi\Server\SapiServerService */
                'Poirot\Application\Sapi\Server\SapiServerService',

                'sapi.config' => [
                    '_class_'  => 'InstanceService',
                    'service'  => 'Poirot\Application\Config',
                    'allow_override' => false,
                ],
            ],
        ],
];
