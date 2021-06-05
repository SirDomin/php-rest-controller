<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\EntityRepository;
use App\Request\Request;
use App\Response\Response;
use App\Router;

class RegisterController implements ControllerInterface
{

    private Request $request;

    private Router $router;

    private EntityRepository $userRepository;

    function __construct(Request $request, Router $router, EntityRepository $userRepository)
    {
        $this->router = $router;
        $this->request = $request;
        $this->userRepository = $userRepository;
    }

    public function init(): Response {

        $user = new User();

        $user->setPassword($this->request->get('password', true), true);
        $user->setLogin($this->request->get('login', true));
        $user->setEmail($this->request->get('email', true));

        $this->userRepository->save($user);
        return Response::JsonResponse(
            [
                'user' => $user()
            ]
        );
    }
}
