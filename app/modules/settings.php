<?php

class Settings
{
    public function index()
    {
        view('settings.php');
    }

    private function verifyPassword($password)
    {
        $userId = $_SESSION['user_id'];
        $data = getData(
            'SELECT
                `password`
            FROM `user`
            WHERE
                `id` = '.$userId.'
                AND `deleted_at` IS NULL'
        );

        if (! isset($data[0])) {
            return [
                'valid' => false,
                'message' => 'Invalid user.'
            ];
        }

        if (! verifyHash($password, $data[0]['password'])) {
            return [
                'valid' => false,
                'message' => 'Invalid password.'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Password verified.'
        ];
    } 

    public function updateUsername()
    {
        $userId = $_SESSION['user_id'];
        $request = escapeString([
            'new_username' => post('new_username'),
            'password' => post('password')
        ]);

        $verify = $this->verifyPassword($request['password']);
        if (! $verify['valid']) {
            return errorResponse([$verify['message']]);
        }

        $data = getData('SELECT `id` FROM `user` WHERE `id` <> '.$userId.' AND `username` = \''.$request['new_username'].'\' AND deleted_at IS NULL');
        if (isset($data['0'])) {
            return errorResponse(['Username already exists.']);
        }

        executeQuery("UPDATE `user` SET `username` = '".$request['new_username']."' WHERE `id` = ".$userId);
        return successfulResponse(['Username has been changed.']);
    }

    public function updatePassword()
    {
        $userId = $_SESSION['user_id'];
        $request = escapeString([
            'new_username' => post('new_username'),
            'repeat_password' => post('repeat_password'),
            'password' => post('password')
        ]);

        $verify = $this->verifyPassword($request['password']);
        if (! $verify['valid']) {
            return errorResponse([$verify['message']]);
        }

        if (trim($request['new_username']) == '') {
            return errorResponse(['Invalid new password.']);
        }

        if ($request['new_username'] != $request['repeat_password']) {
            return errorResponse(['Password not matched.']);
        }

        if (strlen($request['new_username']) < 6) {
            return errorResponse(['Password must be minimum of 6 characters.']);
        }

        $newPassword = hashString($request['new_username']);
        executeQuery("UPDATE `user` SET `password` = '".$newPassword."' WHERE `id` = ".$userId);

        return successfulResponse(['Password has been changed.']);
    }

    public function loginAttemptCount()
    {
        $data = getData('SELECT COUNT(*) AS `count` FROM failed_login');
        return successfulResponse(['login_attempt_count' => $data[0]['count']]);
    }

    public function loginAttempt()
    {
        $data = getData("SELECT *, '*****' AS `password_display` FROM failed_login");
        if (count($data) > 0) {
            return successfulResponse(['data' => $data]);
        }

        return errorResponse(['No results found.']);
    }

    public function clearLoginAttemptCount()
    {
        $request = escapeString([
            'password' => post('password')
        ]);

        $verify = $this->verifyPassword($request['password']);
        if (! $verify['valid']) {
            return errorResponse([$verify['message']]);
        }

        executeQuery("TRUNCATE TABLE failed_login");
        return successfulResponse(['message' => 'Logs cleared.']);
    }

    public function clearData()
    {
        $request = escapeString([
            'password' => post('password')
        ]);

        $verify = $this->verifyPassword($request['password']);
        if (! $verify['valid']) {
            return errorResponse([$verify['message']]);
        }

        executeQuery("TRUNCATE TABLE purchase");
        executeQuery("TRUNCATE TABLE purchase_detail");
        executeQuery("TRUNCATE TABLE sales");
        executeQuery("TRUNCATE TABLE sales_detail");

        return successfulResponse(['message' => 'Data cleared.']);
    }
}