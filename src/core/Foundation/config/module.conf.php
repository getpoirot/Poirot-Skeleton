<?php
$themesFolder = basename(PT_DIR_THEME_DEFAULT);

return array(
    // Path Helper Action Options
    \Module\Foundation\Actions\Helper\PathService::CONF_KEY
    => array(
        'paths' => array(
            'app-assets' => "\$basePath/{$themesFolder}/www",
        ),
    ),

    // View Renderer Options
    \Poirot\Application\Sapi\Server\Http\ViewRenderStrategy\ListenersRenderDefaultStrategy::CONF_KEY
    => array(
        'default_layout'   => 'default',

        \Poirot\Application\Sapi\Server\Http\ViewRenderStrategy\DefaultStrategy\ListenerError::CONF_KEY => array(
            ## full name of class exception

            ## use null on second index cause view template render as final layout
            // 'Exception' => ['error/error', null],
            // 'Specific\Error\Exception' => ['error/spec', 'override_layout_name_here']

            ## here (blank) is defined as default layout for all error pages
            'Exception' => array('error/error', 'blank'),
            'Poirot\Application\Exception\exRouteNotMatch' => 'error/404',
        ),
    ),
);
