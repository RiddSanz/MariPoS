<?php
require_once __DIR__ . '/../config/database.php';
check_login();
if (!is_admin()) {
    echo "<p>Access Denied. Admin only !</p>";
    exit();
}
$settings = get_all_settings();
?>
<style>
    .table th.actions, .table td:last-child {
        text-align: right;
        white-space: nowrap;
        width: 1%;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
</style>

<div class="container-fluid"> <!-- Changed to container-fluid for better width usage -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Admin Panel</h1>
    </div>

    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">App Settings</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button">Categories</button>
        </li>
        <!-- <li class="nav-item" role="presentation">
            <button class="nav-link" id="suppliers-tab" data-bs-toggle="tab" data-bs-target="#suppliers" type="button">Suppliers</button>
        </li> -->
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="discounts-tab" data-bs-toggle="tab" data-bs-target="#discounts" type="button">Discounts</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button">Shipping</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">Users</button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="settings">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="settingsForm">
                        <div class="mb-3">
                            <label class="form-label">App Name</label>
                            <input name="app_name" class="form-control" autocomplete="off" value="<?= htmlspecialchars($settings['app_name'] ?? 'Parid Store'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" autocomplete="off"><?= htmlspecialchars($settings['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input name="phone" class="form-control" autocomplete="off" value="<?= htmlspecialchars($settings['phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo (file)</label>
                            <input type="file" name="logo" class="form-control" accept="image/*" autocomplete="off">
                            <?php if (!empty($settings['logo_path']) && file_exists($settings['logo_path'])): ?>
                                <img src="/<?= htmlspecialchars($settings['logo_path']); ?>" alt="logo" style="max-height:80px; margin-top:8px;">
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-primary" type="submit">Save settings</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="categories">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Categories</h3>
                        <button class="btn btn-success" onclick="openAddCategory()">
                            <i class="bi bi-plus-lg"></i> Add Category
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoriesList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Modal -->
        <div class="modal fade" id="categoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="categoryForm">
                            <input type="hidden" id="categoryId">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="categoryName" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SKU Prefix</label>
                                <input type="text" class="form-control" id="categoryPrefix" autocomplete="off">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="suppliers">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Suppliers</h3>
                        <button class="btn btn-success" onclick="openAddSupplier()">
                            <i class="bi bi-plus-lg"></i> Add Supplier
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th class="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="suppliersList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Modal -->
        <div class="modal fade" id="supplierModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="supplierModalLabel">Add Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="supplierForm">
                            <input type="hidden" id="supplierId">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="supplierName" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact</label>
                                <input type="text" class="form-control" id="supplierContact" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" id="supplierAddress" autocomplete="off"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveSupplierBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="discounts">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Discounts</h3>
                        <button class="btn btn-success" onclick="openAddDiscount()">
                            <i class="bi bi-plus-lg"></i> Add Discount
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Applicable To</th>
                                    <th class="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="discountsList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discount Modal -->
        <div class="modal fade" id="discountModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="discountModalLabel">Add Discount</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="discountForm">
                            <input type="hidden" id="discountId">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="discountName" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" id="discountType" required>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Value</label>
                                <input type="number" class="form-control" id="discountValue" min="0" step="0.01" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Applicable To</label>
                                <select class="form-select" id="discountApplicable" required>
                                    <option value="all">All Products</option>
                                    <option value="categories">Specific Categories</option>
                                    <option value="products">Specific Products</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveDiscountBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="shipping">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Shipping Configurations</h3>
                        <button class="btn btn-success" onclick="openAddShipping()">
                            <i class="bi bi-plus-lg"></i> Add Config
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Vehicle Type</th>
                                    <th>Fuel Price/Liter</th>
                                    <th>Fuel Consumption/Km</th>
                                    <th>Driver Wage</th>
                                    <th>Transport Fee</th>
                                    <th>Max Volume</th>
                                    <th>Max Weight</th>
                                    <th class="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="shippingList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Modal -->
        <div class="modal fade" id="shippingModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shippingModalLabel">Add Shipping Config</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="shippingForm">
                            <input type="hidden" id="shippingId">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vehicle Type</label>
                                    <input type="text" class="form-control" id="vehicleType" required autocomplete="off">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fuel Price per Liter (Rp)</label>
                                    <input type="number" class="form-control" id="fuelPrice" min="0" step="0.01" required autocomplete="off">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fuel Consumption per Km (L/Km)</label>
                                    <input type="number" class="form-control" id="fuelConsumption" min="0" step="0.01" required autocomplete="off">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Driver Wage (Rp)</label>
                                    <input type="number" class="form-control" id="driverWage" min="0" step="0.01" required autocomplete="off">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Transport Fee (Rp)</label>
                                    <input type="number" class="form-control" id="transportFee" min="0" step="0.01" required autocomplete="off">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Max Volume (m³)</label>
                                    <input type="number" class="form-control" id="maxVolume" min="0" step="0.01" autocomplete="off">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Max Weight (kg)</label>
                                    <input type="number" class="form-control" id="maxWeight" min="0" step="0.01" autocomplete="off">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveShippingBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="users">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Users</h3>
                        <button class="btn btn-success" onclick="openAddUser()">
                            <i class="bi bi-plus-lg"></i> Add User
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Modal -->
        <div class="modal fade" id="userModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm">
                            <input type="hidden" id="userId">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" id="userUsername" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" id="userPassword" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" id="userRole" required>
                                    <option value="kasir">Kasir</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveUserBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Settings form submit
        const settingsForm = document.getElementById('settingsForm');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var form = new FormData(this);
                fetch('api/update_settings.php', {
                    method: 'POST',
                    body: form
                }).then(r=>r.json()).then(j=>{
                    if (j.success) alert('Settings updated'); else alert('Error: '+(j.error||'unknown'));
                });
            });
        }

        // Category Management
        const categoryModalEl = document.getElementById('categoryModal');
        const categoryModal = categoryModalEl ? new bootstrap.Modal(categoryModalEl) : null;
        let editingCategoryId = null;

        function loadCategories() {
            if (!document.getElementById('categoriesList')) return;
            fetch('api/get_categories.php').then(r=>r.json()).then(data=>{
                if (data.error) {
                    document.getElementById('categoriesList').innerHTML = `<tr><td colspan="2" class="text-center text-danger">${data.error}</td></tr>`;
                    return;
                }
                let html = '';
                data.forEach(function(c){
                    html += `<tr>
                        <td>${c.name}</td>
                        <td class="actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary" onclick="openEditCategory(${c.id}, '${c.name.replace(/'/g, "\'")}', '${c.prefix ? c.prefix.replace(/'/g, "\\'") : ''}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteCategory(${c.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                document.getElementById('categoriesList').innerHTML = html || '<tr><td colspan="2" class="text-center">No categories found</td></tr>';
            }).catch(error => {
                console.error('Error loading categories:', error);
                document.getElementById('categoriesList').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Failed to load categories</td></tr>';
            });
        }

        window.openEditCategory = function(id, name, prefix) {
            editingCategoryId = id;
            document.getElementById('categoryModalLabel').textContent = 'Edit Category';
            document.getElementById('categoryName').value = name;
            document.getElementById('categoryPrefix').value = prefix;
            categoryModal.show();
        }

        window.openAddCategory = function() {
            editingCategoryId = null;
            document.getElementById('categoryModalLabel').textContent = 'Add Category';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryPrefix').value = '';
            categoryModal.show();
        }

        const saveCategoryBtn = document.getElementById('saveCategoryBtn');
        if (saveCategoryBtn) {
            saveCategoryBtn.addEventListener('click', function() {
                const name = document.getElementById('categoryName').value.trim();
                const prefix = document.getElementById('categoryPrefix').value.trim();
                if (!name) {
                    alert('Name is required');
                    return;
                }

                const endpoint = editingCategoryId ? 'api/edit_category.php' : 'api/add_category.php';
                const data = editingCategoryId ? {id: editingCategoryId, name, prefix} : {name, prefix};

                fetch(endpoint, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                }).then(r => r.json()).then(response => {
                    if (response.success) {
                        categoryModal.hide();
                        loadCategories();
                    } else {
                        alert('Error: ' + (response.error || 'Unknown error'));
                    }
                }).catch(error => {
                    console.error('Error saving category:', error);
                    alert('Failed to save category');
                });
            });
        }

        window.deleteCategory = function(id) {
            if (!confirm('Are you sure you want to delete this category? This will affect all products in this category.')) return;
            
            fetch('api/delete_category.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            }).then(r => r.json()).then(response => {
                if (response.success) {
                    loadCategories();
                } else {
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            }).catch(error => {
                console.error('Error deleting category:', error);
                alert('Failed to delete category');
            });
        }

        // Supplier Management
        const supplierModalEl = document.getElementById('supplierModal');
        const supplierModal = supplierModalEl ? new bootstrap.Modal(supplierModalEl) : null;
        let editingSupplierId = null;

        function loadSuppliers() {
            if (!document.getElementById('suppliersList')) return;
            fetch('api/get_suppliers.php').then(r=>r.json()).then(data=>{
                if (data.error) {
                    document.getElementById('suppliersList').innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.error}</td></tr>`;
                    return;
                }
                let html = '';
                data.forEach(function(s){
                    html += `<tr>
                        <td>${s.name}</td>
                        <td>${s.contact || '-'}</td>
                        <td>${s.address || '-'}</td>
                        <td class="actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary" onclick="openEditSupplier(${s.id}, '${s.name.replace(/'/g, "\'")}', '${(s.contact || '').replace(/'/g, "\\'")}', '${(s.address || '').replace(/'/g, "\\'")}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteSupplier(${s.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                document.getElementById('suppliersList').innerHTML = html || '<tr><td colspan="4" class="text-center">No suppliers found</td></tr>';
            }).catch(error => {
                console.error('Error loading suppliers:', error);
                document.getElementById('suppliersList').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load suppliers</td></tr>';
            });
        }

        window.openEditSupplier = function(id, name, contact, address) {
            editingSupplierId = id;
            document.getElementById('supplierModalLabel').textContent = 'Edit Supplier';
            document.getElementById('supplierName').value = name;
            document.getElementById('supplierContact').value = contact;
            document.getElementById('supplierAddress').value = address;
            supplierModal.show();
        }

        window.openAddSupplier = function() {
            editingSupplierId = null;
            document.getElementById('supplierModalLabel').textContent = 'Add Supplier';
            document.getElementById('supplierForm').reset();
            supplierModal.show();
        }

        const saveSupplierBtn = document.getElementById('saveSupplierBtn');
        if (saveSupplierBtn) {
            saveSupplierBtn.addEventListener('click', function() {
                const name = document.getElementById('supplierName').value.trim();
                const contact = document.getElementById('supplierContact').value.trim();
                const address = document.getElementById('supplierAddress').value.trim();
                if (!name) {
                    alert('Name is required');
                    return;
                }

                const endpoint = editingSupplierId ? 'api/edit_supplier.php' : 'api/add_supplier.php';
                const data = editingSupplierId ? {id: editingSupplierId, name, contact, address} : {name, contact, address};

                fetch(endpoint, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                }).then(r => r.json()).then(response => {
                    if (response.success) {
                        supplierModal.hide();
                        loadSuppliers();
                    } else {
                        alert('Error: ' + (response.error || 'Unknown error'));
                    }
                }).catch(error => {
                    console.error('Error saving supplier:', error);
                    alert('Failed to save supplier');
                });
            });
        }

        window.deleteSupplier = function(id) {
            if (!confirm('Are you sure you want to delete this supplier?')) return;

            fetch('api/delete_supplier.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            }).then(r => r.json()).then(response => {
                if (response.success) {
                    loadSuppliers();
                } else {
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            }).catch(error => {
                console.error('Error deleting supplier:', error);
                alert('Failed to delete supplier');
            });
        }

        // Discount Management
        const discountModalEl = document.getElementById('discountModal');
        const discountModal = discountModalEl ? new bootstrap.Modal(discountModalEl) : null;
        let editingDiscountId = null;

        function loadDiscounts() {
            if (!document.getElementById('discountsList')) return;
            fetch('api/get_discounts.php').then(r=>r.json()).then(data=>{
                if (data.error) {
                    document.getElementById('discountsList').innerHTML = `<tr><td colspan="5" class="text-center text-danger">${data.error}</td></tr>`;
                    return;
                }
                let html = '';
                data.forEach(function(d){
                    const valueDisplay = d.type === 'percentage' ? `${d.value}%` : `Rp ${parseFloat(d.value).toLocaleString('id-ID')}`;
                    const applicableDisplay = d.applicable_to === 'all' ? 'All Products' : d.applicable_to === 'categories' ? 'Categories' : 'Products';
                    html += `<tr>
                        <td>${d.name}</td>
                        <td>${d.type === 'percentage' ? 'Percentage' : 'Fixed'}</td>
                        <td>${valueDisplay}</td>
                        <td>${applicableDisplay}</td>
                        <td class="actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary" onclick="openEditDiscount(${d.id}, '${d.name.replace(/'/g, "\'")}', '${d.type}', '${d.value}', '${d.applicable_to}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteDiscount(${d.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                document.getElementById('discountsList').innerHTML = html || '<tr><td colspan="5" class="text-center">No discounts found</td></tr>';
            }).catch(error => {
                console.error('Error loading discounts:', error);
                document.getElementById('discountsList').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load discounts</td></tr>';
            });
        }

        window.openEditDiscount = function(id, name, type, value, applicable) {
            editingDiscountId = id;
            document.getElementById('discountModalLabel').textContent = 'Edit Discount';
            document.getElementById('discountName').value = name;
            document.getElementById('discountType').value = type;
            document.getElementById('discountValue').value = value;
            document.getElementById('discountApplicable').value = applicable;
            discountModal.show();
        }

        window.openAddDiscount = function() {
            editingDiscountId = null;
            document.getElementById('discountModalLabel').textContent = 'Add Discount';
            document.getElementById('discountForm').reset();
            discountModal.show();
        }

        const saveDiscountBtn = document.getElementById('saveDiscountBtn');
        if (saveDiscountBtn) {
            saveDiscountBtn.addEventListener('click', function() {
                const name = document.getElementById('discountName').value.trim();
                const type = document.getElementById('discountType').value;
                const value = parseFloat(document.getElementById('discountValue').value);
                const applicable = document.getElementById('discountApplicable').value;
                if (!name || isNaN(value) || value < 0 ) {
                    alert('Name and valid value are required');
                    return;
                }
                 else if (type === 'percentage' && value > 100) {
                    alert('Percentage value must be between 0 and 100');
                    return;
                } 

                const endpoint = editingDiscountId ? 'api/edit_discount.php' : 'api/add_discount.php';
                const data = editingDiscountId ? {id: editingDiscountId, name, type, value, applicable_to: applicable} : {name, type, value, applicable_to: applicable};

                fetch(endpoint, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                }).then(r => r.json()).then(response => {
                    if (response.success) {
                        discountModal.hide();
                        loadDiscounts();
                    } else {
                        alert('Error: ' + (response.error || 'Unknown error'));
                    }
                }).catch(error => {
                    console.error('Error saving discount:', error);
                    alert('Failed to save discount');
                });
            });
        }

        window.deleteDiscount = function(id) {
            if (!confirm('Are you sure you want to delete this discount?')) return;

            fetch('api/delete_discount.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            }).then(r => r.json()).then(response => {
                if (response.success) {
                    loadDiscounts();
                } else {
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            }).catch(error => {
                console.error('Error deleting discount:', error);
                alert('Failed to delete discount');
            });
        }

        // Shipping Management
        const shippingModalEl = document.getElementById('shippingModal');
        const shippingModal = shippingModalEl ? new bootstrap.Modal(shippingModalEl) : null;
        let editingShippingId = null;

        function loadShipping() {
            if (!document.getElementById('shippingList')) return;
            fetch('api/get_shipping.php').then(r=>r.json()).then(data=>{
                if (data.error) {
                    document.getElementById('shippingList').innerHTML = `<tr><td colspan="8" class="text-center text-danger">${data.error}</td></tr>`;
                    return;
                }
                let html = '';
                data.forEach(function(s){
                    html += `<tr>
                        <td>${s.vehicle_type}</td>
                        <td>Rp ${parseFloat(s.fuel_price_per_liter).toLocaleString('id-ID')}</td>
                        <td>${s.fuel_consumption_per_km} L/Km</td>
                        <td>Rp ${parseFloat(s.driver_wage).toLocaleString('id-ID')}</td>
                        <td>Rp ${parseFloat(s.transport_fee).toLocaleString('id-ID')}</td>
                        <td>${s.max_volume ? s.max_volume + ' m³' : '-'}</td>
                        <td>${s.max_weight ? s.max_weight + ' kg' : '-'}</td>
                        <td class="actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary" onclick="openEditShipping(${s.id}, '${s.vehicle_type.replace(/'/g, "\'")}', '${s.fuel_price_per_liter}', '${s.fuel_consumption_per_km}', '${s.driver_wage}', '${s.transport_fee}', '${s.max_volume || ''}', '${s.max_weight || ''}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteShipping(${s.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                document.getElementById('shippingList').innerHTML = html || '<tr><td colspan="8" class="text-center">No shipping configs found</td></tr>';
            }).catch(error => {
                console.error('Error loading shipping:', error);
                document.getElementById('shippingList').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Failed to load shipping configs</td></tr>';
            });
        }

        window.openEditShipping = function(id, vehicleType, fuelPrice, fuelConsumption, driverWage, transportFee, maxVolume, maxWeight) {
            editingShippingId = id;
            document.getElementById('shippingModalLabel').textContent = 'Edit Shipping Config';
            document.getElementById('vehicleType').value = vehicleType;
            document.getElementById('fuelPrice').value = fuelPrice;
            document.getElementById('fuelConsumption').value = fuelConsumption;
            document.getElementById('driverWage').value = driverWage;
            document.getElementById('transportFee').value = transportFee;
            document.getElementById('maxVolume').value = maxVolume;
            document.getElementById('maxWeight').value = maxWeight;
            shippingModal.show();
        }

        window.openAddShipping = function() {
            editingShippingId = null;
            document.getElementById('shippingModalLabel').textContent = 'Add Shipping Config';
            document.getElementById('shippingForm').reset();
            shippingModal.show();
        }

        const saveShippingBtn = document.getElementById('saveShippingBtn');
        if (saveShippingBtn) {
            saveShippingBtn.addEventListener('click', function() {
                const vehicleType = document.getElementById('vehicleType').value.trim();
                const fuelPrice = parseFloat(document.getElementById('fuelPrice').value);
                const fuelConsumption = parseFloat(document.getElementById('fuelConsumption').value);
                const driverWage = parseFloat(document.getElementById('driverWage').value);
                const transportFee = parseFloat(document.getElementById('transportFee').value);
                const maxVolume = document.getElementById('maxVolume').value ? parseFloat(document.getElementById('maxVolume').value) : null;
                const maxWeight = document.getElementById('maxWeight').value ? parseFloat(document.getElementById('maxWeight').value) : null;

                if (!vehicleType || isNaN(fuelPrice) || isNaN(fuelConsumption) || isNaN(driverWage) || isNaN(transportFee)) {
                    alert('Required fields must be filled with valid numbers');
                    return;
                }

                const endpoint = editingShippingId ? 'api/edit_shipping.php' : 'api/add_shipping.php';
                const data = editingShippingId ? {id: editingShippingId, vehicle_type: vehicleType, fuel_price_per_liter: fuelPrice, fuel_consumption_per_km: fuelConsumption, driver_wage: driverWage, transport_fee: transportFee, max_volume: maxVolume, max_weight: maxWeight} : {vehicle_type: vehicleType, fuel_price_per_liter: fuelPrice, fuel_consumption_per_km: fuelConsumption, driver_wage: driverWage, transport_fee: transportFee, max_volume: maxVolume, max_weight: maxWeight};

                fetch(endpoint, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                }).then(r => r.json()).then(response => {
                    if (response.success) {
                        shippingModal.hide();
                        loadShipping();
                    } else {
                        alert('Error: ' + (response.error || 'Unknown error'));
                    }
                }).catch(error => {
                    console.error('Error saving shipping:', error);
                    alert('Failed to save shipping config');
                });
            });
        }

        window.deleteShipping = function(id) {
            if (!confirm('Are you sure you want to delete this shipping config?')) return;

            fetch('api/delete_shipping.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            }).then(r => r.json()).then(response => {
                if (response.success) {
                    loadShipping();
                } else {
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            }).catch(error => {
                console.error('Error deleting shipping:', error);
                alert('Failed to delete shipping config');
            });
        }

        // User Management
        const userModalEl = document.getElementById('userModal');
        const userModal = userModalEl ? new bootstrap.Modal(userModalEl) : null;
        let editingUserId = null;

        function loadUsers() {
            if (!document.getElementById('usersList')) return;
            fetch('api/get_users.php')
                .then(r => r.json())
                .then(users => {
                    if (!Array.isArray(users)) {
                        throw new Error('Invalid response format');
                    }
                    let html = '';
                    users.forEach(user => {
                        html += `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.username}</td>
                                <td><span class="badge ${user.role === 'admin' ? 'bg-primary' : 'bg-secondary'}">${user.role === 'admin' ? 'Admin' : 'Kasir'}</span></td>
                                <td>${user.last_login || '-'}</td>
                                <td class="actions">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-primary" onclick="openEditUser(${user.id}, '${user.username.replace(/'/g, "\\'")}', '${user.role}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteUser(${user.id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    document.getElementById('usersList').innerHTML = html || '<tr><td colspan="5" class="text-center">No users found</td></tr>';
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    document.getElementById('usersList').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load users</td></tr>';
                });
        }

        window.openEditUser = function(id, username, role) {
            editingUserId = id;
            document.getElementById('userModalLabel').textContent = 'Edit User';
            document.getElementById('userUsername').value = username;
            document.getElementById('userPassword').value = ''; // Don't prefill password
            document.getElementById('userRole').value = role;
            userModal.show();
        }

        window.openAddUser = function() {
            editingUserId = null;
            document.getElementById('userModalLabel').textContent = 'Add User';
            document.getElementById('userForm').reset();
            userModal.show();
        }

        const saveUserBtn = document.getElementById('saveUserBtn');
        if (saveUserBtn) {
            saveUserBtn.addEventListener('click', function() {
                const username = document.getElementById('userUsername').value.trim();
                const password = document.getElementById('userPassword').value.trim();
                const role = document.getElementById('userRole').value;
                if (!username || (!editingUserId && !password)) {
                    alert('Username and password (for new user) are required');
                    return;
                }

                const endpoint = editingUserId ? 'api/edit_user.php' : 'api/add_user.php';
                const data = editingUserId ? {id: editingUserId, username, password: password || undefined, role} : {username, password, role};

                fetch(endpoint, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                }).then(r => r.json()).then(response => {
                    if (response.success) {
                        userModal.hide();
                        loadUsers();
                    } else {
                        alert('Error: ' + (response.error || 'Unknown error'));
                    }
                }).catch(error => {
                    console.error('Error saving user:', error);
                    alert('Failed to save user');
                });
            });
        }

        window.deleteUser = function(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            fetch('api/delete_user.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            }).then(r => r.json()).then(response => {
                if (response.success) {
                    loadUsers();
                } else {
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            }).catch(error => {
                console.error('Error deleting user:', error);
                alert('Failed to delete user');
            });
        }

        // Initial loads
        loadCategories();
        loadSuppliers();
        loadDiscounts();
        loadShipping();
        loadUsers();
    });
</script>
