<?php
namespace app\controllers;

use app\framework\classes\RouterParameters;

abstract class Controller
{
    public function parameters()
    {
        return RouterParameters::get();
    }

    public function router()
    {
    }
}
