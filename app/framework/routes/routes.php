<?php

return
    [
    'GET' =>
    [
        '/' => ['HomeController@index',['name' => 'home']],
        '/user/{id}' => ['UserController@index',['name' => 'user']],
        '/product/{id}/name/{name}' => ['ProductController@index',['name' => 'product']], // you can use with or without array
        '/login' => ['LoginController@index',['name' => 'login']],
    ],
    'POST' => [
        '/login' => ['LoginController@store', ['name' => 'login.post']]
    ],
    'PUT' => [
        '/product/{id}/update' => 'ProductController@update',
    ],
    'DELETE' => [
        '/product/{id}/delete' => 'ProductController@destroy',
    ]
];
