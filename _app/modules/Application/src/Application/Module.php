<?php
namespace Application;

use Poirot\Application\Sapi;

use Application\Actions\ApplicationActionsBuilder;
use Application\HttpSapi\ViewModelRenderer;
use Poirot\Application\Interfaces\iApplication;
use Poirot\Application\Interfaces\Sapi\iSapiModule;
use Poirot\Application\AbstractSapi;
use Poirot\Application\Sapi\Module\ModuleActionsContainer;
use Poirot\Container\Container;
use Poirot\Container\Service\InstanceService;
use Poirot\Core\Interfaces\EntityInterface;
use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Loader\AggregateLoader;
use Poirot\Loader\Autoloader\AggregateAutoloader;
use Poirot\Loader\PathStackResolver;
use Poirot\Router\Http\RChainStack;

class Module implements iSapiModule
    , Sapi\Module\Feature\InitializeSapiFeature
    , Sapi\Module\Feature\AutoloadFeature
    , Sapi\Module\Feature\ServiceContainerFeature
    , Sapi\Module\Feature\ActionProviderFeature
    , Sapi\Module\Feature\PostLoadModulesServicesFeature
    , Sapi\Module\Feature\ConfigFeature
{
    /**
     * Init Module Against Application
     *
     * priority: 1000
     *
     * @param iApplication|AbstractSapi $app Application Instance
     *
     * @throws \Exception
     * @return void
     */
    function Initialize(iApplication $app)
    {
        // init requirements
        if (!getenv('HTTP_MOD_REWRITE'))
            throw new \RuntimeException('It seems that you don\'t have "MOD_REWRITE" enabled on the server.');

        if (!$app instanceof \Poirot\Application\AbstractSapi)
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
     * Register config key/value
     * @see InitModuleListener
     *
     * priority: 800
     *
     * - you may return an array or iDataSetConveyor
     *   that would be merge with config current data
     *
     * @param EntityInterface $config
     *
     * @return array|iDataSetConveyor
     */
    function withConfig(EntityInterface $config)
    {
        return include __DIR__.'/../../config/module.conf.php';
    }

    /**
     * Build Service Container
     *
     * priority: 700
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
     * Get Action Services
     * @see onAttainModuleActionsListener
     *
     * priority 400
     *
     * - return Array used to Build ModuleActionsContainer
     *
     * @return array|ModuleActionsContainer
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
     * ! after all modules loaded
     *
     * - arguments must has default values
     *
     * [code]
     * resolveToServices(iHRouter $router = null, $sapi = null, $other = null)
     * [/code]
     *
     * @param AbstractSapi                        $sapi
     * @param RChainStack                         $router
     * @param AggregateLoader                     $viewModelResolver
     * @param Sapi\Server\Http\ViewRenderStrategy $viewRenderStrategy
     *
     * @throws \Exception
     */
    function onModulesLoadedWithServices(
        $sapi = null
        , $router = null
        , $viewModelResolver = null
        , $viewRenderStrategy = null
        , $AssetManager = null
    ) {
        $config = $sapi->config();

        // Set Default Template Name From Config ----------------------------------------------------\
        ($viewRenderStrategy !== null) ?: $viewRenderStrategy->setDefaultLayout(
            $config->get('view_renderer')['default_layout']
        );

        // Register Module Default View Scripts Path To View Resolver -------------------------------\
        $viewModelResolver->attach(new PathStackResolver([
            'site/home' => [__DIR__.'/../../view/site/home'],
            'partial'   => [__DIR__.'/../../view/partial'],
        ]));

        # Register Routes:
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
                    ## use last action result or merged params
                    ## default is params
                    # '_use_' => 'params',
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
