<?php

// TODO:
// Secure using token
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
    if (isset($headers['X-USER-TOKEN']) && $_SESSION['token']) {
        return $headers['X-USER-TOKEN'] == $_SESSION['token'];
    }

    return false;
}
