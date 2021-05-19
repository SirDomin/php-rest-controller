<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\Request;
use App\Response\Response;

class UserController implements ControllerInterface
{
    private Request $request;

    private UserRepository $userRepository;

    function __construct(Request $request, UserRepository $userRepository)
    {
        $this->request = $request;
        $this->userRepository = $userRepository;
    }

    function init(): Response
    {
        return Response::JsonResponse(
            [
                "UserController"
            ]
        );
    }

    function findUserById(): Response
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy('id', $this->request->get('id', true));

        assert($user !== null, new \Exception('user not found', 404));

        return Response::JsonResponse(
            [
                'user' => $user()
            ]
        );
    }
}