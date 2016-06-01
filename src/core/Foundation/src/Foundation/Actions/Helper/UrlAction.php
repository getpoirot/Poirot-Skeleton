<?php
namespace Module\Foundation\Actions\Helper;

use Module\Foundation\Actions\aAction;
use Poirot\Application\Sapi\Module\ContainerForFeatureActions;
use Poirot\Router\Interfaces\iRoute;
use Poirot\Router\RouterStack;

class UrlAction 
    extends aAction
{
    /** @var RouterStack */
    protected $_router;
    /** @var iRoute */
    protected $_routeMatch;
    /** @var ContainerForFeatureActions */
    protected $_sContainer;

    protected $_c__lastInvokedRouter;

    /**
     * Generates an url given the name of a route
     *
     * @inheritdoc
     *
     * @return mixed
     */
    function __invoke($routeName = null, $params = array())
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

        $this->_c__lastInvokedRouter = array($router, $params);

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
     * @return iRoute
     */
    function getMatchedRoute()
    {
        if ($this->_routeMatch)
            return $this->_routeMatch;

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
}
 