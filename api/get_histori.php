<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$query = "SELECT t.id, t.created_at, t.total_amount, u.username 
          FROM transactions t 
          JOIN users u ON t.user_id = u.id";

// Jika bukan admin, hanya tampilkan transaksi milik user yang login
if (!is_admin()) {
    $query .= " WHERE t.user_id = :user_id";
}

$query .= " ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);

if (!is_admin()) {
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
}

$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($transactions);
?>