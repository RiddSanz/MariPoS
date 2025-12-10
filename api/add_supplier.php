<?php
require_once '../config/database.php';
check_login();
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$name = trim($data['name']);
$contact = isset($data['contact']) ? trim($data['contact']) : null;
$address = isset($data['address']) ? trim($data['address']) : null;

if (empty($name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Name is required']);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO suppliers (name, contact, address) VALUES (?, ?, ?)");
    $stmt->execute([$name, $contact, $address]);
    echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
