<?php
namespace Module\Foundation\Actions;

use Poirot\Http\Interfaces\iHeader;
use Poirot\Http\Interfaces\iHttpRequest;

class ParseRequestData extends aAction
{
    /**
     * Parse Request Body Data
     *
     * @param iHttpRequest $request
     *
     * @return array['request_data' => \Traversable]
     */
    function __invoke(iHttpRequest $request = null)
    {
        $header = $request->headers()->get('content-type');
        $contentType = '';
        /** @var iHeader $h */
        foreach ($header as $h)
            $contentType .= $h->renderValueLine();

        $contentType = strtolower($contentType);


        # Post Data:
        $parsedData = array();

        switch ($contentType)
        {
            case 'application/json':
                $parsedData = $request->getBody();
                $parsedData = json_decode($parsedData, true);
                break;

            case 'application/x-www-form-urlencoded':
            case strpos($contentType, 'multipart') !== false:
                $parsedData = \Poirot\Http\HttpMessage\Request\Plugin\PhpServer::_($request)->getPost();
                break;

            default:
        }

        return array('request_data' => $parsedData);
    }
}
