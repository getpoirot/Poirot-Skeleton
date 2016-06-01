<?php
namespace Module\Foundation\Actions;

use Poirot\Stream\Streamable;

class HomeInfo
    extends aAction
{
    const REPO_URL = 'https://api.github.com/orgs/phpoirot/events';

    /**
     * - Route Params Will Resolved As Argument On Invoke
     *   you can use param name if needed.
     */
    function __invoke()
    {
        // TODO load latest events as ajax widget on view

        # grab latest event from github repository
        $events = file_get_contents(self::REPO_URL);
        $events = json_decode($events);

        return array('events' => $events);
    }
}
