-- Database: hypehunt_db

CREATE TABLE IF NOT EXISTS early_access (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(30) NULL,
  password_hash VARCHAR(255) NOT NULL,
  comments TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_email (email)
);

CREATE TABLE IF NOT EXISTS notify_signups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  user_type ENUM('collector','reseller','both','unknown') DEFAULT 'unknown',
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_email (email)
);

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin credentials:
-- username: admin
-- password: Admin@123
INSERT INTO admin_users (username, password_hash)
VALUES ('admin', '$2y$10$i.iET4b0oHsSvUzHimgiE.4LYbyl2mgMnZiMQ.MYqfGxJTFOAh7Uu')
ON DUPLICATE KEY UPDATE username = VALUES(username);
