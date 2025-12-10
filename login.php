<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $message = 'Username dan password tidak boleh kosong.';
    } else {
        $query = "SELECT id, username, password, role FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':username', $_POST['username']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($_POST['password'] == $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                header("Location: index.php");
                exit();
            } else {
                $message = 'Password salah.';
            }
        } else {
            $message = 'Username tidak ditemukan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(get_setting('app_name', 'Parid Store')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex p-0">
        <div class="row g-0 w-100">
            <!-- Left Side: Image/Branding -->
            <div class="col-lg-7 d-none d-lg-flex align-items-center justify-content-center bg-login-image position-relative">
                <div class="overlay"></div>
                <div class="z-1 text-white text-center p-5">
                    <h1 class="display-3 fw-bold mb-3">MariPoS</h1>
                    <p class="lead fs-4">Manage your store efficiently and effortlessly.</p>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="col-lg-5 d-flex align-items-center justify-content-center bg-body-tertiary">
                <div class="login-form-container w-100 p-5">
                    <div class="text-center mb-5">
                        <div class="mb-3">
                            <i class="bi bi-shop fs-1 text-primary"></i>
                        </div>
                        <h2 class="fw-bold">Welcome Back</h2>
                        <p id="typewriter" class="text-muted" style="min-height: 24px;"></p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div><?php echo $message; ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="post">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                            <label for="username"><i class="bi bi-person me-2"></i>Username</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <!-- <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                <label class="form-check-label text-secondary" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            <a href="#" class="text-decoration-none small">Forgot password?</a> -->
                        </div>

                        <button class="btn btn-primary w-100 py-3 fw-semibold fs-5 rounded-3 shadow-sm" type="submit">
                            Sign In <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <!-- <div class="mt-5 text-center text-muted small">
                        &copy; <?= date('Y'); ?> Parid Store. All rights reserved.
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/theme.js"></script>
    <script>
        const typewriter = document.getElementById('typewriter');
        const text = 'Welcome back, Admin. Please identify yourself !';
        let i = 0;
        function type() {
            if (i < text.length) {
                typewriter.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, 50);
            }
        }
        // Start typing after a short delay to ensure DOM is ready and transition is smooth
        setTimeout(type, 500);
    </script>
</body>
</html>
