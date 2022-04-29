<?php
namespace app\framework\classes;

use Exception;

class Router
{
    private string $request;
    private string $path;

    private function routeFound($routes)
    {
        if (!isset($routes[$this->request])) {
            throw new Exception("Route {$this->path} does not exist");
        }
        
        if (isset($routes[$this->request][$this->path])) {
            return true;
        }
    }

    private function controllerFound($controllerNamespace, $controller, $action)
    {
        if (!class_exists($controllerNamespace)) {
            throw new Exception("Controller {$controller} does not exist");
        }
        
        if (!method_exists($controllerNamespace, $action)) {
            throw new Exception("Method {$action} does not exist on controller {$controller}");
        }
    }

    private function routerPlaceholder(array $routes)
    {
        $path = RouterPlaceholder::create($routes[$this->request], $this->path);

        if (!$path) {
            throw new Exception("Route does not exist {$this->path}");
        }

        $this->path = $path;
    }

    public function execute($routes)
    {
        $this->path = path();
        $this->request = request();

        // if not found the exact router with path
        $routerFound = $this->routeFound($routes);

        // try to get with dynamic routes
        if (!$routerFound) {
            $this->routerPlaceholder($routes);
        }

        $router = $routes[$this->request][$this->path];

        if (is_string($router)) {
            [$controller, $action] = explode('@', $router);
        }
        
        if (is_array($router)) {
            [$controller, $action] = explode('@', $router[0]);
        }
    
        $controllerNamespace = "app\\controllers\\{$controller}";

        $this->controllerFound($controllerNamespace, $controller, $action);
        
        $controllerInstance = new $controllerNamespace;
        $controllerInstance->$action();
    }
}
