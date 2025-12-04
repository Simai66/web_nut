<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['name'])) {
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (customer_name, phone, email) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['phone'] ?? '',
            $data['email'] ?? ''
        ]);

        echo json_encode([
            'success' => true,
            'customer_id' => $pdo->lastInsertId(),
            'customer_name' => $data['name']
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
