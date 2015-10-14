<?php
namespace Application\Actions\Helper;

use Poirot\AaResponder\AbstractAResponder;
use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\View\Interfaces\iPermutationViewModel;
use Poirot\View\Interfaces\iViewModel;
use Poirot\View\PermutationViewModel;

/*
 * Render View Templates
 *
 * echo $this->action()->view('template', ['var' => $value])
 *
 * $this->action()->view()->setTemplate('template')->render()
 */

class ViewAction extends AbstractAResponder
{
    /** @var PermutationViewModel */
    protected $viewModel;

    /**
     * View Model Renderer Instance
     *
     * @param string|null            $template
     * @param array|iDataSetConveyor $variables
     *
     * @return iViewModel|PermutationViewModel|string
     */
    function __invoke($template = null, $variables = null)
    {
        $viewModel = $this->viewModel;

        if ($template !== null)
            $viewModel->setTemplate($template);

        if ($variables)
            $viewModel->variables()->from($variables);

        return $this;
    }

    function __toString()
    {
        return $this->viewModel->render();
    }

    function __call($method, $arguments)
    {
        return call_user_func_array([$this->viewModel, $method], $arguments);
    }

    /**
     * Set View Model
     *
     * @param iPermutationViewModel $viewModel
     *
     * @return $this
     */
    function setViewModel(iPermutationViewModel $viewModel)
    {
        $this->viewModel = $viewModel;

        return $this;
    }
}
