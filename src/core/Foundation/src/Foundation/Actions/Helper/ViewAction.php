<?php
namespace Module\Foundation\Actions\Helper;

use Module\Foundation\Actions\aAction;
use Poirot\View\Interfaces\iViewModel;
use Poirot\View\ViewModelTemplate;

/*
 * Render View Templates
 *
 * echo $this->action()->view('template', ['var' => $value])
 *
 * $this->action()->view()->setTemplate('template')->render()
 */

class ViewAction 
    extends aAction
{
    /** @var ViewModelTemplate */
    protected $viewModel;

    /**
     * View Model Renderer Instance
     *
     * @param string|null        $template
     * @param array|\Traversable $variables
     *
     * @return iViewModel|ViewModelTemplate|string
     */
    function __invoke($template = null, $variables = null)
    {
        #! view must be immutable
        $viewModel = clone $this->viewModel;

        if ($template !== null)
            $viewModel->setTemplate($template);

        if ($variables)
            $viewModel->variables()->import($variables);

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
            $rendered = $this->_renderException($e);
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
     * @param iViewModel $viewModel
     *
     * @return $this
     */
    function setViewModel(iViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    protected function _renderException($e)
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
