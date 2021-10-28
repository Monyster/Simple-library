<?php
try {
    $connection = new PDO('mysql:host=localhost;dbname=biblioteka', 'root', 'ПАРОЛЬ');
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    echo 'Database error: ' . $exception->getMessage();
    exit();
}

$statement = $connection->prepare('DELETE FROM book WHERE soft_delete = "delete"');
$statement->execute();