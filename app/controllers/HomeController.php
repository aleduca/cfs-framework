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
        $usersFound = $db->table('users')
        ->select('id, firstName, lastName')
        ->where('id', '>', 260)
        ->order('id desc')
        ->limit(2)
        ->get();

        var_dump($usersFound);
        
        // $commentsFound = $db->table('comments')->select('id,content')->where('id', '>', 1)->get();
        
        // dd($commentsFound);

        DatabaseConnect::close();
        view('home', ['name' => 'Alexandre']);
    }
}
