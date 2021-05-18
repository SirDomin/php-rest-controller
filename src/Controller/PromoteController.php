<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\Request;
use App\Response\Response;

class PromoteController implements ControllerInterface
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
        /** @var User $user */
        $user = $this->userRepository->findOneBy('id', $this->request->get('id', true));

        $user->setRole('admin');

        $this->userRepository->save($user);

        return Response::JsonResponse(
            [
                'user' => $user()
            ]
        );
    }
}