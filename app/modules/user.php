<?php

class User
{
    public function all()
    {
        $users = getData('SELECT `id`, `username` FROM `user` WHERE `deleted_at` IS NULL');
        return successfulResponse($users);
    }
}
