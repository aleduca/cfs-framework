<?php
namespace app\controllers;

class ProductController extends Controller
{
    public function index()
    {
        // var_dump($this->router('login'));
        // var_dump($this->parameters()->id); // get parameter based on placeholder name (e.g. /product/name/{id}/name/{name})
    }

    public function update()
    {
        var_dump('update product')   ;
    }

    public function destroy()
    {
        var_dump('destroy');
    }
}
