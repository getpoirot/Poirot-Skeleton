<?php
namespace Application\Actions\Helper;

use Poirot\AaResponder\AbstractAResponder;
use Poirot\Application\Sapi\Module\ModuleActionsContainer;
use Poirot\Container\Interfaces\iContainer;
use Poirot\Container\Interfaces\Respec\iCServiceAware;
use Poirot\Router\Http\HAbstractChainRouter;

class UrlAction extends AbstractAResponder
    implements iCServiceAware
{
    /** @var HAbstractChainRouter */
    protected $_router;
    /** @var HAbstractChainRouter */
    protected $_routeMatch;
    /** @var ModuleActionsContainer */
    protected $_sContainer;

    protected $_c__lastInvokedRouter;

    /**
     * Generates an url given the name of a route
     *
     * @inheritdoc
     *
     * @return mixed
     */
    function __invoke($routeName = null, $params = [])
    {
        if ($this->_router === null )
            throw new \RuntimeException('No RouteStackInterface instance provided');

        if ($routeName === null)
            ## using matched route
            $router = $this->getMatchedRoute();
        else
            $router = $this->_router->explore($routeName);

        if ($router === false)
            throw new \Exception(sprintf(
                'Cant explore to router (%s).'
                , ($routeName === null) ? 'MatchedRoute' : $routeName
            ));

        $this->_c__lastInvokedRouter = [$router, $params];

        return $this;
    }

    function uri()
    {
        // TODO using internal cache
        $router = $this->_c__lastInvokedRouter[0];
        $params = $this->_c__lastInvokedRouter[1];

        return $router->assemble($params);
    }

    /**
     * Attain Route Match
     * @return HAbstractChainRouter
     */
    function getMatchedRoute()
    {
        if ($this->_routeMatch)
            return $this->_routeMatch;

        /** @var \Poirot\Http\Message\HttpRequest $request */
        // TODO fresh because route (RSegment) manipulate meta DataFiled and must be reset
        $request = $this->_sContainer->from('/')->fresh('request');
        $router  = $this->_router;

        $this->_routeMatch = $router->match($request);
        return $this->_routeMatch;
    }

    function __toString()
    {
        try {
            $return = $this->uri()->toString();
        } catch (\Exception $e)
        {
            $return = $e->getMessage();
        }

        return $return;
    }

    /**
     * Set Router
     *
     * @param $router
     *
     * @return $this
     */
    function setRouter($router)
    {
        $this->_router = $router;

        return $this;
    }

    /**
     * Set Route Match
     *
     * @param $routeMatch
     *
     * @return $this
     */
    function setRouteMatch($routeMatch)
    {
        $this->_routeMatch = $routeMatch;

        return $this;
    }


    // ...

    /**
     * Set Service Container
     *
     * @param ModuleActionsContainer|iContainer $container
     */
    function setServiceContainer(iContainer $container)
    {
        $this->_sContainer = $container;
    }
}
 