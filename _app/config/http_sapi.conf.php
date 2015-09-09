<?php
/**
 * Default PHP as HTTP SAPI Configuration
 *
 * - accessible from (config) service
 *
 */
return [
    'container' =>
    /**
     * container builder options
     * @see \Poirot\Container\ContainerBuilder
     */

        [
            'services'   => [
                'HttpSapi.ServiceBuilder' => [
                    ## default http sapi services as builder
                    /** @see \Poirot\Application\Sapi\Server\Http\Service\DefaultServicesAsBuilder */

                    '_class_' => 'InstanceService',
                    'service' => 'Poirot\Application\Sapi\Server\Http\Service\DefaultServicesAsBuilder',
                ]
            ],
        ],
];
