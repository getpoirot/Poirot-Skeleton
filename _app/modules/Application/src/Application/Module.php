<?php
namespace Application;

use Application\Actions\ApplicationActionsBuilder;
use Application\Actions\Helper\PathAction;
use Application\HttpSapi\ViewModelRenderer;
use Poirot\Application\Config;
use Poirot\Application\Interfaces\iApplication;
use Poirot\Application\Interfaces\Sapi\iSapiModule;
use Poirot\Application\Sapi;
use Poirot\Application\Sapi\Module\ModuleActionsContainer;
use Poirot\Container\Container;
use Poirot\Container\Service\InstanceService;
use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Loader\Autoloader\AggregateAutoloader;
use Poirot\Router\Http\RChainStack;

class Module implements iSapiModule
    , Sapi\Module\Feature\InitializeSapiFeature
    , Sapi\Module\Feature\AutoloadFeature
    , Sapi\Module\Feature\ServiceContainerFeature
    , Sapi\Module\Feature\ActionProviderFeature
    , Sapi\Module\Feature\ResolveToServicesFeature
    , Sapi\Module\Feature\ConfigFeature
{
    /**
     * Init Module Against Application
     *
     * priority: 1000
     *
     * @param iApplication|Sapi $app Application Instance
     *
     * @throws \Exception
     * @return void
     */
    function Initialize(iApplication $app)
    {
        // init requirements
        if (!getenv('HTTP_MOD_REWRITE'))
            throw new \RuntimeException('It seems that you don\'t have "MOD_REWRITE" enabled on the server.');

        if (!$app instanceof \Poirot\Application\Sapi)
            throw new \Exception('This module is not compatible with application.');
    }

    /**
     * Register class autoload on Autoload
     *
     * priority: 999
     *
     * @param AggregateAutoloader $autoloader
     *
     * @return void
     */
    function withAutoload(AggregateAutoloader $autoloader)
    {
        $autoloader->loader('NamespaceAutoloader')
            ->setStack(__NAMESPACE__, __DIR__);
    }

    /**
     * Build Service Container
     *
     * - register services
     * - define aliases
     * - add initializers
     * - ...
     *
     * @param Container $services
     *
     * @return void
     */
    function withServiceContainer(Container $services)
    {
        ## replace default renderer with Application renderer including stuffs
        if ($services->has('ViewModelRenderer'))
            $services->set(new InstanceService('ViewModelRenderer', new ViewModelRenderer));
    }

    /**
     * Register config key/value
     *
     * - you may return an array or iDataSetConveyor
     *   that would be merge with config current data
     *
     * @param Config $config
     *
     * @return array|iDataSetConveyor
     */
    function withConfig(Config $config)
    {
        return [
            'view_renderer' => [
                /** @see ViewRenderStrategy::getDefaultLayout */
                'default_layout'   => 'default',

                /** @see onErrorListener::__invoke */
                'error_view_template' => [
                    ## full name of class exception

                    ## use null on second index cause view template render as final layout
                    // 'Exception' => ['error/error', null],
                    // 'Specific\Error\Exception' => ['error/spec', 'override_layout_name_here']

                    ## here (blank) is defined as default layout for all error pages
                    'Exception' => ['error/error', 'blank'],
                    'Poirot\Application\Exception\RouteNotMatchException' => 'error/404',
                ],
            ],
        ];
    }

    /**
     * Get Action Services
     *
     * @return ModuleActionsContainer
     */
    function getActions()
    {
        $moduleActions  = new ModuleActionsContainer(
            new ApplicationActionsBuilder
        );

        return $moduleActions;
    }

    /**
     * Resolve to service with name and type
     *
     * - arguments must has default values
     *
     * [code]
     * resolveToServices(iHRouter $router = null, $sapi = null, $other = null)
     * [/code]
     *
     * @param Container   $services
     * @param RChainStack $router
     *
     * @return void
     */
    function withContainerServices($services = null, $router = null)
    {
        /** @var PathAction $path */
        $path = $services->from('\module\application.action')->get('path');
        $path->setPath('app-assets', '$basePath/theme/www');

        // ...

        $this->__withHttpRouter($router);
    }

    /**
     * Setup Http Stack Router
     *
     * @param RChainStack $router
     *
     * @return void
     */
    protected function __withHttpRouter(RChainStack $router)
    {
        $router->addRoutes([
            'home'  => [
                'route'    => 'segment',
                ## 'override' => true, ## default is true
                'options' => [
                    'criteria'    => '/',
                    'exact_match' => true,
                ],
                'params'  => [
                    '_then_' => [
                        ## chain actions
                        '/module/application.action/HomeInfo',
                        '/module/application.action/RenderContent',
                    ],
                ],
            ],
        ]);
    }
}
