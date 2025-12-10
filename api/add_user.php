<?php
require_once '../config/database.php';
check_login();
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['username']) || !isset($data['password']) || !isset($data['role'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$username = trim($data['username']);
$password = $data['password'];
$role = $data['role'];

if (empty($username) || empty($password) || !in_array($role, ['admin', 'kasir'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
    echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
