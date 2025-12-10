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
if (!isset($input['id']) || !is_numeric($input['id']) || !isset($input['name']) || !isset($input['prefix'])) {
    echo json_encode(['error' => 'Invalid input']);
    http_response_code(400);
    exit();
}

$id = (int)$input['id'];
$name = trim($input['name']);
$prefix = trim($input['prefix']);
if ($name === '') {
    echo json_encode(['error' => 'Name required']);
    http_response_code(400);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE categories SET name = :name, prefix = :prefix WHERE id = :id");
    $stmt->execute(['name' => $name, 'prefix' => $prefix, 'id' => $id]);
    if ($stmt->rowCount() === 0) {
        // Maybe no change or not found; check existence
        $check = $conn->prepare("SELECT id FROM categories WHERE id = :id");
        $check->execute(['id' => $id]);
        if ($check->rowCount() == 0) {
            echo json_encode(['error' => 'Category not found']);
            http_response_code(404);
            exit();
        }
    }
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['error' => 'Category name already exists']);
        http_response_code(400);
    } else {
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500);
    }
}
