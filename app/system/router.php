<?php

require 'config.php';
require 'const.php';
require 'auth.php';
require 'helpers.php';

$routes = [];

function defaultRoute($module, $requestType, $validateAuth = true)
{
    global $routes;

    $routes['index.php'] = [
        'module' => $module.'.php',
        'request_type' => $requestType,
        'validate_auth' => $validateAuth
    ];
}

function addRoute($url, $module, $requestType, $validateAuth = true)
{
    global $routes;

    $routes[$url] = [
        'module' => $module.'.php',
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

        require getModulesPath().$route['module'];

        if (!function_exists($function)) {
            renderNotFound();
            exit;
        }

        $module = call_user_func($function, $value1, $value2);
        if ($route['request_type'] == REQUEST_JSON) {
            jsonResponse($module['data'], $module['message']);
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

function jsonResponse($data, $message)
{
    global $responseCode;

    $response = [
        'code' => $responseCode,
        'message' => $message,
        'data' => $data
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
