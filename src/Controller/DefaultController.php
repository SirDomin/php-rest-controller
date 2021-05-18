<?php

namespace App\Controller;

use App\Entity\Summoner;
use App\Repository\SummonerRepository;
use App\Request\Request;
use App\Response\Response;
use App\Router;

class DefaultController implements ControllerInterface
{

    private Request $request;

    private Router $router;

    function __construct(Request $request, Router $router)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function init(): Response {
        return Response::JsonResponse(
            [
                'available_routes' => $this->router->getRoutes(),
                'current_route' => $this->request->url()
            ]
        );
    }
}
