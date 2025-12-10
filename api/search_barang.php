<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$term = isset($_GET['term']) ? '%' .$_GET['term'] . '%' : '%';

$query = "SELECT p.id, p.sku, p.name, p.price, p.stock, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE (p.name LIKE :term OR p.sku LIKE :term) AND p.stock > 0 ORDER BY p.name ASC LIMIT 20";

$stmt = $conn->prepare($query);
$stmt->bindParam(':term', $term);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>