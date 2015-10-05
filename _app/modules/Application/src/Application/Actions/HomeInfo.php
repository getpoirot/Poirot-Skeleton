<?php
namespace Application\Actions;

use Application\AbstractSapiAction;

class HomeInfo extends AbstractSapiAction
{
    /**
     * @inheritdoc
     */
    function __invoke($arg = null)
    {
        ## force to render view script page layout
        return [];
    }
}
