<?php
namespace app\controllers;

use app\database\DatabaseConnect;
use app\database\models\User;

class HomeController
{
    public function index()
    {
        // DatabaseConnect::open(true);

        // $user = new User;
        // $user->create([
        //     'firstName' => 'Joao',
        //     'lastName' => 'Santos',
        //     'email' => 'santos@email.com.br',
        //     'password' => password_hash('123', PASSWORD_DEFAULT),
        // ]);
        
        // DatabaseConnect::close();
        view('home', ['name' => 'Alexandre']);
    }
}
