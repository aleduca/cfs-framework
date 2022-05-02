<?php
namespace app\controllers;

use app\database\builder\DB;
use app\database\DatabaseConnect;
use app\database\models\User;

class HomeController
{
    public function index()
    {
        DatabaseConnect::open(true);

        $db = new DB;
        $userFound = $db->table('users')->select('id, firstName, lastName')->get();
        
        var_dump($userFound);

        DatabaseConnect::close();
        view('home', ['name' => 'Alexandre']);
    }
}
