<?php
namespace app\controllers;

use app\database\builder\DB;
use app\database\DatabaseConnect;
use app\database\models\Post;
use app\database\models\User;
use app\database\paginate\Paginate;

class HomeController
{
    public function index()
    {
        DatabaseConnect::open(true);

        $db = new DB;
        // $usersFound = $db->table('users')
        // ->select('id, firstName, lastName')
        // ->where('id', '>', 260)
        // ->order('id desc')
        // ->group('id')
        // ->limit(2)
        // ->get();
        
        // $commentsFound = $db->table('comments')->select('id,content')->where('id', '>', 1)->get();
        
        // dd($commentsFound);

        // $posts = $db->table('users')
        // ->select('id,firstName,lastName')
        // ->total();

        $post = new Post;
        $posts = $post->all();

        $paginate = new Paginate;
        // $paginate->setPerPage(10);
        // $paginate->setUrlIdentification('page');
        $paginate->setData($posts);

        var_dump($posts);
        die();

        // Paginate::create($posts);

        DatabaseConnect::close();
        view('home', ['name' => 'Alexandre','posts' => $posts]);
    }
}
