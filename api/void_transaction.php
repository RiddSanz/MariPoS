<?php
require_once '../config/database.php';
check_login();

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$transaction_id = (int)$data['id'];

try {
    // Check if transaction exists and is completed
    $stmt = $conn->prepare("SELECT status FROM transactions WHERE id = ?");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['error' => 'Transaction not found']);
        exit();
    }

    if ($transaction['status'] !== 'completed') {
        http_response_code(400);
        echo json_encode(['error' => 'Only completed transactions can be voided']);
        exit();
    }

    // Start transaction
    $conn->beginTransaction();

    // Get transaction details to restore stock
    $stmt = $conn->prepare("SELECT product_id, quantity FROM transaction_details WHERE transaction_id = ?");
    $stmt->execute([$transaction_id]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Restore stock
    $update_stock_stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    foreach ($details as $detail) {
        $update_stock_stmt->execute([$detail['quantity'], $detail['product_id']]);
    }

    // Update transaction status to voided
    $stmt = $conn->prepare("UPDATE transactions SET status = 'voided' WHERE id = ?");
    $stmt->execute([$transaction_id]);

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
