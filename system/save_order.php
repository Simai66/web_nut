<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['cart']) || empty($data['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Calculate Total
        $total = 0;
        foreach ($data['cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $total_with_tax = $total * 1.07;

        // Create Order
        $customer_id = isset($data['customer_id']) ? $data['customer_id'] : 1;

        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$customer_id, $total_with_tax]);
        $order_id = $pdo->lastInsertId();

        // Create Order Items and Update Stock
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_each) VALUES (?, ?, ?, ?)");
        $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");

        foreach ($data['cart'] as $item) {
            $stmt_item->execute([$order_id, $item['product_id'], $item['qty'], $item['price']]);
            $stmt_stock->execute([$item['qty'], $item['product_id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
