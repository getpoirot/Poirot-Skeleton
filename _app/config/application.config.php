<?php
/**
 * Default Application Options
 *
 * - pass to Application as SetterBuilder
 *   @see \Poirot\Application\Sapi::__construct
 * 
 */
return [
    'modules' =>
        [
            // Enabled Application Module(s)
            'Application',

            # instance direct load module
            ## 'Application' => new Module()

            # instance module loading with module manager options
            # 'Application' => ['dir_map' => APP_DIR_APPLICATION.'/modules'],
        ],
    'module_manager' =>
        /**
         * container builder options
         * @see Poirot\Application\Sapi\ModuleManagerOpts
         */
        [
            'options' => [
                # options setter
                'dir_map' => [
                    # directory that application module folder exists
                    'Application' => APP_DIR_APPLICATION.'/modules',
                    'NewModule'   => APP_DIR_APPLICATION.'/modules',
                ],
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
