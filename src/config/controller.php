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
    'App\Controller\LoginController' => [
        'services' => [
            'App\Request\Request',
            'App\Router',
            'App\Repository\UserRepository'
        ]
    ],
    'App\Controller\RegisterController' => [
        'services' => [
            'App\Request\Request',
            'App\Router',
            'App\Repository\EntityRepository'
        ]
    ],
    'App\Controller\PromoteController' => [
        'services' => [
            'App\Request\Request',
            'App\Repository\UserRepository'
        ]
    ],
    'App\Controller\UserController' => [
        'services' => [
            'App\Request\Request',
            'App\Repository\UserRepository'
        ]
    ]
];
