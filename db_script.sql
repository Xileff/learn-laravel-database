CREATE DATABASE db_learn_laravel_database;

CREATE TABLE categories
(
	id varchar(100) not null primary key,
    name varchar(100) not null,
    description text,
    created_at timestamp
) engine innodb;