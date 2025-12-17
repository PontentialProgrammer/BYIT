CREATE DATABASE IF NOT EXISTS eComm;
USE eComm;

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(225) NOT NULL
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE order_items (
    order_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);


ALTER TABLE customers 
ADD COLUMN otp_code VARCHAR(6) NULL,
ADD COLUMN otp_expires_at DATETIME NULL,
ADD COLUMN is_verified TINYINT(1) DEFAULT 0;

ALTER TABLE products
ADD COLUMN product_desc VARCHAR(255) NOT NULL,
ADD COLUMN image_path VARCHAR(255) NOT NULL;