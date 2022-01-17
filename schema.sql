CREATE DATABASE TaskForce DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
USE TaskForce;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
    code VARCHAR(64) NOT NULL UNIQUE,
    icon VARCHAR(256) DEFAULT NULL
) COMMENT = 'Таблица категорий';

CREATE INDEX code_ind ON categories(code);

DROP TABLE IF EXISTS `documents`;
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    link VARCHAR(512) NOT NULL,
    size INT DEFAULT 0
) COMMENT = 'Таблица ссылок на дополнительные документы к заданию';

CREATE INDEX doc_ind ON documents(task_id);

DROP TABLE IF EXISTS `images`;
CREATE TABLE images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    img VARCHAR(256) NOT NULL
) COMMENT = 'Таблица фотографий работ исполнителя';

CREATE INDEX img_ind ON images(user_id);

DROP TABLE IF EXISTS `cities`;
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL
) COMMENT = 'Таблица городов';

CREATE INDEX city_ind ON cities(name);

DROP TABLE IF EXISTS `locations`;
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL COMMENT 'широта места',
    longitude DECIMAL(11, 8) DEFAULT NULL COMMENT 'долгота места',
    district VARCHAR(64) DEFAULT NULL COMMENT 'район',
    street VARCHAR(64) DEFAULT NULL COMMENT 'улица',
    info TEXT DEFAULT NULL COMMENT 'дополн. информация'
) COMMENT = 'Таблица мест выполнения заданий';

CREATE INDEX loc_ind ON locations(city_id);

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    custom_id INT NOT NULL COMMENT 'заказчик',
    contr_id INT DEFAULT 0 COMMENT 'исполнитель',
    name VARCHAR(256) NOT NULL,
    description TEXT DEFAULT NULL,
    cat_id  INT NOT NULL COMMENT 'категория задания',
    loc_id INT NOT NULL COMMENT 'локация задания',
    budget INT NOT NULL,
    add_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deadline DATETIME NOT NULL COMMENT 'срок выполнения задания',
    fin_date DATETIME DEFAULT NULL COMMENT 'фактический срок выполнения задания',
    status VARCHAR(16) DEFAULT 'new' COMMENT 'текущий стстаус задания'
) COMMENT = 'Таблица заданий';

CREATE INDEX task_ind ON tasks(name);

DROP TABLE IF EXISTS `users`;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
    email VARCHAR(64) NOT NULL UNIQUE,
    password VARCHAR(64) NOT NULL,
    add_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    contractor TINYINT(3) NOT NULL DEFAULT 0 COMMENT 'исполнитель или заказчик',
    city_id INT UNSIGNED NOT NULL DEFAULT 0
) COMMENT = 'Таблица пользователей';

CREATE INDEX user_ind ON users(email);

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    born_date DATE NOT NULL,
    avatar VARCHAR(256) DEFAULT NULL,
    last_act DATETIME DEFAULT NULL COMMENT 'дата последней активности',
    phone VARCHAR(32) DEFAULT NULL,
    messenger VARCHAR(32) DEFAULT NULL,
    social_net VARCHAR(32) DEFAULT NULL,
    address VARCHAR(256) DEFAULT NULL,
    about_info TEXT DEFAULT NULL COMMENT 'дополнительная информация о себе'
) COMMENT = 'Таблица профилей пользователей';

CREATE INDEX profile_ind ON profiles(user_id);

DROP TABLE IF EXISTS `replies`;
CREATE TABLE replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    contr_id INT NOT NULL,
    price INT NOT NULL,
    comment TEXT DEFAULT NULL,
    add_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    rating INT NOT NULL,
    status VARCHAR(16) DEFAULT NULL COMMENT 'accepted или rejected'
) COMMENT = 'Таблица откликов исполнителей';

CREATE INDEX reply_ind ON replies(add_date);

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    custom_id INT NOT NULL COMMENT 'заказчик работы',
    contr_id INT NOT NULL COMMENT 'исполнитель работы',
    add_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    comment TEXT NOT NULL COMMENT 'отзыв заказчика о работе',
    rating INT NOT NULL
) COMMENT = 'Таблица отзывов о работах исполнителей';

CREATE INDEX review_ind ON reviews(add_date);

DROP TABLE IF EXISTS `messages`;
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content VARCHAR(256) NOT NULL,
    from_id INT NOT NULL COMMENT 'от кого сообщение',
    whom_id INT NOT NULL COMMENT 'кому сообщение',
    add_date DATETIME NOT NULL, DEFAULT CURRENT_TIMESTAMP
) COMMENT = 'Таблица сообщений пользователей';

CREATE INDEX msg_ind ON messages(add_date);

DROP TABLE IF EXISTS `users_categories`;
CREATE TABLE users_categories (
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (user_id, category_id),
    CONSTRAINT fk_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_categories FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) COMMENT 'Таблица связей типа многие-ко-многим пользователи-категории';

CREATE INDEX user_ind ON users_categories(user_id);
CREATE INDEX category_ind ON users_categories(category_id);
