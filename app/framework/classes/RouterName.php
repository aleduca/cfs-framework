<?php
namespace app\framework\classes;

class RouterName
{
    protected static array $names = [];

    public static function set(array $routes)
    {
        foreach ($routes as $route) {
            foreach ($route as $k => $r) {
                if (isset($r[1]) && isset($r[1]['name'])) {
                    if (isset(self::$names[$r[1]['name']])) {
                        throw new \Exception("Route with name {$r[1]['name']} already exists");
                    }
                    self::$names[$r[1]['name']] = $k;
                }
            }
        }
    }

    public static function get(string $name, array $replace = [])
    {
        if (!isset(self::$names[$name])) {
            throw new \Exception("Router with name {$name} does not exist");
        }

        $routePath = preg_replace_callback('/\{[a-z0-9]+\}/', function ($found) use ($replace) {
            $replaceFound = str_replace(['{','}'], ['',''], $found[0]);
            if (!isset($replace[$replaceFound])) {
                throw new \Exception("Please git the {$replaceFound} placeholder to the route");
            }
            return $replace[$replaceFound];
        }, self::$names[$name]);
        

        return $routePath;
    }
}
