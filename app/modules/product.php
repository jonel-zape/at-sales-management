<?php

class Product
{
    public function index()
    {
        view('product/list.php');
    }

    public function create()
    {
        view('product/detail.php');
    }

    public function edit($id)
    {
        view('product/detail.php');
    }
}
