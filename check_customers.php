<?php
require_once 'system/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM customers LIMIT 1");
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Table 'customers' exists.\n";
    if ($customer) {
        echo "Columns: " . implode(", ", array_keys($customer)) . "\n";
    } else {
        echo "Table is empty.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
