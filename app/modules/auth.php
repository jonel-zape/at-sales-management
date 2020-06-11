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
            $this->logFailed($username, $password, 'Invalid username and password');
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
                $this->logFailed($username, $password, 'Invalid password');
                return errorResponse(['Invalid username or password.']);
            }

            $_SESSION['user_id'] = $userId;
            $_SESSION['token'] = hashString(rand(10000,999999));
            return successfulResponse([
                'id' => $userId,
                'token' => $_SESSION['token']
            ]);
        }

        $this->logFailed($username, $password, 'Invalid username');
        return errorResponse(['Invalid username or password.']);
    }

    private function logFailed($username, $password, $error)
    {
        executeQuery(
            'INSERT INTO failed_login (username_used, password_used, date_time, other_info)
            VALUES
            (\''.$username.'\', \''.$password.'\', NOW(), \''.$error.'\')'
        );
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        routeTo('/sign-in');
    }
}
