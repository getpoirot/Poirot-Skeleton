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
            'url'    => 'Application\Actions\Helper\UrlService',
            'path'   => 'Application\Actions\Helper\PathService',
            'script' => 'Application\Actions\Helper\ScriptAction',
            'cycle'  => 'Application\Actions\Helper\CycleAction',
            'view'   => 'Application\Actions\Helper\ViewService',

            # Data Actions
            'HomeInfo'      => 'Application\Actions\HomeInfo',
            'RenderContent' => 'Application\Actions\RenderContent',
        ];
}
 