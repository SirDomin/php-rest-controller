<?php

return [
    'App\Repository\DatabaseRepository' => [
        'services' => []
    ],
    'App\Repository\GameRepository' => [
        'services' => [
        ]
    ],
    'App\Repository\PlayerRepository' => [
        'services' => []
    ],
    'App\Request\Request' => [
        'services' => []
    ],
    'App\Resolver\GameResolver' => [
        'services' => [
            'App\Repository\GameRepository',
            'App\Repository\PlayerRepository',
            'App\Repository\SummonerRepository',
            'App\Router',
        ]
    ],
    'App\Repository\SummonerRepository' => [
        'services' => []

    ],
    'App\Router' => [
        'services' => ['App\Request\Request']
    ],
];
