<?php

class Auth
{
    public function index()
    {
        if (isAuthenticated()) {
            routeTo('/home');
        }

        view('sign-in.php');
    }
}
