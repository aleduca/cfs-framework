<?php
namespace app\framework\classes;

class RouterPlaceholder
{
    public static function create(array $routes, string $path)
    {
        foreach (array_keys($routes) as $route) {
            if (str_contains($route, '{')) {
                $routeReplaced = str_replace('/', '\/', $route);

                $pattern = preg_replace_callback('/\{[a-z0-9]+\}/', function () {
                    return '[a-z0-9]+';
                }, $routeReplaced);

                
                preg_match("/$pattern/", $path, $match);
                
                if (isset($match[0])) {
                    RouterParameters::set($route, $match[0]);
                    RouterName::set($match[0]);
                    return $route;
                    break;
                }
            }
        }

        return false;
    }
}
