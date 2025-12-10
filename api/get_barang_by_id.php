<?php
header('Content-Type: application/json');
require_once '../config/database.php';

check_login_api();
if (!is_admin()) {
    echo json_encode(['error' => 'Akses ditolak']);
    http_response_code(403);
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['error' => 'ID tidak valid']);
    http_response_code(400);
    exit();
}

$query = "SELECT id, sku, name, price, stock, category_id FROM products WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($product);
} else {
    echo json_encode(['error' => 'Barang tidak ditemukan']);
    http_response_code(404);
}
?>