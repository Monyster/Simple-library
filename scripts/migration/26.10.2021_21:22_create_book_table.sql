create table if not exists book
(
    id          int(6) unsigned auto_increment
        primary key,
    title       varchar(255)              not null,
    description varchar(255)              not null,
    year        varchar(4)                not null,
    image       varchar(255)              not null,
    visit_C     int(8) unsigned default 0 null,
    offer_C     int(8) unsigned default 0 null,
    soft_delete varchar(10)               null
);