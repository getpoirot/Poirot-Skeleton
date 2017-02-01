<?php
namespace Module\Foundation\Actions\Helper;

use Module\Foundation\Actions\aAction;
use Poirot\Application\Sapi\Module\ContainerForFeatureActions;
use Poirot\Application\Sapi\Server\Http\BuildHttpSapiServices;
use Poirot\Http\Psr\ServerRequestBridgeInPsr;
use Poirot\Router\Interfaces\iRoute;
use Poirot\Router\Interfaces\iRouterStack;
use Poirot\Router\RouterStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;


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
     * - if given route name cause to resolve to currently route match,
     *   the route object has matched parameters from route injected.
     *
     *
     * @param null|string  $routeName              If not given use current matched route name
     * @param array        $params                 Route Assemble Params
     * @param bool         $preserveCurrentRequest Use current request query params?!!
     *
     * @return mixed
     * @throws \Exception
     */
    function __invoke($routeName = null, $params = array(), $preserveCurrentRequest = false)
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

        $router = clone $router;
        if (!$preserveCurrentRequest)
            // clean current route injected params
            $router->params()->clean();

        $this->_c__lastInvokedRouter = array($router, $params, $preserveCurrentRequest);
        return $this;
    }

    /**
     * Assemble Route as URI
     *
     * @return UriInterface
     */
    function uri()
    {
        // TODO using internal cache
        $router = $this->_c__lastInvokedRouter[0];
        /** @var iRouterStack $router */
        if ($params = $this->_c__lastInvokedRouter[1])
            $uri = $router->assemble($params);
        else
            $uri = $router->assemble();

        if ($preserve = $this->_c__lastInvokedRouter[2]) {
            $request = $this->_getRequest();
            $request = $request->getRequestTarget();
            if ($query = parse_url($request, PHP_URL_QUERY))
                $uri = \Poirot\Psr7\modifyUri($uri, array(
                    'query' => $query
                ));
        }

        return $uri;
    }

    /**
     * Attain Route Match
     * @return iRoute
     */
    function getMatchedRoute()
    {
        if ($this->_routeMatch)
            return $this->_routeMatch;

        $router            = $this->_router;
        $this->_routeMatch = $router->match($this->_getRequest());
        return $this->_routeMatch;
    }

    function __toString()
    {
        try {
            $return = (string) $this->uri();
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


    // ..

    /**
     * @return RequestInterface
     */
    function _getRequest()
    {
        $request = $this->services()->from('/')->get(BuildHttpSapiServices::SERVICE_NAME_REQUEST);
        return new ServerRequestBridgeInPsr($request);
    }
}