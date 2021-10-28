create table if not exists book_author
(
    id_author int(6) unsigned null,
    id_book   int(6) unsigned null,
    constraint book_author_book_id_fk
        foreign key (id_book) references book (id)
            on delete cascade
);