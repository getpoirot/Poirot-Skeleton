<?php
namespace Module\Foundation\Actions;

use Module\Foundation\aActionSapi;
use Poirot\Stream\Streamable;

class HomeInfo extends aActionSapi
{
    const REPO_URL = 'https://api.github.com/orgs/phpoirot/events';

    /**
     * - Route Params Will Resolved As Argument On Invoke
     *   you can use param name if needed.
     */
    function exec()
    {
        // TODO load latest events as ajax widget on view

        # grab latest event from github repository
        $client = new WrapperClient(self::REPO_URL , null, new HttpContext([
            'http' => ['user_agent' => $_SERVER['HTTP_USER_AGENT']]
        ]));

        $resource = $client->getConnect();
        $stream   = new Streamable($resource);
        $events   = json_decode($stream->read());

        return ['events' => $events];
    }
}
