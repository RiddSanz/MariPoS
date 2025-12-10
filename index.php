<?php
require_once 'config/database.php';
check_login();

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$page_path = "pages/{$page}.php";

if (!file_exists($page_path)) {
    $page_path = "pages/dashboard.php"; // Fallback to dashboard
}

include 'includes/header.php';
?>
<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>
    <main class="flex-grow-1 p-3">
        <?php include $page_path; ?>
    </main>
</div>
<?php include 'includes/footer.php'; ?>
