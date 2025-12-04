<?php
require_once 'system/db.php';

try {
    // List all customers
    $stmt = $pdo->query("SELECT * FROM customers");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Current Customers:\n";
    foreach ($customers as $c) {
        echo "ID: " . $c['customer_id'] . " | Name: " . $c['customer_name'] . "\n";
    }

    // Update 'Somchai Jinda' to 'Unknown'
    $update = $pdo->prepare("UPDATE customers SET customer_name = 'Unknown' WHERE customer_name = 'Somchai Jinda'");
    $update->execute();

    if ($update->rowCount() > 0) {
        echo "\n✅ Updated " . $update->rowCount() . " record(s) named 'Somchai Jinda' to 'Unknown'.\n";
    } else {
        echo "\n⚠️ No customer named 'Somchai Jinda' found to update.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
