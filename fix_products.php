<?php
require_once 'system/db.php';

echo "<h1>Fixing Products...</h1>";

try {
    // 1. Check if products table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() == 0) {
        // Create table if missing (based on schema)
        $sql = "CREATE TABLE IF NOT EXISTS products (
            product_id INT AUTO_INCREMENT PRIMARY KEY,
            product_name VARCHAR(200) NOT NULL,
            category VARCHAR(100),
            price DECIMAL(10, 2) NOT NULL,
            stock INT DEFAULT 0,
            image VARCHAR(255) DEFAULT 'fa-box',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        echo "<p>✅ Created 'products' table.</p>";
    }

    // 2. Check count
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insert sample products
        $sql = "INSERT INTO products (product_name, category, price, stock, image) VALUES 
        ('T-Shirt Basic (White)', 'T-Shirts', 199.00, 50, 'fa-tshirt'),
        ('Jeans Slim Fit', 'Pants', 590.00, 30, 'fa-user-astronaut'),
        ('Sneakers Red', 'Shoes', 1200.00, 15, 'fa-shoe-prints'),
        ('Cap Black', 'Accessories', 150.00, 20, 'fa-hat-cowboy'),
        ('Hoodie Grey', 'Jackets', 890.00, 25, 'fa-user-secret')";

        $pdo->exec($sql);
        echo "<p>✅ Inserted 5 sample products.</p>";
    } else {
        echo "<p>ℹ️ Products table already has $count items.</p>";
    }

    echo "<h2>🎉 Fix Complete!</h2>";
    echo "<p><a href='system/pos.php' style='font-size:20px; font-weight:bold;'>Click here to go to POS</a></p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
