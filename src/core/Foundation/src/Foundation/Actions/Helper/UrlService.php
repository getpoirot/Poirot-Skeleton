<?php
namespace Module\Foundation\Actions\Helper;

use Poirot\Application\Sapi\Server\Http\BuildHttpSapiServices;
use Poirot\Ioc\Container\Service\aServiceContainer;

class UrlService 
    extends aServiceContainer
{
    /**
     * @var string Service Name
     */
    protected $name = 'url';

    /**
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        $services = $this->services();

        $router  = $services->from('/')->get(BuildHttpSapiServices::SERVICE_NAME_ROUTER);

        $rAction = new UrlAction;
        $rAction->setRouter($router);

        if ($services->has('/router.match')) {
            /** @see onRouteMatchListener */
            // TODO its poirot foundation feature where "router.match is set?!!"
            $matched = $services->from('/')->get('router.match');
            $rAction->setRouteMatch($matched);
        }

        return $rAction;
    }
}
