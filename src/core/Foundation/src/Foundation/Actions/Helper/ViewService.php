<?php
namespace Module\Foundation\Actions\Helper;

use Poirot\Application\Sapi\Server\Http\BuildHttpSapiServices;
use Poirot\Ioc\Container\Service\aServiceContainer;

class ViewService 
    extends aServiceContainer
{
    /**
     * @var string Service Name
     */
    protected $name = 'view';

    /**
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        $rootSrv   = $this->services()->from('/');
        $viewModel = $rootSrv->fresh(BuildHttpSapiServices::SERVICE_NAME_VIEW_MODEL);

        $view = new ViewAction;
        $view->setViewModel($viewModel);

        return $view;
    }
}
