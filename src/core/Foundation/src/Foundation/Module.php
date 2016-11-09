<?php
namespace Module\Foundation;

use Poirot\Application\Interfaces\iApplication;
use Poirot\Application\Interfaces\Sapi\iSapiModule;
use Poirot\Application\aSapi;
use Poirot\Application\Interfaces\Sapi;
use Poirot\Application\Sapi\Module\ContainerForFeatureActions;
use Poirot\Application\Sapi\Server\Http\BuildHttpSapiServices;
use Poirot\Application\Sapi\Server\Http\ListenerDispatch;
use Poirot\Application\SapiCli;
use Poirot\Application\SapiHttp;

use Poirot\Ioc\Container;
use Poirot\Ioc\Container\BuildContainer;

use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
use Poirot\Loader\Interfaces\iLoaderAutoload;
use Poirot\Loader\LoaderAggregate;
use Poirot\Loader\LoaderNamespaceStack;

use Poirot\Router\BuildRouterStack;
use Poirot\Router\Interfaces\iRouterStack;

use Poirot\Std\Interfaces\Struct\iDataEntity;

use Module\Foundation\Actions\BuildContainerActionOfFoundationModule;
use Module\Foundation\HttpSapi\ViewModelRenderer;


class Module implements iSapiModule
    , Sapi\Module\Feature\iFeatureModuleInitSapi
    , Sapi\Module\Feature\iFeatureModuleAutoload
    , Sapi\Module\Feature\iFeatureModuleInitServices
    , Sapi\Module\Feature\iFeatureModuleNestActions
    , Sapi\Module\Feature\iFeatureOnPostLoadModulesGrabServices
    , Sapi\Module\Feature\iFeatureModuleMergeConfig
{
    /** @var SapiHttp|SapiCli */
    protected $sapi;

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

        $this->sapi = $sapi;
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
        $nameSpaceLoader = $baseAutoloader->loader($nameSpaceLoader);
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
        return \Poirot\Config\load(__DIR__ . '/../../config/mod-foundation');
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
        // replace default renderer with Foundation Renderer including stuffs
        if ($this->sapi instanceof SapiHttp) {
            $this->sapi->services()->set(new Container\Service\ServiceInstance(
                BuildHttpSapiServices::SERVICE_NAME_VIEW_MODEL_RENDERER
                , new ViewModelRenderer
            ));
        }
    }

    /**
     * Get Action Services
     *
     * priority not that serious
     *
     * - return Array used to Build ModuleActionsContainer
     *
     * @return array|ContainerForFeatureActions|BuildContainer|\Traversable
     */
    function getActions()
    {
        return new BuildContainerActionOfFoundationModule;
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
     * @param aSapi                          $sapi
     * @param iRouterStack                   $router
     * @param LoaderAggregate                $viewModelResolver
     *
     * @internal param null $services service names must have default value
     */
    function resolveRegisteredServices(
        $sapi = null
        , $router = null
        , $viewModelResolver = null
    ) {
        if ($this->sapi instanceof SapiHttp) 
        {
            // This is Http Sapi Application

            # Attach Module Scripts To View Resolver:
            
            // TODO Define ViewModelResolver within ViewModel or better!! all view services may not have resolver
            // But We May Need Template Rendering Even In API Calls
            /** @var LoaderNamespaceStack $resolver */
            $resolver = $viewModelResolver->loader('Poirot\Loader\LoaderNamespaceStack');
            $resolver->with(array(
                    'main/home' => __DIR__. '/../../view/main/home',
                    'partial'   => __DIR__.'/../../view/partial',
                ));

            # Register Routes:
            $this->_setupHttpRouter($router);
        }
    }

    /**
     * Setup Http Stack Router
     *
     * @param iRouterStack $router
     *
     * @return void
     */
    protected function _setupHttpRouter(iRouterStack $router)
    {
        $buildRoute = new BuildRouterStack();
        $buildRoute->setRoutes(array(
            'home'  => array(
                'route'    => 'RouteSegment',
                ## 'allow_override' => true, ## default is true
                'options' => array(
                    'criteria'    => '/',
                    'match_whole' => true,
                ),
                'params'  => array(
                    ListenerDispatch::CONF_KEY => function() { return array(); },
                ),
            ),
        ));
        
        $buildRoute->build($router);
    }
}
