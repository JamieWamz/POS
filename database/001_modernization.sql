-- Golden Tap POS modernization migration
-- Apply once to an existing pre-modernization Golden Tap database.
-- Back up the database before running this file.

SET NAMES utf8mb4;
SET time_zone = '+02:00';
SET @OLD_SQL_MODE = @@SESSION.sql_mode;
SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '');

ALTER TABLE categories
  MODIFY Category VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY Date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD UNIQUE KEY uq_categories_name (Category);

ALTER TABLE customers
  MODIFY name VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY idDocument INT NULL DEFAULT NULL,
  MODIFY email VARCHAR(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  MODIFY phone VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY address VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  MODIFY purchases INT UNSIGNED NOT NULL DEFAULT 0,
  MODIFY lastPurchase DATETIME NULL DEFAULT NULL,
  MODIFY registerDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE products
  MODIFY code VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY description VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY image VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'views/img/products/default/anonymous.png',
  MODIFY stock INT UNSIGNED NOT NULL DEFAULT 0,
  MODIFY buyingPrice DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  MODIFY sellingPrice DECIMAL(12,2) UNSIGNED NOT NULL,
  MODIFY sales INT UNSIGNED NOT NULL DEFAULT 0,
  MODIFY date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD UNIQUE KEY uq_products_code (code),
  ADD KEY idx_products_category (idCategory),
  ADD KEY idx_products_stock (stock);

ALTER TABLE sales
  ADD COLUMN taxRate DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT 0.00 AFTER products,
  MODIFY tax DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  MODIFY netPrice DECIMAL(12,2) UNSIGNED NOT NULL,
  MODIFY totalPrice DECIMAL(12,2) UNSIGNED NOT NULL,
  MODIFY paymentMethod VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  ADD COLUMN amountTendered DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL AFTER paymentMethod,
  ADD COLUMN changeDue DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0.00 AFTER amountTendered,
  MODIFY saledate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD UNIQUE KEY uq_sales_code (code),
  ADD KEY idx_sales_customer (idCustomer),
  ADD KEY idx_sales_seller (idSeller),
  ADD KEY idx_sales_date (saledate);

UPDATE sales SET amountTendered = totalPrice WHERE paymentMethod = 'cash' AND amountTendered IS NULL;
UPDATE sales SET taxRate = CASE WHEN netPrice > 0 THEN ROUND((tax / netPrice) * 100, 2) ELSE 0.00 END;

ALTER TABLE users
  MODIFY name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY user VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY password VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  MODIFY profile ENUM('Administrator', 'Special', 'Seller') NOT NULL,
  MODIFY photo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  MODIFY status TINYINT(1) NOT NULL DEFAULT 1,
  MODIFY lastLogin DATETIME NULL DEFAULT NULL,
  ADD UNIQUE KEY uq_users_username (user);

UPDATE customers SET lastPurchase = NULL WHERE lastPurchase = '0000-00-00 00:00:00';
UPDATE users SET lastLogin = NULL WHERE lastLogin = '0000-00-00 00:00:00';

CREATE TABLE expenses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  description VARCHAR(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  category VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  amount DECIMAL(12,2) UNSIGNED NOT NULL,
  id_user INT NOT NULL,
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_expenses_user (id_user),
  KEY idx_expenses_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  action VARCHAR(80) NOT NULL,
  entity_type VARCHAR(50) NOT NULL,
  entity_id VARCHAR(80) NULL,
  metadata JSON NULL,
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_activity_user (user_id),
  KEY idx_activity_created (created_at),
  KEY idx_activity_entity (entity_type, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE products
  ADD CONSTRAINT fk_products_category FOREIGN KEY (idCategory) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE sales
  ADD CONSTRAINT fk_sales_customer FOREIGN KEY (idCustomer) REFERENCES customers(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_sales_seller FOREIGN KEY (idSeller) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE expenses
  ADD CONSTRAINT fk_expenses_user FOREIGN KEY (id_user) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE activity_logs
  ADD CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL;

SET SESSION sql_mode = @OLD_SQL_MODE;
