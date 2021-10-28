<?php

namespace src\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use src\Models\Admin;

class AdminController
{
    private Admin $admin;

    private $view;

    public function __construct($connection)
    {
        $this->admin = new Admin($connection);
        $loader = new FilesystemLoader('src/Views');
        $this->view = new Environment($loader);

    }

    public function getList(array $query)
    {
        $page = 1;
        $totalBooksOnPage = 10;

        if (array_key_exists('page', $query)) {
            $page = $query['page'];
        }

        $body = $this->view->render('admin.twig', [
            'books' => $this->admin->getList($page, $totalBooksOnPage),
            'pagination' => $this->admin->getPageCount($totalBooksOnPage)
        ]);
        echo $body;
    }

    public function deleteBook(array $query)
    {
        $book_id = 0;
        if (array_key_exists('bookId', $query)) {
            $book_id = $query['bookId'];
        }
        $this->admin->deleteBook($book_id);
    }

    public function addBook()
    {
        $cover = $_FILES['bookCover'];
        $title = $_POST['bookTitle'];
        $year = (int)$_POST['bookYear'];
        $description = $_POST['bookDescription'];
        $authors = [$_POST['bookAuthor1'], $_POST['bookAuthor2'], $_POST['bookAuthor3']];

        $this->admin->addBook($cover, $title, $year, $description, $authors);
        header("Location: /admin");
    }
}