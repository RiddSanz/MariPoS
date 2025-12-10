<?php
require_once '../config/database.php';
check_login();
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id']) || !isset($data['name']) || !isset($data['type']) || !isset($data['value']) || !isset($data['applicable_to'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$id = (int)$data['id'];
$name = trim($data['name']);
$type = $data['type'];
$value = (float)$data['value'];
$applicable_to = $data['applicable_to'];

if (empty($name) || !in_array($type, ['percentage', 'fixed']) || $value < 0 || !in_array($applicable_to, ['all', 'categories', 'products'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE discounts SET name = ?, type = ?, value = ?, applicable_to = ? WHERE id = ?");
    $stmt->execute([$name, $type, $value, $applicable_to, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
