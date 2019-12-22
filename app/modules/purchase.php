<?php

class Purchase
{
    public function index() {
        view('purchase/list.php');
    }

    public function create()
    {
        view('purchase/detail.php');
    }
}