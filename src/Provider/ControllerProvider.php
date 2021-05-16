<?php

namespace App\Provider;

use App\Controller\ControllerInterface;

class ControllerProvider {

    private $controllers = [];

    function __construct(array $controllers) {
        $this->controllers = $controllers;
    }

    function provide($module): ?ControllerInterface {

        /** @var ControllerInterface */
        foreach($this->controllers as $controller) {

            if ($controller->supports($module)) {
                return $controller;
            }
        }

        return null;
    }
}
