<?php
/**
 * Options pass to Application as SetterBuilder
 *
 * @see \Poirot\Application\Sapi::__construct
 */
return [
    'modules' =>
        # module options
        [
            'enabled' => [
                'application',
                'analytics',
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
