<?php
namespace Application;

use Application\Actions\ApplicationActionsBuilder;
use Poirot\Application\Interfaces\iApplication;
use Poirot\Application\Interfaces\Sapi\iSapiModule;
use Poirot\Application\Sapi;
use Poirot\Application\Sapi\Module\ModuleActionsContainer;
use Poirot\Container\Container;
use Poirot\Loader\Autoloader\AggregateAutoloader;
use Poirot\Router\Http\RChainStack;

class Module implements iSapiModule
    , Sapi\Module\Feature\InitializeSapiAppFeature
    , Sapi\Module\Feature\ServiceContainerFeature
    , Sapi\Module\Feature\AutoloadFeature
    , Sapi\Module\Feature\ActionProviderFeature
    , Sapi\Module\Feature\ResolveToServicesFeature
{
    /**
     * Init Module Against Application
     *
     * priority: 1000
     *
     * @param iApplication $app Application Instance
     *
     * @throws \Exception
     * @return void
     */
    function Initialize(iApplication $app)
    {
        if (!$app instanceof \Poirot\Application\Sapi)
            throw new \Exception('This module is not compatible with application.');
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
        $services->extend('path', '\application.actions\path');
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
        $autoloader->loader('Poirot\Loader\Autoloader\NamespaceAutoloader')
            ->setStack(__NAMESPACE__, __DIR__);
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
    function resolveToServices($services = null, $router = null)
    {
        $this->__withHttpRouter($router);
    }

    /**
     * Setup Http Stack Router
     *
     * @param RChainStack $router
     *
     * @return void
     */
    function __withHttpRouter(RChainStack $router)
    {
        $router->addRoutes([
            'home'  => [
                'route'   => 'segment',
                'options' => [
                    'criteria'    => '/',
                    'exact_match' => true,
                ],
            ],
        ]);
    }
}
