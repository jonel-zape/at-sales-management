<?php

require 'config.php';
require 'const.php';
require 'auth.php';

$routes = [];

function addRoute($url, $location, $requestType, $validateAuth = true)
{
    global $routes;

    $routes['url_'.$url] = [
        'location' => $location,
        'request_type' => $requestType,
        'validate_auth' => $validateAuth
    ];

    $segments = explode('/', $url);
    $routes['default_404_request_type'.$segments[0]] = $requestType;
}

function route()
{
    global $routes;

    $requestType = REQUEST_PAGE;
    $validateAuth = false;
    $location = '';

    if (isset($_GET['segment1'])) {
        $key = 'default_404_request_type'.$_GET['segment1'];
        if (isset($routes[$key])) {
            $requestType = $routes['default_404_request_type'.$_GET['segment1']];
        }

        $key = 'url_'.$_GET['segment1'];

        if (isset($_GET['segment2'])) {
            $key = $key.'/'.$_GET['segment2'];
        }
        if (isset($_GET['segment3'])) {
            $key = $key.'/'.$_GET['segment3'];
        }
        if (isset($_GET['segment4'])) {
            $key = $key.'/'.$_GET['segment4'];
        }

        if (isset($routes[$key])) {
            $location = $routes[$key]['location'];
            $requestType = $routes[$key]['request_type'];
            $validateAuth = $routes[$key]['validate_auth'];
        }
    }

    renderResponse($requestType, $validateAuth, $location);
}

function renderResponse($requestType, $validateAuth, $location)
{
    if ($requestType == REQUEST_JSON) {
        $json['data'] = ['message' => 'page not found'];
        if (trim($location) == '') {
            renderNotFound(REQUEST_JSON);
        } else {
            require getModulesPath().$location;

            if (!isset($_GET['segment2']) || !function_exists($_GET['segment2'])) {
                renderNotFound(REQUEST_JSON);
            }

            $json['data'] = call_user_func($_GET['segment2']);
            echo json_encode($json);
            exit;
        }
    } else {
        if (trim($location) == '') {
            renderNotFound($requestType);
        } else {
            require getPagesPath().$location;
        }
    }
}

function renderNotFound($requestType)
{
    http_response_code(404);
    if ($requestType == REQUEST_JSON) {
        $json['data'] = ['message' => 'Page not found.'];
        echo json_encode($json);
    } else {
        require getPagesPath().'404.php';
    }
    exit;
}
