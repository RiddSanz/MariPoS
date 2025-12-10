<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$transaction_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($transaction_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID Transaksi tidak valid']);
    exit();
}

// Jika bukan admin, pastikan user hanya bisa melihat detail transaksinya sendiri
if (!is_admin()) {
    $stmt = $conn->prepare("SELECT id FROM transactions WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $transaction_id, 'user_id' => $_SESSION['user_id']]);
    if ($stmt->rowCount() == 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Akses ditolak']);
        exit();
    }
}

$stmt = $conn->prepare("SELECT td.quantity, td.price, p.name 
                        FROM transaction_details td 
                        JOIN products p ON td.product_id = p.id 
                        WHERE td.transaction_id = :id");
$stmt->execute(['id' => $transaction_id]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($details);
?>