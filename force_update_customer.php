<?php
require_once 'system/db.php';

try {
    echo "Updating Customer ID 1...\n";
    $stmt = $pdo->prepare("UPDATE customers SET customer_name = 'Unknown' WHERE customer_id = 1");
    $stmt->execute();
    echo "✅ Updated Customer ID 1 to 'Unknown'.\n";

    // Verify
    $stmt = $pdo->query("SELECT * FROM customers WHERE customer_id = 1");
    $c = $stmt->fetch();
    echo "Current Name for ID 1: " . $c['customer_name'] . "\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
