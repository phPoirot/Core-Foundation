<?php
// TODO by this considered that themes always exists within www that seems ok when not using asset-manager

$themesFolder = trim(str_replace(PT_DIR_WWW, '', PT_DIR_THEME_DEFAULT), '/');

return [
    Poirot\Application\Sapi\Server\Http\Service\ServiceRouter::CONF_KEY
    => [
        // ( ! ) note: Change Config Of Router In Specific Case That You Aware Of It!!
        //             may corrupt routing behaviour

        // router stack name; this name will prefixed to route names
        // exp. main/home
        'route_name' => 'main',
        'preparator' => new \Poirot\Ioc\instance(
            \Module\Foundation\HttpSapi\RouterStack\PreparatorHandleBaseUrl::class
        ),
        'params' => [
            // default router params merge with matched route
        ],
    ],

    // Path Helper Action Options
    \Module\Foundation\Services\PathService::CONF_KEY
    => [
        'paths' => [
            'www-assets' => "\$baseUrl/{$themesFolder}/www",
        ],
        'variables' => [
            // force base url value; but still detect from within path service
            # 'baseUrl' => ($baseurl = getenv('PT_BASEURL')) ? $baseurl : null,
        ],
    ],

    // View Renderer Options
    \Poirot\Application\Sapi\Server\Http\RenderStrategy\ListenersRenderDefaultStrategy::CONF_KEY
    => [
        'default_layout'   => 'default',

        \Poirot\Application\Sapi\Server\Http\Service\ServiceViewModelResolver::CONF_KEY => [
            /*
             * > Setup Aggregate Loader
             *   Options:
             *  [
             *    'attach' => [new Loader(), $priority => new OtherLoader(), ['loader' => iLoader, 'priority' => $pr] ],
             *    Loader::class => [
             *       // Options
             *       'Poirot\AaResponder'  => [APP_DIR_VENDOR.'/poirot/action-responder/Poirot/AaResponder'],
             *       'Poirot\Application'  => [APP_DIR_VENDOR.'/poirot/application/Poirot/Application'],
             *    ],
             *    OtherLoader::class => [options..]
             *  ]
             */
            'Poirot\Loader\LoaderNamespaceStack' => [
                // Use Default Theme Folder To Achieve Views With Force First ("**")
                '**' => PT_DIR_THEME_DEFAULT,
            ],
        ],

        \Poirot\Application\Sapi\Server\Http\RenderStrategy\DefaultStrategy\ListenerError::CONF_KEY => [
            ## full name of class exception

            ## use null on second index cause view template render as final layout
            // 'Exception' => ['error/error', null],
            // 'Specific\Error\Exception' => ['error/spec', 'override_layout_name_here']

            ## here (blank) is defined as default layout for all error pages
            'Exception' => ['error/error', 'blank'],
            'Poirot\Application\Exception\exRouteNotMatch' => 'error/404',
        ],
    ],
];