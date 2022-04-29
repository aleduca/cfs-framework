<?php

return
    [
    'GET' =>
    [
        '/' => ['HomeController@index',['name' => 'home']],
        '/user/{id}' => ['UserController@index',['name' => 'user']],
        '/product/{id}/name/{name}' => 'ProductController@index', // you can use with or without array
        '/login' => 'LoginController@index',
    ],
    'POST' => [
        '/login' => 'LoginController@store'
    ],
    'PUT' => [
        '/product/{id}/update' => 'ProductController@update',
    ],
    'DELETE' => [
        '/product/{id}/delete' => 'ProductController@destroy',
    ]
];
