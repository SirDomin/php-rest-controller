<?php

declare(strict_types=1);

namespace App;

use App\Controller\ControllerInterface;
use App\Manager\SecurityManager;
use App\Request\Request;

final class Router
{
    private $routes = [];

    private $request;

    private $classLoader;

    private SecurityManager $securityManager;

    public function __construct(Request $request, ClassLoader $classLoader, SecurityManager $securityManager)
    {
        $this->request = $request;
        $this->classLoader = $classLoader;
        $this->routes = yaml_parse_file('src/config/routes.yaml')['routes'];
        $this->securityManager = $securityManager;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getUrlByName(string $name): ?string
    {
        foreach ($this->routes as $routeName => $routeConfig) {
            if ($routeName === $name) {
                return $routeConfig['url'];
            }
        }

        return null;
    }

    public function resolve(): array
    {
        foreach ($this->routes as $route) {
            $url = explode('/', $route['url']);
            $requestUrl = explode('/', $this->request->url());

            for ($x = 0; $x < sizeof($url); $x++) {
                if (isset($requestUrl[$x]) && str_contains($url[$x], '{') && $url[$x] !== $requestUrl[$x]) {
                    $variable = str_replace('{', '', $url[$x]);
                    $variable = str_replace('}', '', $variable);

                    $this->request->set($variable, $requestUrl[$x]);
                    $requestUrl[$x] = $url[$x];
                }
            }
            $url = implode('/', $url);
            $requestUrl = implode('/', $requestUrl);

            if ($url === $requestUrl) {
                if (in_array($this->request->method(), $route['methods'])) {
                    if(isset($route['allow']) && !in_array('guest', $route['allow'])) {
                        $this->securityManager->authorize($route['allow']);
                    }

                    $controllerData = explode('::',$route['controller']);

                    return [
                        'controller' => $this->classLoader->load($controllerData[0]),
                        'method' => $controllerData[1] ?? 'init'
                    ];
                }
            }

        }

        throw new \Exception(sprintf('no route registered for %s with method %s',$this->request->url(), $this->request->method()), 404);
    }
}
