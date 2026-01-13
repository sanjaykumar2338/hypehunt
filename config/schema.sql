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
  is_unsubscribed TINYINT(1) DEFAULT 0,
  unsubscribed_at DATETIME NULL,
  confirm_email_sent TINYINT(1) DEFAULT 0,
  confirm_email_sent_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_email (email)
);

CREATE TABLE IF NOT EXISTS notify_signups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  user_type ENUM('collector','reseller','both','unknown') DEFAULT 'unknown',
  ip_address VARCHAR(45) NULL,
  is_unsubscribed TINYINT(1) DEFAULT 0,
  unsubscribed_at DATETIME NULL,
  confirm_email_sent TINYINT(1) DEFAULT 0,
  confirm_email_sent_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_email (email)
);

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS unsubscribe_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  list_type ENUM('early_access','notify') NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt TEXT NULL,
  content LONGTEXT NOT NULL,
  featured_image VARCHAR(255) NULL,
  status ENUM('draft','published') DEFAULT 'draft',
  published_at DATETIME NULL,
  meta_title VARCHAR(255) NULL,
  meta_description VARCHAR(300) NULL,
  canonical_url VARCHAR(255) NULL,
  og_title VARCHAR(255) NULL,
  og_description VARCHAR(300) NULL,
  og_image VARCHAR(255) NULL,
  twitter_title VARCHAR(255) NULL,
  twitter_description VARCHAR(300) NULL,
  twitter_image VARCHAR(255) NULL,
  tags VARCHAR(255) NULL,
  author VARCHAR(120) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default admin credentials:
-- username: admin
-- password: Admin@123
INSERT INTO admin_users (username, password_hash)
VALUES ('admin', '$2y$10$i.iET4b0oHsSvUzHimgiE.4LYbyl2mgMnZiMQ.MYqfGxJTFOAh7Uu')
ON DUPLICATE KEY UPDATE username = VALUES(username);
