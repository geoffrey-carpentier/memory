-- Script SQL de base pour le mini-projet MVC

CREATE DATABASE IF NOT EXISTS mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mvc;

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;