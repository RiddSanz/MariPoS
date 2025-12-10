<?php
header('Content-Type: application/json');
require_once '../config/database.php';
check_login_api();

// Get date range parameters
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

try {
    $query = "
        SELECT 
            t.id,
            t.created_at,
            t.total as total_amount,
            u.username,
            COUNT(td.id) as items_count
        FROM transactions t
        JOIN users u ON u.id = t.user_id
        JOIN transaction_details td ON td.transaction_id = t.id
        WHERE DATE(t.created_at) BETWEEN :start_date AND :end_date
        GROUP BY t.id, t.created_at, t.total, u.username
        ORDER BY t.created_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>