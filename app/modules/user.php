<?php

class User
{
    public function authenticate()
    {
        return successfulResponse([
            'user_id' => 1,
            'token' => 'XZXWAWRTERSCCZZSsswSASWSA'
        ]);
    }

    public function all()
    {
        $users = getData('SELECT `id`, `username` FROM `user` WHERE `deleted_at` IS NULL');
        return successfulResponse($users);
    }
}
