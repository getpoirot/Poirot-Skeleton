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
            'view'   => 'Application\Actions\Helper\ViewService',
            'url'    => 'Application\Actions\Helper\UrlService',
            'path'   => 'Application\Actions\Helper\PathService',

            'cycle'  => 'Application\Actions\Helper\CycleAction',

            # Html Tag Helpers
            'htmlScript' => 'Application\Actions\Helper\HtmlScriptAction',
            'htmlLink'   => 'Application\Actions\Helper\HtmlLinkAction',

            # Data Actions
            'HomeInfo'      => 'Application\Actions\HomeInfo',
            'RenderContent' => 'Application\Actions\RenderContent',
        ];
}
 