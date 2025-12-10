<?php
require_once '../config/database.php';
check_login();

try {
    $stmt = $conn->query("SELECT id, name, contact, address FROM suppliers ORDER BY name");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($suppliers);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
