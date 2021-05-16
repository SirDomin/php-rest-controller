<?php

return [
    'App\Controller\DatabaseController' => [
        'services' => [
            'App\Repository\DatabaseRepository',
        ],
    ],
    'App\Controller\IndexController' => [
        'services' => [
            'App\Request\Request',
        ],
    ],
    'App\Controller\IndexIdController' => [
        'services' => [
            'App\Request\Request',
        ],
    ],
    'App\Controller\DefaultController' => [
        'services' => [
            'App\Request\Request',
            'App\Router'
        ]
    ],
];
