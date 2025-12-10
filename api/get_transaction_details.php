<?php
require_once '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Transaction ID required']);
    exit();
}

try {
    // Get transaction
    $stmt = $conn->prepare("SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
    $stmt->execute([$id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['error' => 'Transaction not found']);
        exit();
    }

    // Get transaction details
    $stmt = $conn->prepare("SELECT td.*, p.name FROM transaction_details td JOIN products p ON td.product_id = p.id WHERE td.transaction_id = ?");
    $stmt->execute([$id]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if can void (admin and status completed)
    $canVoid = is_admin() && $transaction['status'] === 'completed';

    echo json_encode([
        'transaction' => $transaction,
        'details' => $details,
        'canVoid' => $canVoid
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
