<?php
namespace Application\Actions;

use Poirot\Container\Service\AbstractService;

class PathService extends AbstractService
{
    /**
     * @var string Service Name
     */
    protected $name = 'path';

    /**
     * Create Service
     *
     * @return mixed
     */
    function createService()
    {
        return new PathAction;
    }
}
 