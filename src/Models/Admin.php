<?php

namespace src\Models;

use PDO;

class Admin
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

    public function deleteBook($book_id)
    {
        $statement = $this->connection->prepare("
        UPDATE book
        SET soft_delete = 'delete'
        WHERE id = :id");
        $statement->execute([
            'id' => $book_id
        ]);
    }

    public function addBook($cover, $title, $year, $description, $authors)
    {
        $image_path = $this->uploadCover($cover);

        $authorIds = [];
        foreach ($authors as $author) {
            $authorIds[] = $this->insertAuthor($author);
        }

        //TODO: Если одинаковые автора, то потом не создаются связи для новой книги. Нужно всеравно получить айди автора но не добавлять его и создать связи

        $bookId = $this->insertBook($title, $year, $description, $image_path);

        foreach ($authorIds as $authorId) {
            if ($authorId != NULL) {
                $this->makePair($authorId, $bookId);
            }
        }
    }


    private function insertAuthor($author)
    {
        $statement = $this->connection->prepare('
    INSERT INTO author (author)
    SELECT * FROM (SELECT :author) AS tmp
    WHERE NOT EXISTS (
            SELECT author FROM author WHERE author = :author
        ) LIMIT 1;
    ');
        $statement->execute(['author' => $author]);

        return $this->getLastId('author');
    }

    private function insertBook($title, $year, $description, $image_path)
    {
        $statement = $this->connection->prepare('
INSERT INTO book(title, year, description, image, soft_delete)
VALUES (:title, :bookYear, :description, :image, "no")');
        $statement->execute([
            'title' => $title,
            'bookYear' => $year,
            'description' => $description,
            'image' => $image_path
        ]);

        return $this->getLastId('book');
    }

    private function getLastId($table_name)
    {
        $statement = $this->connection->prepare('SELECT * FROM ' . $table_name . ' WHERE id = LAST_INSERT_ID()');
        $statement->execute();
        return $statement->fetchAll()[0]['id'];
    }

    private function makePair($authorId, $bookId)
    {
        $statement = $this->connection->prepare('
INSERT INTO book_author(id_author, id_book)
VALUES (:authorId, :bookId)');
        $statement->execute([
            'authorId' => $authorId,
            'bookId' => $bookId
        ]);
    }

    private function uploadCover($cover): string
    {
        $rootDir = $_SERVER['DOCUMENT_ROOT'];
        $coverName = $cover['name'];
        $newCoverName = date('Y-m-d-H-i-s') . '_' . uniqid() . $coverName;

        $uploadFrom = $cover['tmp_name'];

        $uploadTo = $rootDir . "/upload/" . $newCoverName;
        move_uploaded_file($uploadFrom, $uploadTo);

// CHMOD file
        chmod($uploadTo, 0777);
        return $newCoverName;
    }
}