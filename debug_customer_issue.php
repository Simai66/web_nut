<?php
require_once 'system/db.php';

echo "=== CUSTOMERS TABLE ===\n";
$stmt = $pdo->query("SELECT * FROM customers");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($customers as $c) {
    echo "ID: " . $c['customer_id'] . " | Name: " . $c['customer_name'] . "\n";
}

echo "\n=== ORDERS TABLE STRUCTURE ===\n";
$stmt = $pdo->query("DESCRIBE orders");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\n=== RECENT ORDERS ===\n";
$stmt = $pdo->query("SELECT * FROM orders ORDER BY order_id DESC LIMIT 5");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($orders as $o) {
    echo "Order ID: " . $o['order_id'] . " | Customer ID: " . $o['customer_id'];
    if (isset($o['customer_name'])) {
        echo " | Customer Name (in orders table): " . $o['customer_name'];
    }
    echo "\n";
}
