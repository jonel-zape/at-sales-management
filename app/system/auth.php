<?php

function isAuthenticated()
{
    if (isset($_SESSION['user_id'])) {
        return true;
    }

    return false;
}

function isAuthenticatedJson()
{
    $headers = getallheaders();

    $token = null;
    if (isset($headers['X-USER-TOKEN'])) {
        $token = $headers['X-USER-TOKEN'];
    } elseif (isset($headers['x-user-token'])) {
        $token = $headers['x-user-token'];
    }

    if ($token != null && $_SESSION['token']) {
        return $token == $_SESSION['token'];
    }

    return false;
}
