<?php
/**
 * Default Sapi Application Options
 *
 * - merged config will set as service with (app.config) name
 */

return [
    /**
     * @see BuildSapi
     */

    'modules' =>
        /**
         * this list used by Module Manager To Load Modules
         * @see \Poirot\Application\ModuleManager\AbstractModuleManager::loadModule
         */
        [
            // Enabled Base Module(s)
            'Foundation',

            'HttpFoundation',
            'HttpRenderer',

            'CliFoundation',

            # instance direct load module
            ## 'Application' => new Module()
        ],
    'module_manager' =>
        [
            # options setter
            /** @see Poirot\Application\Sapi\SapiModuleManagerOpts */
            'dir_map' => [
                # directory that application module folder exists
                # 'myModule' => APP_DIR_APPLICATION.'/modules',
            ],

            'modules_dir' => [
                # default modules directory
                PT_DIR_CORE,
                // ...
            ],
            'events' => [
                /** @see \Poirot\Events\Event\BuildEvent */
                #'listeners' => [
                   // ...
                #],
                #'then' => []// ...,
            ],
        ],

    'default_config' => [
        // Options Key Merged Into Config Service
        'extra_merged_config' => 'extra',
    ],
];
