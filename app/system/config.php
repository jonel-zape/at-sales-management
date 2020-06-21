<?php

function systemVersion()
{
    // Major.Minor.Fix
    return '1.0.0';
}

function getDatabaseConfig()
{
    return [
        'host'     => '127.0.0.1',
        'user'     => 'root',
        'password' => '123456',
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

function getComponentsPath()
{
    return '../app/resources/components/';
}

function pageHeader()
{
    require getPagesPath().'/master/header.php';
}

function pageFooter()
{
    require getPagesPath().'/master/footer.php';
}

function signInPageLocation()
{
    return '/sign-in';
}

function getMonetaryPrecision()
{
    return 2;
}
