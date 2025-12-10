<?php
require_once '../config/database.php';
check_login();

try {
    $stmt = $conn->query("SELECT id, vehicle_type, fuel_price_per_liter, fuel_consumption_per_km, driver_wage, transport_fee, max_volume, max_weight FROM shipping_configs ORDER BY vehicle_type");
    $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($configs);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
