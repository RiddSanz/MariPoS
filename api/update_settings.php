<?php
header('Content-Type: application/json');
require_once '../config/database.php';
if (!is_admin()) {
    echo json_encode(['error' => 'Akses ditolak']);
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    http_response_code(405);
    exit();
}

try {
    // Handle logo upload if present
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo = $_FILES['logo'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($logo['type'], $allowed)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF allowed.');
        }

        $upload_path = __DIR__ . '/../assets/img/';
        $filename = 'logo_' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $logo['name']);
        $filepath = $upload_path . $filename;

        if (move_uploaded_file($logo['tmp_name'], $filepath)) {
            $_POST['logo_path'] = 'assets/img/' . $filename;
        } else {
            throw new Exception('Failed to save logo file');
        }
    }

    // Update settings
    foreach (['app_name', 'address', 'phone', 'logo_path'] as $key) {
        if (isset($_POST[$key])) {
            update_setting($key, $_POST[$key]);
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}