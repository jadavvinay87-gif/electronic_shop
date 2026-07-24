<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "electronic_shop";

$conn = new mysqli($host, $user, $pass);
$conn->query("DROP DATABASE IF EXISTS electronic_shop");
$conn->query("CREATE DATABASE electronic_shop");
$conn->select_db("electronic_shop");

$sql = "
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    brand_id INT,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount_price DECIMAL(10,2) DEFAULT 0.00,
    quantity INT DEFAULT 0,
    description TEXT,
    specifications TEXT,
    warranty VARCHAR(100),
    main_image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_latest BOOLEAN DEFAULT TRUE,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
    order_status ENUM('Pending', 'Processing', 'Completed', 'Cancelled') DEFAULT 'Pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Admins
INSERT INTO admins (username, email, password) VALUES ('admin', 'admin@shop.com', '".password_hash('admin', PASSWORD_DEFAULT)."');

-- Seed Settings
INSERT INTO settings (setting_key, setting_value) VALUES 
('store_name', 'ElectroShop'),
('store_email', 'contact@electroshop.com'),
('currency', '$'),
('store_phone', '+1 234 567 8900'),
('store_address', '123 Tech Lane, Silicon Valley, CA 94025');

-- Seed Categories
INSERT INTO categories (name) VALUES ('Smartphones'), ('Laptops'), ('Audio'), ('Wearables'), ('Accessories');

-- Seed Brands
INSERT INTO brands (name) VALUES ('Apple'), ('Samsung'), ('Sony'), ('Dell'), ('Bose');

-- Seed Users
INSERT INTO users (name, email, password, phone, address) VALUES 
('John Doe', 'john@example.com', '".password_hash('password123', PASSWORD_DEFAULT)."', '555-0101', '123 Main St, City, ST'),
('Jane Smith', 'jane@example.com', '".password_hash('password123', PASSWORD_DEFAULT)."', '555-0202', '456 Oak Ave, Town, ST');

-- Seed Products
INSERT INTO products (category_id, brand_id, name, price, discount_price, quantity, description, is_featured) VALUES 
(1, 1, 'iPhone 15 Pro', 999.00, 0.00, 50, 'The latest iPhone with titanium design.', 1),
(1, 2, 'Galaxy S24 Ultra', 1199.00, 1099.00, 30, 'Samsung flagship with S Pen.', 1),
(2, 1, 'MacBook Pro 14\"', 1999.00, 1899.00, 15, 'M3 Pro chip, 16GB RAM, 512GB SSD.', 1),
(2, 4, 'XPS 15', 1499.00, 0.00, 20, 'Dell XPS 15 with OLED display.', 0),
(3, 3, 'WH-1000XM5', 398.00, 348.00, 100, 'Noise cancelling wireless headphones.', 1),
(4, 1, 'Apple Watch Series 9', 399.00, 0.00, 45, 'Smartwatch with advanced health features.', 0);

-- Seed Orders
INSERT INTO orders (user_id, total_amount, payment_method, payment_status, order_status, shipping_address) VALUES 
(1, 999.00, 'Credit Card', 'Paid', 'Completed', '123 Main St, City, ST'),
(2, 1499.00, 'PayPal', 'Pending', 'Processing', '456 Oak Ave, Town, ST'),
(1, 398.00, 'Credit Card', 'Paid', 'Pending', '123 Main St, City, ST');

-- Seed Order Items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES 
(1, 1, 1, 999.00),
(2, 4, 1, 1499.00),
(3, 5, 1, 398.00);
";

if ($conn->multi_query($sql)) {
    echo "Database created successfully.";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
