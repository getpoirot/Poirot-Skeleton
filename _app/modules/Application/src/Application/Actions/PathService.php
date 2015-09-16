<?php
namespace Application\Actions;

use Poirot\Container\Service\AbstractService;
use Poirot\Http\Interfaces\Message\iHttpRequest;

class PathService extends AbstractService
{
    /**
     * @var string Service Name
     */
    protected $name = 'path';

    /**
     * Create Service
     *
     * @return mixed
     */
    function createService()
    {
        $pathAction = new PathAction;

        # register default paths and variables
        if ($this->services()->from('/')->get('request') instanceof iHttpRequest) {
            $pathAction->params()->set('serverUrl', $this->__getServerUrl());
        }

        return $pathAction;
    }

    protected function __getServerUrl()
    {
        /** @var \Poirot\Http\Message\HttpRequest $request */
        $request = $this->services()->from('/')->get('Request');
        $uri     = $request->getUri();

        $server  = '';
        (!$scheme = $uri->getScheme()) ?: $server .= $scheme.'://';
        (!$host   = $uri->getHost())   ?: $server .= $host;
        (!$port   = $uri->getPort())   ?: $server .= ':'.$port;

        return $server;
    }
}
 