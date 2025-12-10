<?php
header('Content-Type: application/json');
require_once '../config/database.php';

check_login_api();
if (!is_admin()) {
    echo json_encode(['error' => 'Akses ditolak']);
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Metode tidak diizinkan']);
    http_response_code(405);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? 0;

if ($id <= 0) {
    echo json_encode(['error' => 'ID tidak valid']);
    http_response_code(400);
    exit();
}

$query = "DELETE FROM products WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => 'Barang berhasil dihapus.']);
    } else {
        echo json_encode(['error' => 'Barang tidak ditemukan.']);
        http_response_code(404);
    }
} else {
    echo json_encode(['error' => 'Gagal menghapus barang.']);
    http_response_code(500);
}
?>