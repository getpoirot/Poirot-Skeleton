<?php
namespace Application\Actions\Helper;

use Poirot\Container\Service\AbstractService;
use Poirot\Http\Interfaces\Message\iHttpRequest;
use Poirot\Http\Plugins\Request\PhpServer;

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
            $self = $this;
            ## server url
            $pathAction->params()->set('serverUrl', function() use ($self) {
                return $self->__getServerUrl();
            });
            ## base path
            $pathAction->params()->set('basePath', function() use ($self) {
                return $self->__getBasePath();
            });
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

    protected function __getBasePath()
    {
        /** @var \Poirot\Http\Message\HttpRequest $request */
        $request = $this->services()->from('/')->get('Request');

        $phpServer = new PhpServer(['message_object' => $request]);
        return $phpServer->getBasePath();
    }
}
 