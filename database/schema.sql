-- Golden Tap POS clean installation schema
-- MySQL 8.0+ or MariaDB 10.6+

SET NAMES utf8mb4;
SET time_zone = '+02:00';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  user VARCHAR(50) NOT NULL,
  password VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  profile ENUM('Administrator', 'Special', 'Seller') NOT NULL,
  photo VARCHAR(255) NOT NULL DEFAULT '',
  status TINYINT(1) NOT NULL DEFAULT 1,
  lastLogin DATETIME NULL DEFAULT NULL,
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Category VARCHAR(80) NOT NULL,
  Date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (Category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customers (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  idDocument INT NULL DEFAULT NULL,
  email VARCHAR(254) NOT NULL DEFAULT '',
  phone VARCHAR(30) NOT NULL,
  address VARCHAR(255) NOT NULL DEFAULT '',
  birthdate DATE NOT NULL,
  purchases INT UNSIGNED NOT NULL DEFAULT 0,
  lastPurchase DATETIME NULL DEFAULT NULL,
  registerDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_customers_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idCategory INT UNSIGNED NOT NULL,
  code VARCHAR(40) NOT NULL,
  description VARCHAR(160) NOT NULL,
  image VARCHAR(255) NOT NULL DEFAULT 'views/img/products/default/anonymous.png',
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  buyingPrice DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  sellingPrice DECIMAL(12,2) UNSIGNED NOT NULL,
  sales INT UNSIGNED NOT NULL DEFAULT 0,
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_products_code (code),
  KEY idx_products_category (idCategory),
  KEY idx_products_stock (stock),
  CONSTRAINT fk_products_category FOREIGN KEY (idCategory) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sales (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code INT UNSIGNED NOT NULL,
  idCustomer INT UNSIGNED NOT NULL,
  idSeller INT UNSIGNED NOT NULL,
  products LONGTEXT NOT NULL,
  taxRate DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT 0.00,
  tax DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  netPrice DECIMAL(12,2) UNSIGNED NOT NULL,
  totalPrice DECIMAL(12,2) UNSIGNED NOT NULL,
  paymentMethod VARCHAR(80) NOT NULL,
  amountTendered DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
  changeDue DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  saledate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sales_code (code),
  KEY idx_sales_customer (idCustomer),
  KEY idx_sales_seller (idSeller),
  KEY idx_sales_date (saledate),
  CONSTRAINT fk_sales_customer FOREIGN KEY (idCustomer) REFERENCES customers(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_sales_seller FOREIGN KEY (idSeller) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expenses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  description VARCHAR(180) NOT NULL,
  category VARCHAR(80) NOT NULL,
  amount DECIMAL(12,2) UNSIGNED NOT NULL,
  id_user INT UNSIGNED NOT NULL,
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_expenses_user (id_user),
  KEY idx_expenses_date (date),
  CONSTRAINT fk_expenses_user FOREIGN KEY (id_user) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  action VARCHAR(80) NOT NULL,
  entity_type VARCHAR(50) NOT NULL,
  entity_id VARCHAR(80) NULL,
  metadata JSON NULL,
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_activity_user (user_id),
  KEY idx_activity_created (created_at),
  KEY idx_activity_entity (entity_type, entity_id),
  CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
