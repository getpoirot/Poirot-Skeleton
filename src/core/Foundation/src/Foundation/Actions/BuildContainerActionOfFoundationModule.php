<?php
namespace Module\Foundation\Actions;

use Poirot\Ioc\Container\BuildContainer;

class BuildContainerActionOfFoundationModule
    extends BuildContainer
{
    protected $extends
        = array(
            'path' => '/module/foundation/services/path'
        );

    protected $services
        = array(
            ## Helpers
            'view'          => 'Module\Foundation\Actions\Helper\ViewService',
            'config'        => 'Module\Foundation\Actions\Helper\ConfigAction',

            'url'           => 'Module\Foundation\Actions\Helper\UrlService',
            'path'          => 'Module\Foundation\Actions\Helper\PathService',

            'flashMessage'  => 'Module\Foundation\Actions\Helper\FlashMessageAction',
            'cycle'         => 'Module\Foundation\Actions\Helper\CycleAction',

            # Html Tag Helpers
            'htmlHeadTitle' => 'Module\Foundation\Actions\Helper\HtmlHeadTitle',
            'htmlScript'    => 'Module\Foundation\Actions\Helper\HtmlScriptAction',
            'htmlLink'      => 'Module\Foundation\Actions\Helper\HtmlLinkAction',

            # Data Actions
            'HomeInfo'      => 'Module\Foundation\Actions\HomeInfo',
        );
}
