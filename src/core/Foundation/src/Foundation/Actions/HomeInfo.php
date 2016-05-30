<?php
namespace Module\Foundation\Actions;

use Poirot\Stream\Context\ContextStreamHttp;
use Poirot\Stream\Streamable;
use Poirot\Stream\StreamWrapperClient;

class HomeInfo 
    extends aAction
{
    const REPO_URL = 'https://api.github.com/orgs/phpoirot/events';

    /**
     * - Route Params Will Resolved As Argument On Invoke
     *   you can use param name if needed.
     */
    function doInvoke()
    {
        // TODO load latest events as ajax widget on view

        # grab latest event from github repository
        $client = new StreamWrapperClient(self::REPO_URL , null, new ContextStreamHttp(array(
            'http' => array('user_agent' => $_SERVER['HTTP_USER_AGENT'])
        )));

        $resource = $client->getConnect();
        $stream   = new Streamable($resource);
        $events   = json_decode($stream->read());

        return array('events' => $events);
    }
}
