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
        respondAndTerminate(errorResponse(['Database connection error.'], HTTP_SERVICE_UNAVAILABLE));
    }

    return $connection;
}

function getData($sql, $printCommand = false)
{
    if ($printCommand) {
        die('<pre>'.$sql.'<pre>');
    }

    $connection = databaseConnect();

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $details = [
            'description' => mysqli_error($connection),
            'command' => $sql
        ];

        respondAndTerminate(errorResponse($details, HTTP_UNPROCESSABLE));
    }

    $resultSet = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($connection);

    return $resultSet;
}

function executeQuery($sql)
{
    $connection = databaseConnect();

    if (is_array($sql)) {
        foreach ($sql as $command) {
            $execution = @mysqli_query($connection, $command);
            if (!$execution) {
                $details = [
                    'description' => mysqli_error($connection),
                    'command' => $sql
                ];

                respondAndTerminate(errorResponse($details, HTTP_UNPROCESSABLE));
            }
        }
    } else {
        $execution = @mysqli_query($connection, $sql);
        if (!$execution) {
            $details = [
                'description' => mysqli_error($connection),
                'command' => $sql
            ];

            respondAndTerminate(errorResponse($details, HTTP_UNPROCESSABLE));
        }
    }

    mysqli_close($connection);
}

function escapeString($data)
{
    $escaped = [];

    $connection = databaseConnect();

    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $escaped[$key] = null;
            } else {
                $escaped[$key] = mysqli_real_escape_string($connection, $value);
            }
        }
    } else {
        $escaped = mysqli_real_escape_string($connection, $data);
    }

    mysqli_close($connection);

    return $escaped;
}

function formatNumberSql($raw, $alias = null)
{
    $alias = is_null($alias) ? $raw : $alias;

    return 'FORMAT('.$raw.', '.getMonetaryPrecision().') AS '.$alias;
}

function roundNumberSql($raw, $alias = null)
{
    $alias = is_null($alias) ? $raw : $alias;

    return 'ROUND('.$raw.', '.getMonetaryPrecision().') AS '.$alias;
}

function dateOnlySql($raw, $alias = null)
{
    $alias = is_null($alias) ? $raw : $alias;

    return 'DATE('.$raw.') AS '.$alias;
}

function cancelIfEmpty($value, $logic)
{
    if (trim($value) == '') {
        return '';
    }

    return $logic;
}