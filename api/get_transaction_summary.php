<?php
header('Content-Type: application/json');
require_once '../config/database.php';
check_login_api();

$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

try {
    // Get total income and transaction count
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_transactions,
            COALESCE(SUM(total_amount), 0) as total_income,
            COALESCE(AVG(total_amount), 0) as avg_transaction
        FROM transactions 
        WHERE DATE(created_at) BETWEEN :start AND :end
    ");
    $stmt->execute(['start' => $start_date, 'end' => $end_date]);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Initialize with zeros if null
    $summary['total_transactions'] = (int)$summary['total_transactions'];
    $summary['total_income'] = (float)$summary['total_income'];
    $summary['avg_transaction'] = (float)$summary['avg_transaction'];

    // Get best selling item
    $stmt = $conn->prepare("
        SELECT 
            p.name as product_name,
            COUNT(*) as sold_count,
            SUM(td.quantity) as total_quantity
        FROM transaction_details td
        JOIN products p ON p.id = td.product_id
        JOIN transactions t ON t.id = td.transaction_id
        WHERE DATE(t.created_at) BETWEEN :start AND :end
        GROUP BY p.id, p.name
        ORDER BY total_quantity DESC
        LIMIT 1
    ");
    $stmt->execute(['start' => $start_date, 'end' => $end_date]);
    $bestSeller = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get sales by category
    $stmt = $conn->prepare("
        SELECT 
            c.name as category,
            COUNT(DISTINCT t.id) as transaction_count,
            SUM(td.quantity) as items_sold,
            SUM(td.price * td.quantity) as revenue
        FROM transaction_details td
        JOIN products p ON p.id = td.product_id
        JOIN categories c ON c.id = p.category_id
        JOIN transactions t ON t.id = td.transaction_id
        WHERE DATE(t.created_at) BETWEEN :start AND :end
        GROUP BY c.id, c.name
    ");
    $stmt->execute(['start' => $start_date, 'end' => $end_date]);
    $categorySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'summary' => $summary,
        'bestSeller' => $bestSeller,
        'categorySales' => $categorySales,
        'period' => [
            'start' => $start_date,
            'end' => $end_date
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}