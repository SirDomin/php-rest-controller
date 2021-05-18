<?php

namespace App\Controller;

use App\Entity\Summoner;
use App\Entity\User;
use App\Repository\SummonerRepository;
use App\Repository\UserRepository;
use App\Request\Request;
use App\Response\Response;
use App\Router;

class LoginController implements ControllerInterface
{

    private Request $request;

    private Router $router;

    private UserRepository $userRepository;

    function __construct(Request $request, Router $router, UserRepository $userRepository)
    {
        $this->router = $router;
        $this->request = $request;
        $this->userRepository = $userRepository;
    }

    public function init(): Response {

        /** @var User $user */
        $user = $this->userRepository->findOneBy('login', $this->request->get('login', true));
        $user->login($this->request->get('password', true));

        $this->userRepository->save($user);

        return Response::JsonResponse(
            [
                'login' => $user->getLogin(),
                'token' => $user->getToken()
            ]
        );
    }
}
