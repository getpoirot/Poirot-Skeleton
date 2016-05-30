<?php
namespace Module\Foundation\Actions;

/**
 * With Poirot\Application\Sapi\Server\Http\ViewRenderStrategy
 *
 * Use this action in routes cause to render viewScript page
 * in exp. for route /home => ['_then_' => '/module/application.action/RenderContent'
 * this will render site/home template from viewRenderer
 */

class RenderContent extends aAction
{
    function doInvoke()
    {
        ## force to render, every action that return variable will render
        return array();
    }
}
