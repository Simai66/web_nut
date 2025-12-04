<?php
require_once 'system/db.php';

echo "<h1>Fixing Login System...</h1>";

try {
    // 1. Create Users Table
    $sql_create = "CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'staff') DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_create);
    echo "<p>✅ Users table checked/created.</p>";

    // 2. Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        // Update password to 1234
        $stmt = $pdo->prepare("UPDATE users SET password_hash = '1234' WHERE username = 'admin'");
        $stmt->execute();
        echo "<p>✅ Admin password reset to '1234'.</p>";
    } else {
        // Insert admin
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role) VALUES ('admin', '1234', 'Administrator', 'admin')");
        $stmt->execute();
        echo "<p>✅ Admin user created (Password: 1234).</p>";
    }

    echo "<h2>🎉 Fix Complete!</h2>";
    echo "<p><a href='system/login.php' style='font-size:20px; font-weight:bold;'>Click here to Login</a></p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
