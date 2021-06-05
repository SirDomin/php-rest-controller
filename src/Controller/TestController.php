<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\Request;
use App\Response\Response;

class TestController implements ControllerInterface
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
                "GET"
            ]
        );
    }

    public function get(): Response
    {
        return Response::JsonResponse(
            [
                "GET_NOWY"
            ]
        );
    }

    public function getAdmin(): Response
    {
        return Response::JsonResponse(
            [
                "GET_NOWY_ADMIN"
            ]
        );
    }

    public function post(): Response
    {
        return Response::JsonResponse(
            [
                "POST XD"
            ]
        );
    }
}