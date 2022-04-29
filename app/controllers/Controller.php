<?php
namespace app\controllers;

use app\framework\classes\RouterName;
use app\framework\classes\RouterParameters;

abstract class Controller
{
    public function parameters()
    {
        return RouterParameters::get();
    }

    public function router(string $name, $replace = [])
    {
        return RouterName::get($name, $replace);
    }
}
