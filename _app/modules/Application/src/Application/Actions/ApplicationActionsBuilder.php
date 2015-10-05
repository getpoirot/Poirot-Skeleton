<?php
namespace Application\Actions;

use Poirot\Container\ContainerBuilder;

class ApplicationActionsBuilder extends ContainerBuilder
{
    protected $interfaces =
        [

        ];

    protected $services =
        [
            ## Helpers
            'url'  => 'Application\Actions\Helper\UrlService',
            'path' => 'Application\Actions\Helper\PathService',

            'HomeInfo' => 'Application\Actions\HomeInfo',
        ];
}
 