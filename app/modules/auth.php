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

    public function authenticate()
    {
        $request = escapeString([
            'username' => post('username'),
            'password' => post('password')
        ]);

        $username = $request['username'];
        $password = $request['password'];

        if (is_null($username) || is_null($password) || trim($username) == '' || trim($password) == '') {
            return errorResponse(['Invalid username or password.']);
        }

        $user = getData(sprintf(
            "SELECT
                `id`,
                `password`
            FROM `user`
            WHERE
                `username` = '%s'
                AND `deleted_at` IS NULL",
        $username));

        if (isset($user[0]['id'])) {
            $userId = $user[0]['id'];
            $hiddenPassword = $user[0]['password'];

            if (! verifyHash($password, $hiddenPassword)) {
                return errorResponse(['Invalid username or password.']);
            }

            $_SESSION['user_id'] = $userId;
            return successfulResponse(['id' => $userId]);
        }

        return errorResponse(['Invalid username or password.']);
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        routeTo('/sign-in');
    }
}
