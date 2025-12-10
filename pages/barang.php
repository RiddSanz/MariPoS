<?php
if (!is_admin()) {
    echo "<p>This page is restricted to Admin only !</p>";
    return;
}
?>
<style>
    /* Disable number input spinners */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    /* Table column alignment */
    .table th.text-end, 
    .table td.text-end {
        text-align: right !important;
    }
    
    /* Actions column */
    .table th.actions, 
    .table td.actions {
        text-align: center;
        white-space: nowrap;
        width: 1%;
    }
    
    /* Action buttons */
    .actions .btn-group-sm {
        display: inline-flex;
        gap: 0.25rem;
    }

</style>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Barang</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button id="add-product-btn" class="btn btn-sm btn-outline-secondary">+ Tambah Barang</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm" id="products-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th class="actions text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data produk akan dimuat di sini oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Modal untuk Tambah/Edit Barang -->
    <div class="modal fade" id="product-modal" tabindex="-1" aria-labelledby="product-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="product-form">
                        <input type="hidden" id="product-id" name="id">
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU (Kode Barang)</label>
                            <input type="text" class="form-control" id="sku" name="sku" required autocomplete="off" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category_id" autocomplete="off">
                                <!-- Opsi kategori akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="price" name="price" min="0" required autocomplete="off">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" required autocomplete="off">
                        </div>
                        <button type="submit" id="save-product-btn" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>