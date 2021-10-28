<?php


$DBConfig = require_once __DIR__ . '/../config/database.php';

$dsn = $DBConfig['dsn'];
$username = $DBConfig['username'];
$password = $DBConfig['password'];
try {
    $connection = new PDO($dsn, $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    echo 'Database error: ' . $exception->getMessage();
    exit();
}

return $connection;