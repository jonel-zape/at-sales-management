<?php

$responseCode = 200;
$responseMessage = 'Success';
$moduleParameter = [];

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
    $responseCode = setHttpResponseCode(HTTP_SUCCESS, 'Success');
    http_response_code($responseCode);
}

function httpResonseUnauthorized()
{
    $responseCode = setHttpResponseCode(HTTP_UNAUTHORIZED, 'Unauthorized');
    http_response_code($responseCode);
}

function httpResonseNotFound()
{
    $responseCode = setHttpResponseCode(HTTP_NOT_FOUND, 'Request not found');
    http_response_code($responseCode);
}

function httpResonseUnprocessable()
{
    $responseCode = setHttpResponseCode(HTTP_UNPROCESSABLE, 'Unprocessable');
    http_response_code($responseCode);
}

function httpResonseServiceUnavailable()
{
    $responseCode = setHttpResponseCode(HTTP_SERVICE_UNAVAILABLE, 'Service Unavailable');
    http_response_code($responseCode);
}

function userSetHttpResponse($code)
{
    switch ($code) {
        case HTTP_SUCCESS:
            httpResonseSuccess();
            break;
        case HTTP_UNAUTHORIZED:
            httpResonseUnauthorized();
            break;
        case HTTP_NOT_FOUND:
            httpResonseNotFound();
            break;
        case HTTP_SERVICE_UNAVAILABLE;
            httpResonseServiceUnavailable();
            break;
        default:
            httpResonseUnprocessable();
            break;
    }
}

function view($path, $params = [])
{
    global $moduleParameter;

    $moduleParameter = $params;
    require getPagesPath().'/'.$path;
}

function component($path, $params = [])
{
    require getComponentsPath().'/'.$path;
}

function routeTo($location)
{
    ob_end_clean();
    header('location: '.$location);
    exit;
}

function post($key)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }

    return null;
}

function get($key)
{
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }

    return null;
}

function hashString($string)
{
    return password_hash($string, PASSWORD_DEFAULT);
}

function verifyHash($string, $hashed)
{
    return password_verify($string, $hashed);
}

function formatNumber($value)
{
    if (!is_numeric($value)) {
        $value = 0;
    }

    return number_format((float)$value, getMonetaryPrecision(), '.', '');
}

function toNumber($value)
{
    return (float)str_replace(',', '', trim($value));
}

function getDateToday()
{
    return date("Y-m-d");
}

function printDateToday()
{
    echo getDateToday();
}

function printBoolean($boolean)
{
    echo $boolean ? 'true' : 'false';
}

function elementReadOnly($boolean)
{
    echo $boolean ? 'readonly="true"' : '';
}

function tickCheckedBoxIfNotNull($value)
{
    if (!is_null($value)) {
        echo 'checked';
    }
}

function nullToEmpty($value)
{
    return is_null($value) ? '' : $value;
}

function printNullToEmpty($value)
{
    echo nullToEmpty($value);
}

function isValidDate($value, $allowEmpty = false)
{
    if ($allowEmpty && trim($value) == '') {
        return true;
    }

    $dates = explode('-', trim($value));

    if (isset($dates[0]) && isset($dates[1]) && isset($dates[2])) {
        return checkdate($dates[1], $dates[2], $dates[0]);
    }

    return false;
}