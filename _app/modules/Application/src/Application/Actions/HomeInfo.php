<?php
namespace Application\Actions;

use Application\AbstractSapiAction;
use Poirot\Http\Interfaces\Message\iHttpRequest;
use Poirot\Http\Interfaces\Message\iHttpResponse;
use Poirot\Http\Interfaces\Respec\iRequestAware;
use Poirot\Http\Interfaces\Respec\iResponseAware;
use Poirot\View\PermutationViewModel;

class HomeInfo extends AbstractSapiAction
    implements iRequestAware
    , iResponseAware
{
    /** @var iHttpRequest */
    protected $request;
    /** @var iHttpResponse */
    protected $response;

    /**
     * Invoke Action
     *
     * - store invoke return result into ::params entity
     *   with ::getParamsKey
     *
     * @param null $request_page
     *
     * @return mixed
     */
    function __invoke($request_page = null)
    {
        /** @var PermutationViewModel $viewScript */
        $viewScript = $this->services()->from('/')->get('viewRenderStrategy')->getScriptView();

        // $viewScript->setFinal(); ## set as final, no template decorator

        switch($request_page) {
            case 'about':
            case 'contact':
                $viewScript->setTemplate($request_page);
                break;

            default:
                ## let it fall back to view renderer strategy
        }

        return [
            'request_page' => $request_page,
        ];
    }

    /**
     * Set Request
     *
     * @param iHttpRequest $request
     *
     * @return $this
     */
    function setRequest(iHttpRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set Response
     *
     * @param iHttpResponse $response
     *
     * @return $this
     */
    function setResponse(iHttpResponse $response)
    {
        $this->response = $response;

        return $this;
    }
}
