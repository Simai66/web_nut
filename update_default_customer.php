<?php
require_once 'system/db.php';

try {
    // Check if customer ID 1 exists
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = 1");
    $stmt->execute();
    $customer = $stmt->fetch();

    if ($customer) {
        // Update existing customer 1
        $update = $pdo->prepare("UPDATE customers SET customer_name = 'Unknown' WHERE customer_id = 1");
        $update->execute();
        echo "✅ Updated Customer ID 1 name to 'Unknown'.\n";
    } else {
        // Create customer 1 if not exists (though auto-increment might make ID 1 tricky if deleted, we can force it or insert)
        // Usually better to just insert and let ID be assigned, but for "default" we often assume 1.
        // Let's try to insert with explicit ID if possible, or just insert a new one and tell user.

        // Try explicit insert for ID 1
        $insert = $pdo->prepare("INSERT INTO customers (customer_id, customer_name, phone) VALUES (1, 'Unknown', '-')");
        $insert->execute();
        echo "✅ Created new Customer ID 1 with name 'Unknown'.\n";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
