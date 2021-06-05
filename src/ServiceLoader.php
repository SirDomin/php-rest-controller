<?php

namespace App;

use App\Request\Request;

class ServiceLoader {
    private int $servicesLoaded = 0;

    public function __construct()
    {
    }

    public function getServicesLoaded(): int
    {
        return $this->servicesLoaded;
    }

    public function getNeededServices(): int
    {
        return 5;
    }

    function load()
    {
        foreach (glob("src/**/*.php") as $filename)
        {
            $this->servicesLoaded++;
            include $filename;
        }
    }
}