<?php

return
    [
    'GET' =>
    [
        '/' => ['HomeController@index',['name' => 'home']],
        '/user/{id}' => ['UserController@index',['name' => 'user']],
        '/product/{id}/name/{name}' => 'ProductController@index',
        '/login' => ['LoginController@index'],
    ],
    'POST' => [
        '/login' => 'LoginController@store'
    ],
];
