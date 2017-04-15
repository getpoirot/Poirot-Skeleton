<?php
namespace Module\Foundation\HttpSapi\RouterStack;


use Module\Foundation\Services\PathService\PathAction;
use Poirot\Router\Interfaces\RouterStack\iPreparatorRequest;
use Poirot\Router\RouterStack\StripPrefix;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;


class PreparatorHandleBaseUrl
    extends StripPrefix
    implements iPreparatorRequest
{
    protected $baseUrl;


    /**
     * StripPrefix constructor.
     *
     * @param PathAction $path @IoC /module/foundation/services/
     */
    function __construct(PathAction $path)
    {
        $baseUrl = $path->assemble('$baseUrl');
        if ($baseUrl && $baseUrl !== '/' ) {
            $this->baseUrl = $baseUrl;
            parent::__construct($baseUrl);
        }
    }

    /**
     * Prepare Request Object
     *
     * @param RequestInterface $request
     *
     * @return RequestInterface Clone
     */
    function withRequestOnMatch(RequestInterface $request)
    {
        if ($this->baseUrl === null)
            return $request;

        return parent::withRequestOnMatch($request);
    }

    /**
     *
     *
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    function withUriOnAssemble(UriInterface $uri)
    {
        if ($this->baseUrl === null)
            return $uri;

        return parent::withUriOnAssemble($uri);
    }
}
