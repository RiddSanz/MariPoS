<?php
require_once '../config/database.php';
check_login();
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id']) || !isset($data['username']) || !isset($data['role'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$id = (int)$data['id'];
$username = trim($data['username']);
$role = $data['role'];
$password = isset($data['password']) ? $data['password'] : null;

if (empty($username) || !in_array($role, ['admin', 'kasir'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $password, $role, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $role, $id]);
    }
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
