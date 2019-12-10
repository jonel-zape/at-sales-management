<?php

$responseCode = 200;

function httpResonseSuccess()
{
    global $responseCode;
    $responseCode = 200;
    http_response_code(200);
}

function httpResonseUnauthorized()
{
    global $responseCode;
    $responseCode = 401;
    http_response_code(401);
}

function httpResonseNotFound()
{
    global $responseCode;
    $responseCode = 404;
    http_response_code(404);
}

function httpResonseUnprocessable()
{
    global $responseCode;
    $responseCode = 422;
    http_response_code(422);
}
