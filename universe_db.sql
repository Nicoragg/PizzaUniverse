DROP DATABASE IF EXISTS universe_db;

CREATE DATABASE IF NOT EXISTS universe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE universe_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);

INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@admin.com', MD5('admin123'));
