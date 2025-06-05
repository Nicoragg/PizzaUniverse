-- Create Pizza Universe database
CREATE DATABASE IF NOT EXISTS universe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE universe_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);

-- Comments for documentation
ALTER TABLE users COMMENT = 'Users table for Pizza Universe system';
ALTER TABLE users MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique user ID';
ALTER TABLE users MODIFY COLUMN name VARCHAR(100) NOT NULL COMMENT 'User full name';
ALTER TABLE users MODIFY COLUMN email VARCHAR(150) NOT NULL UNIQUE COMMENT 'Unique user email for login';
ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL COMMENT 'Encrypted user password';
ALTER TABLE users MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation date';
ALTER TABLE users MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update date';