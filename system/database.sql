-- Database: clothery_db

CREATE DATABASE IF NOT EXISTS clothery_db DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE clothery_db;

-- Table: users (สำหรับเก็บข้อมูลพนักงาน/แอดมิน)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- เก็บแบบ Hash
    fullname VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin (User: admin, Pass: 1234)
INSERT INTO users (username, password, fullname, role) VALUES 
('admin', '$2y$10$YourHashedPasswordHere', 'Administrator', 'admin');
-- หมายเหตุ: ในการใช้งานจริงต้อง Hash password ด้วย password_hash('1234', PASSWORD_DEFAULT)

-- Table: products (สินค้า)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: orders (รายการขาย)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_no VARCHAR(20) UNIQUE NOT NULL, -- เช่น ORD-20231201-001
    customer_name VARCHAR(100) DEFAULT 'Walk-in Customer',
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50), -- cash, transfer, qr
    status ENUM('completed', 'pending', 'cancelled') DEFAULT 'completed',
    user_id INT, -- ใครเป็นคนขาย
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: order_items (รายละเอียดสินค้าในออเดอร์)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL, -- ราคา ณ ตอนขาย
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ข้อมูลตัวอย่าง (Dummy Data)
INSERT INTO products (sku, name, category, price, stock, image) VALUES 
('TS-001', 'เสื้อยืด Cotton 100%', 'เสื้อยืด', 250.00, 120, 'fa-shirt'),
('JN-005', 'กางเกงยีนส์ Slim Fit', 'กางเกง', 890.00, 45, 'fa-user-tie'),
('DR-012', 'เดรสลายดอกไม้', 'เดรส', 550.00, 5, 'fa-person-dress'),
('SH-003', 'เสื้อเชิ้ตทำงาน', 'เสื้อเชิ้ต', 450.00, 0, 'fa-user-tie'),
('SK-001', 'ถุงเท้าข้อสั้น', 'ถุงเท้า', 59.00, 200, 'fa-socks');
