<?php
namespace Module\Foundation\Actions\Helper;

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
        $viewModel = $rootSrv->fresh('viewModel');

        $view = new ViewAction;
        $view->setViewModel($viewModel);

        return $view;
    }
}
