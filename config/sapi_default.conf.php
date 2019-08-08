<?php
/*
 * This config file could be override by user config.
 */
return [

    // Enabled Modules
    'modules' => [
        // Enabled Default Module(s)
        #'Foundation',

        /** @see \Poirot\Skeleton\Services\ServiceSapiApplication */
        ## SapiHttp
        #'HttpFoundation',
        #'HttpRenderer',

        ## SapiCLI
        #'CliFoundation',

        // modules also can defined as early instance
        // instance direct load module
        # 'Application' => new Module()
    ],

    // Module Manager Specific Settings
    'module_manager' => [
        'dir_map' => [
            # directory that application module folder exists
            # 'myModule' => APP_DIR_APPLICATION.'/modules',
        ],
        'modules_dir' => [
            PT_DIR_CORE,
        ],
    ],

    'sapi' => [
        /*
         * This property specifies an array of globally accessible application parameters.
         * Instead of using hardcoded numbers and strings everywhere in your code, it is a
         * good practice to define them as application parameters in a single place and use
         * the parameters in places where needed. For example, you may define the thumbnail
         * image size as a parameter
         */
        'default_config' => [
            // Options Key Merged Into Config Service
            # 'thumbnail.size' => [128, 128],
        ],

        /** @see \Poirot\Ioc\Container\BuildContainer */
        'services' => [

        ],

        /** @see \Poirot\Events\Event\BuildEvent */
        'events' => [

        ],
    ],
];
