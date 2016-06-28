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
        /** @see onRouteMatchListener */
        $matched = $services->get('/router.match');

        $rAction = new UrlAction;
        $rAction->setRouter($router)
            ->setRouteMatch($matched);

        return $rAction;
    }
}
