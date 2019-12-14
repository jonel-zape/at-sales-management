<?php

function getDatabaseConfig()
{
    return [
        'host'     => '127.0.0.1',
        'user'     => 'root',
        'password' => '121586',
        'database' => 'atsm'
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
