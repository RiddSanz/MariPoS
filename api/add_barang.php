<?php
header('Content-Type: application/json');
require_once '../config/database.php';

check_login_api();
if (!is_admin()) {
    echo json_encode(['error' => 'Akses ditolak']);
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Metode tidak diizinkan']);
    http_response_code(405);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$price = $data['price'] ?? 0;
$stock = $data['stock'] ?? 0;
$category_id = $data['category_id'] ?? null;

if (empty($name) || $price <= 0 || $stock < 0) {
    echo json_encode(['error' => 'Semua field harus diisi dengan benar.']);
    http_response_code(400);
    exit();
}

// Get category prefix
$query = "SELECT prefix FROM categories WHERE id = :category_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':category_id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    echo json_encode(['error' => 'Kategori tidak ditemukan.']);
    http_response_code(400);
    exit();
}

$prefix = $category['prefix'];

// Generate unique SKU
do {
    $rand = rand(100000, 999999);
    $sku = $prefix . '-' . $rand;

    // Cek duplikasi SKU
    $query = "SELECT id FROM products WHERE sku = :sku";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':sku', $sku);
    $stmt->execute();
    $rowCount = $stmt->rowCount();
} while ($rowCount > 0);


$query = "INSERT INTO products (sku, name, price, stock, category_id) VALUES (:sku, :name, :price, :stock, :category_id)";
$stmt = $conn->prepare($query);

$stmt->bindParam(':sku', $sku);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':price', $price);
$stmt->bindParam(':stock', $stock);
$stmt->bindParam(':category_id', $category_id);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Barang berhasil ditambahkan.', 'id' => $conn->lastInsertId()]);
} else {
    echo json_encode(['error' => 'Gagal menambahkan barang.']);
    http_response_code(500);
}
?>
