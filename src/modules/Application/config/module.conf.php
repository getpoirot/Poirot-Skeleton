<?php
$themesFolder = basename(PT_DIR_THEME_DEFAULT);

return [
    // Path Helper Action Options
    'path_statics' => [
        'paths' => [
            'app-assets' => "\$basePath/{$themesFolder}/www",
        ],
    ],

    // Asset Manager Module Config (for enabled AssetManager module)
    'asset_manager' => [
        'Poirot\Loader\AggregateLoader' => [
            'attach' => [
                100 => new \Poirot\Loader\PathStackResolver([$themesFolder => [PT_DIR_THEME_DEFAULT]]),
            ]
        ]
    ],

    // View Renderer Options
    'view_renderer' => [
        'default_layout'   => 'default',

        /** @see onErrorListener::__invoke */
        'error_view_template' => [
            ## full name of class exception

            ## use null on second index cause view template render as final layout
            // 'Exception' => ['error/error', null],
            // 'Specific\Error\Exception' => ['error/spec', 'override_layout_name_here']

            ## here (blank) is defined as default layout for all error pages
            'Exception' => ['error/error', 'blank'],
            'Poirot\Application\Exception\RouteNotMatchException' => 'error/404',
        ],
    ],
];
