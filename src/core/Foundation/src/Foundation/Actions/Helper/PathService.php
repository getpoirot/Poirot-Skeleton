<?php
namespace Module\Foundation\Actions\Helper;

use Poirot\Application\Sapi\Server\Http\BuildHttpSapiServices;
use Poirot\Http\HttpMessage\Request\Plugin\PhpServer;
use Poirot\Http\HttpRequest;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;

class PathService
    extends aServiceContainer
{
    const CONF_KEY = 'module.foundation.path-service';
    
    /**
     * @var string Service Name
     */
    protected $name = 'path';

    /**
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        /** @var DataEntity $config */
        $services = $this->services();
        $config = $services->get('/sapi')->config();
        $config = $config->get(self::CONF_KEY, array());

        $pathAction = new PathAction($config);

        # register default paths and variables
        $self = $this;
        ## server url
        $pathAction->params()->set('serverUrl', function() use ($self) {
            return $self->_getServerUrl();
        });
        ## base path
        $pathAction->params()->set('basePath', function() use ($self) {
            return $self->_getBasePath();
        });

        return $pathAction;
    }
    
    protected function _getServerUrl()
    {
        // TODO
        
        $server = 'http://localhost:8080';
        return $server;
    }

    protected function _getBasePath()
    {
        /** @var HttpRequest $request */
        $services = $this->services();
        $request  = $services->get('/'.BuildHttpSapiServices::SERVICE_NAME_REQUEST);
        $basePath = PhpServer::_($request)->getBasePath();
        return $basePath;
    }
}
