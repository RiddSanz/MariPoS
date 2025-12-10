<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    // API-friendly auth check: avoid calling undefined helper that may cause a fatal error
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    $query = "
    SELECT 
        c.id,
        c.name,
        c.prefix,
        COUNT(DISTINCT p.id) as item_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id, c.name, c.prefix
    ORDER BY c.name ASC
";
$stmt = $conn->prepare($query);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categories);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Throwable $t) {
    // Catch other PHP Errors/Exceptions so we always return JSON (prevents HTML error output)
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $t->getMessage()]);
}
