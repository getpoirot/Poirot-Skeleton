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
            'url'  => 'Application\Actions\UrlService',
            'path' => 'Application\Actions\PathService',

        ];
}
 