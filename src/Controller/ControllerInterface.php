<?php

namespace App\Controller;

use App\Response\Response;

interface ControllerInterface {
    public function init(): Response;
}
