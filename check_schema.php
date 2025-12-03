<?php
require_once 'system/db.php';

$tables = ['products', 'orders', 'customers', 'users', 'order_items'];

foreach ($tables as $table) {
    echo "<h2>Table: $table</h2>";
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            foreach ($col as $key => $val) {
                echo "<td>$val</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error describing $table: " . $e->getMessage() . "</p>";
    }
}
