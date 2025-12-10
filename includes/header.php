<?php
$page = 'dashboard'; 
if (isset($_GET['page'])) {
    $page = basename($_GET['page']);
}
$body_id = 'page-' . htmlspecialchars($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(get_setting('app_name', 'Parid Store')); ?></title>
    <!-- Theme handling -->
    <script>
        (() => {
            const theme = localStorage.getItem('theme') || 
                         (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body id="<?php echo $body_id; ?>">
    <!-- Top navbar for mobile with sidebar toggle -->
    <nav class="d-md-none bg-body-tertiary border-bottom">
        <div class="container-fluid d-flex align-items-center p-2">
            <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand ms-2 link-body-emphasis" href="index.php"><?= htmlspecialchars(get_setting('app_name', 'Parid Store')); ?></a>
        </div>
    </nav>