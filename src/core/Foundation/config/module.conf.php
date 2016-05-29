<?php
$themesFolder = basename(PT_DIR_THEME_DEFAULT);

return array(
    // Path Helper Action Options
    'path_statics' => array(
        'paths' => array(
            'app-assets' => "\$basePath/{$themesFolder}/www",
        ),
    ),

    // Asset Manager Module Config (for enabled AssetManager module)
    'asset_manager' => array(
        'Poirot\Loader\AggregateLoader' => array(
            'attach' => array(
                100 => new \Poirot\Loader\LoaderNamespaceStack(array($themesFolder => array(PT_DIR_THEME_DEFAULT))),
            )
        )
    ),

    // View Renderer Options
    'view_renderer' => array(
        'default_layout'   => 'default',

        /** @see onErrorListener::__invoke */
        'error_view_template' => array(
            ## full name of class exception

            ## use null on second index cause view template render as final layout
            // 'Exception' => ['error/error', null],
            // 'Specific\Error\Exception' => ['error/spec', 'override_layout_name_here']

            ## here (blank) is defined as default layout for all error pages
            'Exception' => array('error/error', 'blank'),
            'Poirot\Application\Exception\RouteNotMatchException' => 'error/404',
        ),
    ),
);
