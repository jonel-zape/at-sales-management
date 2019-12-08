<?php

function authenticate()
{
    return [
        'message' => 'success',
        'user_id' => 1
    ];
}

function logout()
{
    return [
        'message' => 'bye!!',
        'user_id' => 0
    ];
}