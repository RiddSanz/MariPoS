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
if (!isset($input['name']) || trim($input['name']) === '' || !isset($input['prefix'])) {
    echo json_encode(['error' => 'Name and prefix required']);
    http_response_code(400);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO categories (name, prefix) VALUES (:name, :prefix)");
    $stmt->execute(['name' => trim($input['name']), 'prefix' => trim($input['prefix'])]);
    echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Duplicate
        echo json_encode(['error' => 'Category already exists']);
        http_response_code(400);
    } else {
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500);
    }
}
