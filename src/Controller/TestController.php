<?php

namespace App\Controller;

use App\Response\Response;

class TestController implements ControllerInterface
{
    function __construct()
    {

    }

    function init(): Response
    {
        return Response::JsonResponse(
            [
                "TestController"
            ]
        );
    }
}