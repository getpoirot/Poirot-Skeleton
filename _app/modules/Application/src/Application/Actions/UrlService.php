<?php
namespace Application\Actions;

use Poirot\Application\Sapi\Event\ApplicationEvents;
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
        /** @var ApplicationEvents $events */
        $events  = $rootSrv->get('sapi.events');
        $matched = $events->getMatchedRoute();

        $rAction = (new UrlAction)
            ->setRouter($router)
            ->setRouteMatch($matched)
        ;

        return $rAction;
    }
}
 