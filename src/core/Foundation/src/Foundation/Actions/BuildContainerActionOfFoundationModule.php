<?php
namespace Module\Foundation\Actions;

use Poirot\Ioc\Container\BuildContainer;

class BuildContainerActionOfFoundationModule
    extends BuildContainer
{
    protected $interfaces 
        = array();

    /*protected $services
        = array(
            ## Helpers
            'view'   => 'Application\Actions\Helper\ViewService',
            'config' => 'Application\Actions\Helper\ConfigAction',

            'url'    => 'Application\Actions\Helper\UrlService',
            'path'   => 'Application\Actions\Helper\PathService',

            'cycle'  => 'Application\Actions\Helper\CycleAction',

            # Html Tag Helpers
            'htmlScript' => 'Application\Actions\Helper\HtmlScriptAction',
            'htmlLink'   => 'Application\Actions\Helper\HtmlLinkAction',

            # Data Actions
            'HomeInfo'      => 'Application\Actions\HomeInfo',
            'RenderContent' => 'Application\Actions\RenderContent',
        );*/
}
