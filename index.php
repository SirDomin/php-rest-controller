<?php

include_once('./src/ServiceLoader.php');
include_once('./src/Router.php');
include_once('./src/ClassLoader.php');

use App\Response\Response;
use App\ServiceLoader;
use App\Router;
use App\ClassLoader;


function dd(...$data){
    die(json_encode($data));
}

//set_exception_handler(function($exception) {
//    Response::JsonResponse(
//        [
//            'message' => $exception->getMessage(),
//            'code' => $exception->getCode()
//        ],
//        $exception->getCode()
//    )->response();
//});

date_default_timezone_set('Europe/Warsaw');

//$serviceLoader = new ServiceLoader();
//$serviceLoader->load();

$classLoader = new ClassLoader();

$router = new Router($classLoader->load('App\Request\Request'), $classLoader, $classLoader->load('App\Manager\SecurityManager'));
$classLoader->addObject($router);

$controller = $router->resolve();

$controller['controller']->{$controller['method']}()->response();

