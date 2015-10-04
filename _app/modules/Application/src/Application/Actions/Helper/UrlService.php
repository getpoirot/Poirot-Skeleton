<?php
namespace Application\Actions\Helper;

use Poirot\Container\Service\AbstractService;

class UrlService extends AbstractService
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
    function createService()
    {
        $rootSrv = $this->services()->from('/');

        $router  = $rootSrv->get('router');
        /** @see onRouteMatchListener */
        $matched = $rootSrv->get('route.match');

        $rAction = (new UrlAction)
            ->setRouter($router)
            ->setRouteMatch($matched)
        ;

        return $rAction;
    }
}
 