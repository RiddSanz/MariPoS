<?php
require_once '../config/database.php';
check_login();

try {
    $stmt = $conn->query("SELECT id, name, type, value, applicable_to FROM discounts ORDER BY name");
    $discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($discounts);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
