<?php
require_once '../config/database.php';
check_login();
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id']) || !isset($data['vehicle_type']) || !isset($data['fuel_price_per_liter']) || !isset($data['fuel_consumption_per_km']) || !isset($data['driver_wage']) || !isset($data['transport_fee'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$id = (int)$data['id'];
$vehicleType = trim($data['vehicle_type']);
$fuelPrice = (float)$data['fuel_price_per_liter'];
$fuelConsumption = (float)$data['fuel_consumption_per_km'];
$driverWage = (float)$data['driver_wage'];
$transportFee = (float)$data['transport_fee'];
$maxVolume = isset($data['max_volume']) ? (float)$data['max_volume'] : null;
$maxWeight = isset($data['max_weight']) ? (float)$data['max_weight'] : null;

if (empty($vehicleType) || $fuelPrice < 0 || $fuelConsumption < 0 || $driverWage < 0 || $transportFee < 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE shipping_configs SET vehicle_type = ?, fuel_price_per_liter = ?, fuel_consumption_per_km = ?, driver_wage = ?, transport_fee = ?, max_volume = ?, max_weight = ? WHERE id = ?");
    $stmt->execute([$vehicleType, $fuelPrice, $fuelConsumption, $driverWage, $transportFee, $maxVolume, $maxWeight, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
