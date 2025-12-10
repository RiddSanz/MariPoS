<?php
header('Content-Type: application/json');
require_once '../config/database.php';

check_login_api();
// Hanya admin yang bisa mengakses
if (!is_admin()) {
    echo json_encode(['error' => 'Akses ditolak']);
    http_response_code(403);
    exit();
}

$query = "SELECT p.id, p.sku, p.name, p.price, p.stock, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>