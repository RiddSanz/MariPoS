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

$id = $data['id'] ?? 0;
$sku = $data['sku'] ?? '';
$name = $data['name'] ?? '';
$price = $data['price'] ?? 0;
$stock = $data['stock'] ?? 0;
$category_id = $data['category_id'] ?? null;

// Validate input
if ($id <= 0 || empty($name) || $price <= 0 || $stock < 0) {
    echo json_encode(['error' => 'Semua field harus diisi dengan benar.']);
    http_response_code(400);
    exit();
}

try {
    // Check if product exists
    $checkQuery = "SELECT id FROM products WHERE id = :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Barang tidak ditemukan.']);
        http_response_code(404);
        exit();
    }

    // Validate category exists and get prefix
    if ($category_id !== null) {
        $categoryQuery = "SELECT prefix FROM categories WHERE id = :category_id";
        $categoryStmt = $conn->prepare($categoryQuery);
        $categoryStmt->execute(['category_id' => $category_id]);
        $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) {
            echo json_encode(['error' => 'Kategori tidak ditemukan.']);
            http_response_code(400);
            exit();
        }

        $prefix = $category['prefix'];
        
        // Validate SKU format matches category prefix
        if (!empty($sku) && strpos($sku, $prefix . '-') !== 0) {
            echo json_encode(['error' => 'SKU harus dimulai dengan prefix kategori: ' . $prefix . '-']);
            http_response_code(400);
            exit();
        }
    }

    // Check SKU duplication (excluding current product)
    if (!empty($sku)) {
        $skuQuery = "SELECT id FROM products WHERE sku = :sku AND id != :id";
        $skuStmt = $conn->prepare($skuQuery);
        $skuStmt->execute(['sku' => $sku, 'id' => $id]);
        if ($skuStmt->rowCount() > 0) {
            echo json_encode(['error' => 'SKU sudah digunakan oleh barang lain.']);
            http_response_code(409);
            exit();
        }
    }

    // Update product
    $updateQuery = "UPDATE products SET name = :name, price = :price, stock = :stock, category_id = :category_id";
    $params = [
        'name' => $name,
        'price' => $price,
        'stock' => $stock,
        'category_id' => $category_id,
        'id' => $id
    ];
    
    // Only update SKU if provided
    if (!empty($sku)) {
        $updateQuery .= ", sku = :sku";
        $params['sku'] = $sku;
    }
    
    $updateQuery .= " WHERE id = :id";
    
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute($params);
    
    echo json_encode(['success' => 'Barang berhasil diperbarui.']);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Gagal memperbarui barang: ' . $e->getMessage()]);
    http_response_code(500);
}
?>