<!-- Static sidebar for md+ screens / desktop -->
<div class="d-none d-md-flex flex-column flex-shrink-0 p-3 bg-body-tertiary border-end" style="width: 280px; height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1000;">
    <a href="/paridstore" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none link-body-emphasis">
        <i class="bi bi-cash-stack me-2"></i>
        <span class="fs-4"><?= htmlspecialchars(get_setting('app_name', 'Parid Store')); ?></span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php?page=dashboard" class="nav-link link-body-emphasis" aria-current="page">
                <i class="bi bi-house-door me-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="index.php?page=kasir" class="nav-link link-body-emphasis">
                <i class="bi bi-cart me-2"></i>
                Kasir
            </a>
        </li>
        <li>
            <a href="index.php?page=history" class="nav-link link-body-emphasis">
                <i class="bi bi-clock-history me-2"></i>
                Riwayat Transaksi
            </a>
        </li>
        <?php if (is_admin()): ?>
            <li>
                <a href="index.php?page=barang" class="nav-link link-body-emphasis">
                    <i class="bi bi-box-seam me-2"></i>
                    Manajemen Barang
                </a>
            </li>
            <li>
                <a href="index.php?page=admin" class="nav-link link-body-emphasis">
                    <i class="bi bi-gear-fill me-2"></i>
                    Admin Panel
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <!-- <img src="https://avatars.githubusercontent.com/u/82357480?v=4" alt="" width="32" height="32" class="rounded-circle me-2"> -->
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username']); ?>&background=random&size=32" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong><?= ucwords($_SESSION['username']); ?></strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
        </ul>
    </div>
    <hr>
    <div class="dropdown">
        <button class="btn btn-link nav-link link-body-emphasis dropdown-toggle d-inline-flex align-items-center" type="button" id="bd-theme" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-sun-fill theme-icon-active"></i>
            <span class="ms-2">Theme</span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="bd-theme">
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                    <i class="bi bi-sun-fill me-2 opacity-75"></i> Light
                    <i class="bi bi-check2 ms-auto d-none"></i>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                    <i class="bi bi-moon-stars-fill me-2 opacity-75"></i> Dark
                    <i class="bi bi-check2 ms-auto d-none"></i>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto">
                    <i class="bi bi-circle-half me-2 opacity-75"></i> Auto
                    <i class="bi bi-check2 ms-auto d-none"></i>
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Offcanvas sidebar for small screens / mobile -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sidebarOffcanvasLabel"><?= htmlspecialchars(get_setting('app_name', 'Parid Store')); ?></h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px;">
      <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <i class="bi bi-cash-stack me-2"></i>
        <span class="fs-4"><?= htmlspecialchars(get_setting('app_name', 'Parid Store')); ?></span>
      </a>
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php?page=dashboard" class="nav-link link-body-emphasis" aria-current="page">
                <i class="bi bi-house-door me-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="index.php?page=kasir" class="nav-link link-body-emphasis">
                <i class="bi bi-cart me-2"></i>
                Kasir
            </a>
        </li>
        <li>
            <a href="index.php?page=history" class="nav-link link-body-emphasis">
                <i class="bi bi-clock-history me-2"></i>
                Riwayat Transaksi
            </a>
        </li>
        <?php if (is_admin()): ?>
            <li>
                <a href="index.php?page=barang" class="nav-link link-body-emphasis">
                    <i class="bi bi-box-seam me-2"></i>
                    Manajemen Barang
                </a>
            </li>
            <li>
                <a href="index.php?page=admin" class="nav-link link-body-emphasis">
                    <i class="bi bi-gear-fill me-2"></i>
                    Admin Panel
                </a>
            </li>
        <?php endif; ?>
      </ul>
      <hr>
      <div class="dropdown px-3">
        <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" id="dropdownUser1_mobile" data-bs-toggle="dropdown" aria-expanded="false">
            <!-- <img src="https://avatars.githubusercontent.com/u/82357480?v=4" alt="" width="32" height="32" class="rounded-circle me-2"> -->
             <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username']); ?>&background=random&size=32" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1_mobile">
            <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
        </ul>
      </div>
      <hr>
      <div class="dropdown px-3">
        <button class="btn btn-link nav-link link-body-emphasis dropdown-toggle d-inline-flex align-items-center bd-theme" type="button" id="bd-theme-mobile" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi theme-icon-active"></i>
            <span class="ms-2 bd-theme-text">Theme</span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="bd-theme-mobile">
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                    <i class="bi bi-sun-fill me-2 opacity-75"></i> Light
                    <i class="bi bi-check2 ms-auto d-none"></i>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                    <i class="bi bi-moon-stars-fill me-2 opacity-75"></i> Dark
                    <i class="bi bi-check2 ms-auto d-none"></i>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto">
                    <i class="bi bi-circle-half me-2 opacity-75"></i> Auto
                    <i class="bi bi-check2 ms-auto d-none"></i>
                </button>
            </li>
        </ul>
      </div>
    </div>
  </div>
</div>
