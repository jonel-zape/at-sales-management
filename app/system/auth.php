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