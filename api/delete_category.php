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
    echo json_encode(['error' => 'Method not allowed']);
    http_response_code(405);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['id']) || !is_numeric($input['id'])) {
    echo json_encode(['error' => 'Invalid ID']);
    http_response_code(400);
    exit();
}

try {
    // Check if category is in use
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = :id");
    $stmt->execute(['id' => $input['id']]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['error' => 'Cannot delete: category has products']);
        http_response_code(400);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $input['id']]);
    
    if ($stmt->rowCount() === 0) {
        echo json_encode(['error' => 'Category not found']);
        http_response_code(404);
    } else {
        echo json_encode(['success' => true]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}