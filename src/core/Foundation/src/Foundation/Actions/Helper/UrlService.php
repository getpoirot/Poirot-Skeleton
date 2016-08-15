<?php
namespace Module\Foundation\Actions\Helper;

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

        $router  = $services->get('/router');

        $rAction = new UrlAction;
        $rAction->setRouter($router);

        if ($services->has('/router.match')) {
            /** @see onRouteMatchListener */
            $matched = $services->get('/router.match');
            $rAction->setRouteMatch($matched);
        }

        return $rAction;
    }
}
