<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    // API-friendly auth check
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
    
    // Only admins can view user list
    if (!is_admin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT
            id,
            username,
            role,
            CASE WHEN role = 'admin' THEN 1 ELSE 0 END as is_admin
        FROM users
        ORDER BY username
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($users);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $t->getMessage()]);
}