<?php
namespace app\controllers;

use app\framework\classes\Cache;

class LoginController extends Controller
{
    public function index()
    {
        view('login', ['name' => 'Pedro']);
    }

    public function store()
    {
        var_dump('store')   ;
    }
}
