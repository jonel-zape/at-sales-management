<?php

$responseCode = 200;
$responseMessage = 'Success';

function setHttpResponseCode($code, $message)
{
    global $responseCode;
    global $responseMessage;

    $responseCode = $code;
    $responseMessage = $message;

    return $responseCode;
}

function getHttpResponseCode()
{
    global $responseCode;

    return $responseCode;
}

function getHttpResponseMessage()
{
    global $responseMessage;

    return $responseMessage;
}

function httpResonseSuccess()
{
    $responseCode = setHttpResponseCode(200, 'Success');
    http_response_code($responseCode);
}

function httpResonseUnauthorized()
{
    $responseCode = setHttpResponseCode(401, 'Unauthorized');
    http_response_code($responseCode);
}

function httpResonseNotFound()
{
    $responseCode = setHttpResponseCode(404, 'Request not found');
    http_response_code($responseCode);
}

function httpResonseUnprocessable()
{
    $responseCode = setHttpResponseCode(422, 'Unprocessable');
    http_response_code($responseCode);
}

function httpResonseServiceUnavailable()
{
    $responseCode = setHttpResponseCode(503, 'Service Unavailable');
    http_response_code($responseCode);
}

function renderView($path)
{
    require getPagesPath().'/'.$path;
}
