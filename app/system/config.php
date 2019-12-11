<?php

function getDatabaseConfig()
{
    return [
        'host'     => '',
        'user'     => '',
        'password' => '',
        'database' => ''
    ];
}

function getPagesPath()
{
    return '../app/resources/pages/';
}

function getModulesPath()
{
    return '../app/modules/';
}

function pageHeader()
{
    require getPagesPath().'/master/header.php';
}

function pageFooter()
{
    require getPagesPath().'/master/footer.php';
}
