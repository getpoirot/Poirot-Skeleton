<?php
namespace Module\Foundation\Actions\Helper;

use Poirot\Container\Service\AbstractService;

class ViewService extends AbstractService
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
    function createService()
    {
        $rootSrv   = $this->services()->from('/');
        $viewModel = $rootSrv->fresh('viewModel');

        $view = new ViewAction;
        $view->setViewModel($viewModel);

        return $view;
    }
}
 