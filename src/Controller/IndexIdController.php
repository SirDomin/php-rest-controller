<?php

namespace App\Controller;

use App\Request\Request;
use App\Response\Response;

class IndexIdController implements ControllerInterface {

    private Request $request;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function init(): Response {
        if ($this->request->method() === 'GET') {
            dd($this->request->get('test'));
        }
        if ($this->request->method() === 'POST') {

        }
    }
}
