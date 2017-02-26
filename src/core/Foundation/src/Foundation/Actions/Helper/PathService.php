<?php
namespace Module\Foundation\Actions\Helper;

use Poirot\Application\Sapi\Server\Http\BuildHttpSapiServices;
use Poirot\Http\HttpMessage\Request\Plugin\PhpServer;
use Poirot\Http\HttpRequest;
use Poirot\Http\Interfaces\iHttpRequest;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;
use Poirot\Std\Type\StdArray;


class PathService
    extends aServiceContainer
{
    const CONF_KEY = 'module.foundation.path-service';
    
    const PARAM_SERVER_URL = 'serverUrl';
    const PARAM_BASE_PATH  = 'basePath';
    const PARAM_BASE_URL   = 'baseUrl';
    
         
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
        $pathAction = new PathAction;

        
        # register default paths and variables
        $self = $this;
        ## serverUrl
        $pathAction->variables()->set(self::PARAM_SERVER_URL, function() use ($self) {
            return $self->_getServerUrl();
        });
        ## basePath
        $pathAction->variables()->set(self::PARAM_BASE_PATH, function() use ($self) {
            return $self->_getBasePath();
        });
        ## baseUrl
        $pathAction->variables()->set(self::PARAM_BASE_URL, function() use ($self) {
            return $self->_getBaseUrl();
        });
         
        # build with merged config
        /** @var DataEntity $config */
        $services = $this->services();
        $config = $services->get('/sapi')->config();
        $config = $config->get(self::CONF_KEY, array());
        // strip null values from config
        $stdTrav = new StdArray($config);
        $config  = $stdTrav->withWalk(function($val) {
            return $val === null; // null values not saved
        }, true);

        // Rewrite Default Variables If Config Set
        $pathAction->with($pathAction::parseWith($config));
        return $pathAction;
    }
    
    
    // ..
    
    protected function _getServerUrl()
    {
        $request = $this->__attainHttpRequest();
        // TODO get protocol (http|https)
        $server  = 'http://'.$request->getHost();
        return rtrim($server, '/');
    }

    protected function _getBasePath()
    {
        $request  = $this->__attainHttpRequest();
        $basePath = PhpServer::_($request)->getBasePath();
        return rtrim($basePath, '/');
    }

    protected function _getBaseUrl()
    {
        $request  = $this->__attainHttpRequest();
        if ($request->headers()->has('X-Poirot-Base-Url')) {
            // Retrieve Base Url From Server Proxy Passed By Header
            $fromProxy = '';
            /** @var iHeader $h */
            foreach ($request->headers()->get('X-Poirot-Base-Url') as $h)
                $fromProxy .= $h->renderValueLine();
        } 
        if (isset($fromProxy)) {
            $basePath = ($fromProxy == 'no-value') ? '/' : $fromProxy;
        } elseif (getenv('PT_BASEURL')) {
            // From Environment Variable
            $basePath = getenv('PT_BASEURL');
        } else {
            $basePath = PhpServer::_($request)->getBaseUrl();
        }

        return rtrim($basePath, '/');
    }
    
    /** @return iHttpRequest */
    protected function __attainHttpRequest()
    {
        /** @var HttpRequest $request */
        $services = $this->services();
        $request  = $services->get('/'.BuildHttpSapiServices::SERVICE_NAME_REQUEST);
        return $request;
    }
}
