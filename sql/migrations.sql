CREATE DATABASE IF NOT EXISTS orders_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE orders_db;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  items JSON NOT NULL,
  vip TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('active','completed') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  pickup_time DATETIME NOT NULL,
  completed_at DATETIME NULL,
  INDEX orders_idx_status (status),
  INDEX orders_idx_pickup_time (pickup_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;