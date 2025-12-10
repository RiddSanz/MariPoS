<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Halaman Kasir</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button id="hold-transaction-btn" class="btn btn-warning me-2" disabled>Hold Transaction</button>
        <button id="resume-transaction-btn" class="btn btn-info">Resume Held</button>
    </div>
</div>

<div class="row h-100">
    <div class="col-md-7 d-flex flex-column h-100">
        <div class="card flex-grow-1 d-flex flex-column">
            <div class="card-header">
                <input type="text" id="product-search" class="form-control" placeholder="Cari barang...">
            </div>
            <div class="card-body p-0" style="overflow-y: auto;">
                <div id="product-list" class="row g-2 p-3">
                    <!-- Product cards will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5 d-flex flex-column h-100">
        <div class="card flex-grow-1 d-flex flex-column">
            <div class="card-header">
                Keranjang
            </div>
            <div class="card-body p-0 d-flex flex-column" style="overflow-y: auto;">
                <div id="cart-items" class="flex-grow-1 p-3">
                    <p class="text-center text-muted">Keranjang masih kosong.</p>
                </div>
            </div>
            <div class="card-footer bg-body-tertiary">

            <div class="mb-2">
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1">
                             <label class="form-label small mb-1">Shipping</label>
                             <select id="shipping-select" class="form-select form-select-sm">
                                <option value="">No Shipping</option>
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label small mb-1">Discount</label>
                            <select id="discount-select" class="form-select form-select-sm">
                                <option value="">No Discount</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Subtotal -->
                <div class="d-flex justify-content-between align-items-center mb-1 small">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">Rp 0</span>
                </div>
                <!-- Shipping Cost -->
                <div class="d-flex justify-content-between align-items-center mb-1 small">
                    <span>Shipping:</span>
                    <span id="cart-shipping">Rp 0</span>
                </div>
                <!-- Discount Amount -->
                <div class="d-flex justify-content-between align-items-center mb-1 small">
                    <span>Discount:</span>
                    <span id="cart-discount">Rp 0</span>
                </div>
                <!-- Total -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Total:</h5>
                    <h5 class="mb-0" id="cart-total">Rp 0</h5>
                </div>
                <!-- Payment Method -->
                <div class="mb-2">
                    <label class="form-label small mb-1">Payment Method</label>
                    <select id="payment-method" class="form-select form-select-sm">
                        <option value="cash">Tunai</option>
                        <option value="card">Kartu</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <!-- QRIS Modal Button -->
                <div id="qris-section" class="text-center mb-2" style="display: none;">
                    <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#qrisModal">
                        <i class="bi bi-qr-code-scan"></i> Tampilkan QRIS
                    </button>
                </div>
                <!-- Cash Received (for cash payment) -->
                <div id="cash-section" class="mb-2">
                    <label class="form-label small mb-1">Cash Received</label>
                    <input type="number" id="cash-received" class="form-control form-control-sm" placeholder="0" min="0">
                </div>
                <!-- Change / Kembalian (for cash payment) -->
                <div id="change-section" class="d-flex justify-content-between align-items-center mb-2" style="display: none;">
                    <span>Change:</span>
                    <span id="change-amount">Rp 0</span>
                </div>
                <button id="checkout-btn" class="btn btn-primary w-100" disabled>Checkout</button>
            </div>
        </div>
    </div>
</div>

<!-- Resume Held Transaction Modal -->
<div class="modal fade" id="resumeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resume Held Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="held-transactions-list">
                    <p class="text-center text-muted">Loading held transactions...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Void Transaction Modal -->
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Void Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to void this transaction? This action cannot be undone.</p>
                <div id="void-transaction-details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-void-btn">Void Transaction</button>
            </div>
        </div>
    </div>
</div>

<!-- QRIS Modal -->
<div class="modal fade" id="qrisModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QRIS Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="assets/img/qris.png" alt="QRIS" class="img-fluid" style="max-width: 400px;">
                <p class="mt-3">Scan QR code untuk pembayaran</p>
                <div class="alert alert-info">
                    <strong>Total Pembayaran: <span id="qris-total">Rp 0</span></strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="confirm-qris-payment">Konfirmasi Pembayaran</button>
            </div>
        </div>
    </div>
</div>
