<?php

namespace src\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use src\Models\Book;

class BookController
{
    private Book $book;

    private $view;

    public function __construct($connection)
    {
        $this->book = new Book($connection);
        $loader = new FilesystemLoader('src/Views');
        $this->view = new Environment($loader);
    }

    public function getList(array $query)
    {
        $page = 1;
        $totalBooksOnPage = 20;

        if (array_key_exists('page', $query)) {
            $page = $query['page'];
        }

        $body = $this->view->render('index.twig', [
            'books' => $this->book->getList($page, $totalBooksOnPage),
            'pagination' => $this->book->getPageCount($totalBooksOnPage)
        ]);
        echo $body;
    }

    public function getBook(array $query)
    {
        $book_id = 0;
        if (array_key_exists('bookId', $query)) {
            $book_id = $query['bookId'];
        }
        $this->book->incVisit($book_id);
        $book = $this->book->getBook($book_id);
        if (!empty($book)) {
            $body = $this->view->render('book.twig', [
                'book' => $book
            ]);
        }

        echo $body;
    }

    public function incOffer(array $query)
    {
        $book_id = 0;
        if (array_key_exists('bookId', $query)) {
            $book_id = $query['bookId'];
        }
        $this->book->incOffer($book_id);
    }

    public function getSearchList($query)
    {
        $searchQuery = $query["query"];

        if (empty($books))
            $body = $this->view->render('search.twig', [
                'books' => $this->book->getSearchList($searchQuery)
            ]);

        echo $body;

    }
}