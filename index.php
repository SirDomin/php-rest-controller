<?php

include_once('./src/ServiceLoader.php');
include_once('./src/Router.php');
include_once('./src/ClassLoader.php');
include_once('./src/Response/Response.php');

use App\Response\Response;
use App\ServiceLoader;
use App\Router;
use App\ClassLoader;

set_exception_handler(function($exception) {
    Response::JsonResponse(
        [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ],
        $exception->getCode()
    )->response();
});

function dd(...$data){
    die(json_encode($data));
}

date_default_timezone_set('Europe/Warsaw');

$classLoader = new ClassLoader();

$router = new Router($classLoader->load('App\Request\Request'), $classLoader, $classLoader->load('App\Manager\SecurityManager'));
$classLoader->addObject($router);

$controller = $router->resolve();

$controller['controller']->{$controller['method']}()->response();

