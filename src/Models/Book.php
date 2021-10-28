<?php

namespace src\Models;

use PDO;

class Book
{
    private PDO $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getList(int $page, int $booksOnPage): array
    {
        $start = ($page - 1) * $booksOnPage;
        $statement = $this->connection->prepare('SELECT * FROM book WHERE soft_delete != "delete" LIMIT ' . $start . ',' . $booksOnPage);
        $statement->execute();
        $books = $statement->fetchAll();

        return $books;
    }

    public function getPageCount(int $booksOnPage)
    {
        $statement = $this->connection->prepare('SELECT count(id) as total FROM book');
        $statement->execute();
        $total = $statement->fetchColumn() ?? 0;

        return round($total / $booksOnPage, 0, PHP_ROUND_HALF_UP);
    }

    public function getBook(int $book_id): array
    {
        $statement = $this->connection->prepare('SELECT * FROM book WHERE id = :book_id');
        $statement->execute([
            'book_id' => $book_id
        ]);
        $result = $statement->fetchAll();

        $result = array_shift($result);
        $result["authors"] = $this->getAuthors($result["id"]);
        return $result;
    }

    private function getAuthors(int $bookId): ?array
    {
        $statement = $this->connection->prepare("SELECT * FROM book_author WHERE id_book = :bookId");
        $statement->execute(["bookId" => $bookId]);
        $temp = $statement->fetchAll();

        $authorsIds = [];
        foreach ($temp as $item) {
            $authorsIds[] = $item["id_author"];
        }

        $temp = [];
        foreach ($authorsIds as $id) {
            $statement = $this->connection->prepare("SELECT * FROM author WHERE id = :authorId");
            $statement->execute(["authorId" => $id]);
            $temp[] = $statement->fetchAll()[0];
        }

        $authors = [];
        foreach ($temp as $item) {
            $authors[] = $item["author"];
        }

        return $authors;
    }

    public function incVisit(int $book_id)
    {
        $statement = $this->connection->prepare("UPDATE book SET visit_C = visit_C + 1 WHERE id = :id");
        $statement->execute([
            'id' => $book_id
        ]);
    }

    public function incOffer($id)
    {
        $statement = $this->connection->prepare("UPDATE book SET offer_C = offer_C + 1 WHERE id = :id");
        $statement->execute([
            'id' => $id
        ]);
    }

    public function getSearchList($query)
    {
        $statement = $this->connection->prepare("SELECT * FROM book WHERE title LIKE '%" . $query . "%'");
        $statement->execute();
        return $statement->fetchAll();
    }
}