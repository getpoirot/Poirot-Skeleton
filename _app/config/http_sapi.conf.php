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
            'interfaces' => [
                'Request'  => 'Poirot\Http\Interfaces\Message\iHttpRequest',
                'Response' => 'Poirot\Http\Interfaces\Message\iHttpResponse',
                'Router'   => 'Poirot\Router\Interfaces\Http\iHChainingRouter',
            ],
            'services'   => [
                #'Request'  => '..',
                #'Response' => '..',
                #'Router'   => '..',
            ],
        ],
];
