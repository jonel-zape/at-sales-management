<?php

function databaseConnect()
{
    $config = getDatabaseConfig();

    $host = $config['host'];
    $user = $config['user'];
    $password = $config['password'];
    $database = $config['database'];

    $connection = @mysqli_connect($host, $user, $password, $database);

    if (!$connection) {
        httpResonseServiceUnavailable();
        jsonResponse('Database connection error.', null,  TERMINATE_REQUEST);
    }

    return $connection;
}

function getData($sql)
{
    $connection = databaseConnect();

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $details = [
            'description' => mysqli_error($connection),
            'command' => $sql
        ];

        httpResonseUnprocessable();
        jsonResponse($details, null,  TERMINATE_REQUEST);
    }

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
