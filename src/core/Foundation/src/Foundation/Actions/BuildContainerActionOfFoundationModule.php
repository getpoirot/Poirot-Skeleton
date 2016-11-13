<?php
namespace Module\Foundation\Actions;

use Poirot\Ioc\Container\BuildContainer;

class BuildContainerActionOfFoundationModule
    extends BuildContainer
{
    protected $services
        = array(
            ## Helpers
            'view'   => 'Module\Foundation\Actions\Helper\ViewService',
            'config' => 'Module\Foundation\Actions\Helper\ConfigAction',

            'url'    => 'Module\Foundation\Actions\Helper\UrlService',
            'path'   => 'Module\Foundation\Actions\Helper\PathService',

            'cycle'  => 'Module\Foundation\Actions\Helper\CycleAction',

            # Html Tag Helpers
            'htmlScript' => 'Module\Foundation\Actions\Helper\HtmlScriptAction',
            'htmlLink'   => 'Module\Foundation\Actions\Helper\HtmlLinkAction',

            'parseRequestData' => 'Module\Foundation\Actions\ParseRequestData',

            # Data Actions
            'HomeInfo'      => 'Module\Foundation\Actions\HomeInfo',
        );
}
