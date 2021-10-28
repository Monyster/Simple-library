<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use src\Controllers\BookController;
use src\Controllers\AdminController;


$connection = require_once "src/DBConnection.php";
$bookController = new BookController($connection);
$adminController = new AdminController($connection);

// Create app
$app = AppFactory::create();

$app->get('/search', function (Request $request) use ($bookController) {
    $query = $request->getQueryParams();
    $bookController->getSearchList($query);
});

$app->get('/login', function () {
    if ($_SERVER['PHP_AUTH_USER'] != 'adminka') {
        header("HTTP/1.1 401 Unauthorized");
        header('WWW-Authenticate: Basic realm="Input username and password"');
        header('Content-Type: text/html');
        exit;
    }
    header("Location: /admin");
});

$app->get('/logout', function () {
    header("Location:  http://log:log@back.loc/login");
    header('Content-Type: text/html');
    exit;
});

$app->post('/admin/addBook', function (Request $request) use ($adminController) {
    $adminController->addBook();
});

$app->get('/admin/delete', function (Request $request) use ($adminController) {
    $query = $request->getQueryParams();
    $adminController->deleteBook($query);
});

$app->get('/admin', function (Request $request) use ($adminController) {
    $query = $request->getQueryParams();
    $adminController->getList($query);
});

$app->get('/books/incOffer', function (Request $request) use ($bookController) {
    $query = $request->getQueryParams();
    $bookController->incOffer($query);
});

$app->get('/books', function (Request $request) use ($bookController) {
    $query = $request->getQueryParams();
    $bookController->getBook($query);
});

$app->get('/', function (Request $request) use ($bookController) {
    $query = $request->getQueryParams();
    $bookController->getList($query);
});


$app->run();