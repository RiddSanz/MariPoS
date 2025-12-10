<?php
require_once '../config/database.php';
check_login();
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id']) || !isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$id = (int)$data['id'];
$name = trim($data['name']);
$contact = isset($data['contact']) ? trim($data['contact']) : null;
$address = isset($data['address']) ? trim($data['address']) : null;

if (empty($name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Name is required']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE suppliers SET name = ?, contact = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $contact, $address, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
