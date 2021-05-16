<?php

include_once('./src/ServiceLoader.php');
include_once('./src/Router.php');
include_once('./src/ClassLoader.php');

use App\Response;
use App\ServiceLoader;
use App\Router;
use App\ClassLoader;


function dd($data){
    highlight_string("<?php\n " . var_export($data, true) . "?>");
    echo '<script>document.getElementsByTagName("code")[0].getElementsByTagName("span")[1].remove() ;document.getElementsByTagName("code")[0].getElementsByTagName("span")[document.getElementsByTagName("code")[0].getElementsByTagName("span").length - 1].remove() ; </script>';
    die();
}

set_exception_handler(function(Exception $exception) {
    Response\Response::JsonResponse($exception->getMessage(), $exception->getCode())->response();
});

date_default_timezone_set('Europe/Warsaw');

$serviceLoader = new ServiceLoader();
$serviceLoader->load();
$classLoader = new ClassLoader();

$router = new Router($classLoader->load('App\Request\Request'), $classLoader);
$classLoader->addObject($router);

$controller = $router->resolve();
$controller->init()->response();

