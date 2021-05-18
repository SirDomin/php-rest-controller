<?php

return [
    'App\Repository\DatabaseRepository' => [
        'services' => []
    ],
    'App\Request\Request' => [
        'services' => []
    ],
    'App\Router' => [
        'services' => [
            'App\Request\Request',
        ]
    ],
    'App\Repository\EntityRepository' => [
        'services' => []
    ],
    'App\Repository\UserRepository' => [
        'services' => []
    ],
    'App\Manager\SecurityManager' => [
        'services' => [
            'App\Repository\UserRepository',
            'App\Request\Request',
        ]
    ],
];
