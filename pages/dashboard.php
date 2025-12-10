<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>
<p id="welcome-message" data-text="Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?>! Berikut adalah ringkasan aktivitas hari ini."></p>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Penjualan Hari Ini</div>
            <div class="card-body">
                <h5 class="card-title" id="stats-sales-today">Memuat...</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Transaksi Hari Ini</div>
            <div class="card-body">
                <h5 class="card-title" id="stats-transactions-today">Memuat...</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">Barang Akan Habis</div>
            <div class="card-body">
                <h5 class="card-title" id="stats-low-stock">Memuat...</h5>
            </div>
        </div>
    </div>
</div>
