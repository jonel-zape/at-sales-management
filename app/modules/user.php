<?php

class User
{
    public function authenticate()
    {
        httpResonseSuccess();

        return jsonResponse([
            'user_id' => 1,
            'token' => 'XZXWAWRTERSCCZZSsswSASWSA'
        ]);
    }

    public function all()
    {
        $users = getData('SELECT `id`, `username` FROM `user` WHERE `deleted_at` IS NULL');

        httpResonseSuccess();
        return jsonResponse($users);
    }
}
