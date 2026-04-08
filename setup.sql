CREATE DATABASE IF NOT EXISTS primeboats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE primeboats;

CREATE TABLE IF NOT EXISTS boats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price_per_day DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    capacity INT NOT NULL DEFAULT 1,
    length_m DECIMAL(5,2) DEFAULT NULL,
    year INT DEFAULT NULL,
    images JSON DEFAULT NULL,
    is_rented TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    boat_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (boat_id) REFERENCES boats(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

-- Default admin: username=admin, password=changeme123 (change after first login!)
INSERT INTO admin_users (username, password_hash)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
