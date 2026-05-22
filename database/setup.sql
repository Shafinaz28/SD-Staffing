-- SD Staffing — run via php/setup-database.php or phpMyAdmin

CREATE DATABASE IF NOT EXISTS sd_staffing
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sd_staffing;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  company VARCHAR(200) NOT NULL,
  location VARCHAR(50) NOT NULL,
  location_display VARCHAR(100) NOT NULL,
  area VARCHAR(120) DEFAULT '',
  salary VARCHAR(100) NOT NULL,
  experience VARCHAR(100) NOT NULL,
  category VARCHAR(50) DEFAULT 'General',
  keywords TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created (created_at DESC),
  INDEX idx_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
