CREATE DATABASE db_learn_laravel_database;

CREATE TABLE categories
(
	id varchar(100) not null primary key,
    name varchar(100) not null,
    description text,
    created_at timestamp
) engine innodb;

CREATE TABLE counters
(
	id varchar(100) not null primary key,
	counter int not null default 0
) engine innodb;

CREATE TABLE products
(
	id varchar(100) not null primary key,
    name varchar(100) not null,
    description text,
    price int not null,
    category_id varchar(100) not null,
    created_at timestamp,
    constraint fk_category_id foreign key (category_id) references categories(id)
) engine innodb;