<?php
header('Content-Type: application/json');
require_once '../config/database.php';

check_login_api();

$stats = [];

// 1. Total Penjualan Hari Ini
$stmt = $conn->prepare("SELECT SUM(total_amount) as total_sales FROM transactions WHERE DATE(created_at) = CURDATE()");
$stmt->execute();
$sales_today = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];
$stats['sales_today'] = $sales_today ? (float)$sales_today : 0;

// 2. Jumlah Transaksi Hari Ini
$stmt = $conn->prepare("SELECT COUNT(id) as transaction_count FROM transactions WHERE DATE(created_at) = CURDATE()");
$stmt->execute();
$transactions_today = $stmt->fetch(PDO::FETCH_ASSOC)['transaction_count'];
$stats['transactions_today'] = (int)$transactions_today;

// 3. Barang Akan Habis (stok < 10)
$low_stock_threshold = 10;
$stmt = $conn->prepare("SELECT COUNT(id) as low_stock_count FROM products WHERE stock < :threshold");
$stmt->bindParam(':threshold', $low_stock_threshold, PDO::PARAM_INT);
$stmt->execute();
$low_stock_count = $stmt->fetch(PDO::FETCH_ASSOC)['low_stock_count'];
$stats['low_stock_count'] = (int)$low_stock_count;

echo json_encode($stats);
?>