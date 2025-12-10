<?php
$host = 'localhost';
$db_name = 'maripos'; 
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=" . $host, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $exception) {
    die("Connection error: " . $exception->getMessage());
}

require_once 'setup.php';

// --- Fungsi Helper ---
function check_login() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function check_login_api() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit();
    }
}

function is_admin() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['role']) && strtolower(trim($_SESSION['role'])) === 'admin';
}

/**
 * Settings helpers
 */
function get_setting($key, $default = null) {
    global $conn;
    $stmt = $conn->prepare("SELECT v FROM settings WHERE k = :k LIMIT 1");
    $stmt->execute(['k' => $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) return $row['v'];
    return $default;
}

function update_setting($key, $value) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO settings (k,v) VALUES (:k,:v) ON DUPLICATE KEY UPDATE v = :v2");
    return $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
}

function get_all_settings() {
    global $conn;
    $stmt = $conn->query("SELECT k,v FROM settings");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $res = [];
    foreach ($rows as $r) $res[$r['k']] = $r['v'];
    return $res;
}
?>