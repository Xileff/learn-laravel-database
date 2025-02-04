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

SELECT * FROM categories;

SELECT `category_id`, COUNT(id) AS total_product FROM `products` 
GROUP BY `category_id` ORDER BY `category_id` DESC;

DROP table products;
DROP table categories;
DROP table counters;