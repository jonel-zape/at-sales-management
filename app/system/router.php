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

        $module = call_user_func(array($object, $function), $value1, $value2);

        if ($route['request_type'] == REQUEST_PAGE) {
            pageFooter();
        }

        if ($route['request_type'] == REQUEST_JSON) {
            renderJsonResponse($module['data'], $module['message']);
        }
    } else {
        renderNotFound();
    }
}

function renderNotFound()
{
    httpResonseNotFound();
    require getPagesPath().'404.php';
    exit;
}

function jsonResponse($data, $message = null, $terminate = false)
{
    if (is_null($message)) {
        $message = getHttpResponseMessage();
    }

    if ($terminate) {
        renderJsonResponse($data, $message);
    }

    return [
        'message' => $message,
        'data' => $data
    ];
}

function renderJsonResponse($data, $message)
{
    $response = [
        'code' => getHttpResponseCode(),
        'message' => $message,
        'data' => $data
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
