<?php
namespace app\framework\classes;

class RouterParameters
{
    protected static array $parameters = [];

    public static function set(string $route, string $match)
    {
        $routes = array_filter(explode('/', $route));
        $matches = array_filter(explode('/', $match));
        foreach ($routes as $key => $value) {
            if (str_contains($value, '{')) {
                self::$parameters[str_replace(['{','}'], ['',''], $value)] = $matches[$key];
            }
        };
    }

    public static function get()
    {
        return (object)self::$parameters;
    }
}
