<?php

/** @var \Module\Foundation\HttpSapi\ViewModelRenderer $this */
$_f__ = function () use (&$events) {
    if (!$events)
        ## events not exists
        return;

    # hide extra variables from scope
    $_events = []; $i = 0;
    foreach ($events as $event) {
        if ($i++ >= 9)
            break;

        if ($event->type != 'PushEvent')
            ## only list latest commits
            continue;

        # Repository Name
        $repoName = $event->repo->name;
        $event->repo->name = substr($repoName, strpos($repoName, '/')+1);

        # Commit Info
        $author = current($event->payload->commits);
        $author = $author->author;
        $event->payload->author = $author;

        # Message
        $message = current($event->payload->commits);
        $message = $message->message;
        $event->payload->message = $message;

        array_push($_events, $event);
    }

    $events = $_events;
};

$_f__->__invoke();

// put section features into theme as widget
## render partial section feature from view script belong to application module
## @see section-features.phtml
$section_features = $this->action()->view('partial/section-features');

return array(
    'section_features' => $section_features
); // variables to merge with absolute view variables
