<?php

$connection = require_once __DIR__ . "../src/DBConnection.php";

$file = file_get_contents(__DIR__ . 'migration/26.10.2021_21:22_create_author_table.sql', false);
$statement = $connection->prepare($file);
$statement->execute();

$file = file_get_contents(__DIR__ . 'migration/26.10.2021_21:22_create_book_table.sql', false);
$statement = $connection->prepare($file);
$statement->execute();

$file = file_get_contents(__DIR__ . 'migration/26.10.2021_21:22_create_book_author_table.sql', false);
$statement = $connection->prepare($file);
$statement->execute();
