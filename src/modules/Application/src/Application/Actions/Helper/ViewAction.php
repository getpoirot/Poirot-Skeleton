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
    function exec($template = null, $variables = null)
    {
        #! view must be immutable
        $viewModel = clone $this->viewModel;

        if ($template !== null)
            $viewModel->setTemplate($template);

        if ($variables)
            $viewModel->variables()->from($variables);

        #! view helper action is immutable
        return new self(['view_model' => $viewModel]);
    }

    /**
     * Proxy To View Model Render
     *
     * ! to avoid echo $view->render() that output twice
     *
     * @return string
     */
    function render()
    {
        return $this->viewModel->render();
    }

    function __toString()
    {
        try {
            $rendered = $this->render();
        } catch (\Exception $e) {
            ## avoid exception error on __toString, display exception within html body
            $rendered = $this->__renderException($e);
        }

        return $rendered;
    }

    /**
     * Proxy all method calls to ViewModel
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
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

    protected function __renderException($e)
    {
        $eClass = get_class($e);
        return <<<HTML
        <h3>{$eClass}</h3>
        <dl style="direction: ltr">
            <dt>File:</dt>
            <dd>
                <pre class="prettyprint linenums">{$e->getFile()}:{$e->getLine()}</pre>
            </dd>
            <dt>Message:</dt>
            <dd>
                <pre class="prettyprint linenums">{$e->getMessage()}</pre>
            </dd>
        </dl>
HTML;

    }
}
