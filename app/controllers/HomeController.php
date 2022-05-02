<?php
namespace app\controllers;

use app\database\DatabaseConnect;
use app\database\models\User;

class HomeController
{
    public function index()
    {
        DatabaseConnect::open(true);

        $user = new User;
        $userFound = $user->findBy('id', 267);

        var_dump($userFound);
        
        DatabaseConnect::close();
        view('home', ['name' => 'Alexandre']);
    }
}
