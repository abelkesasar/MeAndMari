CREATE DATABASE IF NOT EXISTS me_and_mari;
USE me_and_mari;

CREATE TABLE IF NOT EXISTS memories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    photo VARCHAR(255) NOT NULL,
    happy_meter VARCHAR(50) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
