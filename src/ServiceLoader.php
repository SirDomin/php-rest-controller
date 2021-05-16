<?php

namespace App;

class ServiceLoader {
    function load()
    {
        foreach (glob("src/**/*.php") as $filename)
        {
            include $filename;
        }
    }
}