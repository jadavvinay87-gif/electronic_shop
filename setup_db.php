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

INSERT INTO admins (username, email, password) VALUES ('admin', 'admin@shop.com', '".password_hash('admin', PASSWORD_DEFAULT)."');
";

if ($conn->multi_query($sql)) {
    echo "Database created successfully.";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
