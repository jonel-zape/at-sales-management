<?php

$responseCode = 200;

function setHttpResponseCode($code)
{
    global $responseCode;
    $responseCode = $code;

    return $responseCode;
}

function getHttpResponseCode()
{
    global $responseCode;

    return $responseCode;
}

function httpResonseSuccess()
{
    $responseCode = setHttpResponseCode(200);
    http_response_code($responseCode);
}

function httpResonseUnauthorized()
{
    $responseCode = setHttpResponseCode(401);
    http_response_code($responseCode);
}

function httpResonseNotFound()
{
    $responseCode = setHttpResponseCode(404);
    http_response_code($responseCode);
}

function httpResonseUnprocessable()
{
    $responseCode = setHttpResponseCode(422);
    http_response_code($responseCode);
}

function httpResonseServiceUnavailable()
{
    $responseCode = setHttpResponseCode(503);
    http_response_code($responseCode);
}
