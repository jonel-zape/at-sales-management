<?php

require 'config.php';
require 'const.php';
require 'auth.php';
require 'helpers.php';
require 'database.php';

$routes = [];

function defaultRoute($module, $requestType, $validateAuth = true)
{
    global $routes;

    $routes['index.php'] = [
        'module' => $module,
        'request_type' => $requestType,
        'validate_auth' => $validateAuth
    ];
}

function addRoute($url, $module, $requestType, $validateAuth = true)
{
    global $routes;

    $routes[$url] = [
        'module' => $module,
        'request_type' => $requestType,
        'validate_auth' => $validateAuth
    ];
}

function getSegment($segment)
{
    if (isset($_GET['segment'.$segment])) {
        return $_GET['segment'.$segment];
    }

    return null;
}

function route()
{
    global $routes;

    $function = getSegment(2);
    $value1  = getSegment(3);
    $value2  = getSegment(4);
    $url = getSegment(1);

    $url .= !is_null($function) ? '/'.$function : '';
    $url .= !is_null($value1) ? '/$' : '';
    $url .= !is_null($value2) ? '/$' : '';

    $function = is_null($function) ? 'index' : $function;

    if (isset($routes[$url])) {
        $route = $routes[$url];

        require getModulesPath().$route['module'].'.php';

        $class = ucfirst($route['module']);
        $object = new $class();

        if (! method_exists($object, $function)) {
            renderNotFound();
            exit;
        }

        if ($route['request_type'] == REQUEST_PAGE) {
            pageHeader();
        }

        $moduleResponse = call_user_func(array($object, $function), $value1, $value2);

        if ($route['request_type'] == REQUEST_PAGE) {
            pageFooter();
        }

        if ($route['request_type'] == REQUEST_JSON) {
            respondAndTerminate($moduleResponse);
        }
    } else {
        renderNotFound();
    }
}

function respondAndTerminate($response)
{
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function renderNotFound()
{
    httpResonseNotFound();
    require getPagesPath().'404.php';
    exit;
}

function jsonResponse($data, $terminate = false)
{
    if ($terminate) {
        renderJsonResponse($data);
    }

    return $data;
}

function formatResponse($data, $message = null, $errors = [], $action = '')
{
    $formatted = [
        'code' => getHttpResponseCode(),
        'message' => $message,
        'errors' => $errors,
        'action' => $action,
        'values' => $data
    ];

    return $formatted;
}

// For Modules
function successfulResponse($data, $action = '')
{
    httpResonseSuccess();
    return formatResponse($data, getHttpResponseMessage(), [], $action);
}

function errorResponse($errors, $code = null, $action = '')
{
    if (is_null($code)) {
        httpResonseUnprocessable();
    } else {
        userSetHttpResponse($code);
    }

    return formatResponse('', getHttpResponseMessage(), $errors, $action);
}