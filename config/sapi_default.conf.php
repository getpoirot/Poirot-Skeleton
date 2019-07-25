<?php
/**
 * Default Sapi Application Options
 *
 * - merged config will set as service with (app.config) name
 *
 * @see BuildSapi
 */
return [

    'modules' => [
        // Enabled Base Module(s)
        'Foundation',

        'HttpFoundation',
        'HttpRenderer',

        'CliFoundation',

        // instance direct load module
        # 'Application' => new Module()
    ],

    'module_manager' => [
        # options setter
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
];
