<?php

function databaseConnect()
{
    $config = getDatabaseConfig();

    $host = $dbConfig['host'];
    $user = $dbConfig['user'];
    $password = $dbConfig['password'];
    $database = $dbConfig['database'];

    $connection = @mysqli_connect($host, $user, $password, $database);

    if (!$connection) {
        httpResonseServiceUnavailable();
        jsonResponse([], 'database connection error');
    }

    return $connection;
}

function getData($sql)
{
    $connection = dbConnect();
    $result = mysqli_query($connection, $sql);
    $resultSet = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($connection);

    return $resultSet;
}

function executeQuery($sql)
{
    $connection = databaseConnect();
    mysqli_query($connection, $sql);
    mysqli_close($connection);
}

function escapeString($data)
{
    $escaped = [];

    $connection = databaseConnect();
    foreach ($data as $key => $value) {
        $escaped[$key] = mysqli_real_escape_string($connection, $value);
    }
    mysqli_close($connection);

    return $escaped;
}
