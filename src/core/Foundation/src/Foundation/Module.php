<?php
namespace Module\Foundation;

use Poirot\Application\Interfaces\iApplication;
use Poirot\Application\Interfaces\Sapi\iSapiModule;
use Poirot\Application\aSapi;
use Poirot\Application\Sapi;
use Poirot\Application\Sapi\Module\ContainerForFeatureActions;

use Poirot\Ioc\Container;

use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
use Poirot\Loader\Interfaces\iLoaderAutoload;
use Poirot\Loader\LoaderNamespaceStack;

use Poirot\Router\RouterStack;

use Poirot\Std\Interfaces\Struct\iDataEntity;

use Module\Foundation\Actions\BuildContainerActionOfFoundationModule;

class Module implements iSapiModule
    , Sapi\Module\Feature\FeatureModuleInitSapi
    , Sapi\Module\Feature\FeatureModuleAutoload
    , Sapi\Module\Feature\FeatureModuleInitServices
    , Sapi\Module\Feature\FeatureModuleNestActions
    , Sapi\Module\Feature\FeatureOnPostLoadModulesGrabServices
    , Sapi\Module\Feature\FeatureModuleMergeConfig
{
    /**
     * Init Module Against Application
     *
     * - determine sapi server, cli or http
     *
     * priority: 1000 A
     *
     * @param iApplication|aSapi $sapi Application Instance
     *
     * @throws \Exception
     */
    function initialize($sapi)
    {
        // init requirements
        if (!getenv('HTTP_MOD_REWRITE'))
            throw new \RuntimeException('It seems that you don`t have "MOD_REWRITE" enabled on the server.');

        if (!$sapi instanceof \Poirot\Application\aSapi)
            throw new \Exception('This module is not compatible with this sapi application.');
    }

    /**
     * Register class autoload on Autoload
     *
     * priority: 1000 B
     *
     * @param LoaderAutoloadAggregate $baseAutoloader
     *
     * @return iLoaderAutoload|array|\Traversable|void
     */
    function initAutoload(LoaderAutoloadAggregate $baseAutoloader)
    {
        #$nameSpaceLoader = \Poirot\Loader\Autoloader\LoaderAutoloadNamespace::class;
        $nameSpaceLoader = 'Poirot\Loader\Autoloader\LoaderAutoloadNamespace';
        /** @var LoaderAutoloadNamespace $nameSpaceLoader */
        $nameSpaceLoader = $baseAutoloader->by($nameSpaceLoader);
        $nameSpaceLoader->addResource(__NAMESPACE__, __DIR__);
    }

    /**
     * Register config key/value
     *
     * priority: 1000 D
     *
     * - you may return an array or Traversable
     *   that would be merge with config current data
     *
     * @param iDataEntity $config
     *
     * @return array|\Traversable
     */
    function initConfig(iDataEntity $config)
    {
        return include __DIR__.'/../../config/module.conf.php';
    }

    /**
     * Build Service Container
     *
     * priority: 1000 X
     *
     * - register services
     * - define aliases
     * - add initializers
     * - ...
     *
     * @param Container $services
     *
     * @return array|void Container Builder Config
     */
    function initServices(Container $services)
    {
        // replace default renderer with Application renderer including stuffs
        // ..
    }

    /**
     * Get Action Services
     *
     * priority not that serious
     *
     * - return Array used to Build ModuleActionsContainer
     *
     * @return array|\Traversable|ContainerForFeatureActions
     */
    function getActions()
    {
        $moduleActions  = new ContainerForFeatureActions(
            new BuildContainerActionOfFoundationModule
        );

        return $moduleActions;
    }

    /**
     * Resolve to service with name
     *
     * - each argument represent requested service by registered name
     *   if service not available default argument value remains
     * - "services" as argument will retrieve services container itself.
     *
     * ! after all modules loaded
     *
     * @param null $services service names must have default value
     */
    function resolveRegisteredServices(
        $sapi = null
        , $router = null
        , $viewModelResolver = null
        , $viewRenderStrategy = null
        , $AssetManager = null
    ) {

        return;

        $config = $sapi->config();

        // Set Default Template Name From Config ----------------------------------------------------\
        ($viewRenderStrategy !== null) ?: $viewRenderStrategy->setDefaultLayout(
            $config->get('view_renderer')['default_layout']
        );

        // Register Module Default View Scripts Path To View Resolver -------------------------------\
        $viewModelResolver->attach(new LoaderNamespaceStack(array(
            'site/home' => array(__DIR__.'/../../view/site/home'),
            'partial'   => array(__DIR__.'/../../view/partial'),
        )));

        # Register Routes:
        $this->_setupRouter($router);
    }

    /**
     * Setup Http Stack Router
     *
     * @param RouterStack $router
     *
     * @return void
     */
    protected function _setupRouter(RouterStack $router)
    {
        $router->addRoutes(array(
            'home'  => array(
                'route'    => 'segment',
                ## 'override' => true, ## default is true
                'options' => array(
                    'criteria'    => '/',
                    'exact_match' => true,
                ),
                'params'  => array(
                    ## use last action result or merged params
                    ## default is params
                    # '_use_' => 'params',
                    '_then_' => array(
                        ## chain actions
                        '/module/application.action/HomeInfo',
                        '/module/application.action/RenderContent',
                    ),
                ),
            ),
        ));
    }
}
