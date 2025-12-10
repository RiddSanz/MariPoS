
document.addEventListener('DOMContentLoaded', function() {

    const page_id = document.body.id;

    if (page_id === 'page-dashboard') {
        initializeDashboardPage();
    } else if (page_id === 'page-barang') {
        initializeBarangPage();
    } else if (page_id === 'page-kasir') {
        initializeKasirPage();
        } else if (page_id === 'page-history' || page_id === 'page-histori') {
            initializeHistoryPage();
    }

});
    function initializeDashboardPage() {
        const salesTodayEl = document.getElementById('stats-sales-today');
        const transactionsTodayEl = document.getElementById('stats-transactions-today');
        const lowStockEl = document.getElementById('stats-low-stock');
        const welcomeEl = document.getElementById('welcome-message');

        async function loadStats() {
            try {
                const response = await fetch('api/get_dashboard_stats.php');
                if (!response.ok) throw new Error('Gagal memuat statistik.');
                const stats = await response.json();

                salesTodayEl.textContent = `Rp ${Number(stats.sales_today).toLocaleString('id-ID')}`;
                transactionsTodayEl.textContent = stats.transactions_today;
                lowStockEl.textContent = stats.low_stock_count;

            } catch (error) {
                console.error(error);
                salesTodayEl.textContent = 'Error';
                transactionsTodayEl.textContent = 'Error';
                lowStockEl.textContent = 'Error';
            }
        }

        loadStats();


    }

    function initializeBarangPage() {
        const productModal = new bootstrap.Modal(document.getElementById('product-modal'));
        const addProductBtn = document.getElementById('add-product-btn');
        const productForm = document.getElementById('product-form');
        const modalTitle = document.getElementById('modal-title');
        const productIdInput = document.getElementById('product-id');
        const productsTable = document.getElementById('products-table');
        const categorySelect = document.getElementById('category');

        async function loadCategories() {
            try {
                const response = await fetch('api/get_categories.php');
                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Gagal memuat kategori.');
                const categories = result;
                categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading categories:', error);
                alert(error.message);
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch('api/get_barang.php');
                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Gagal memuat data barang.');
                const products = result;
                const tbody = productsTable.querySelector('tbody');
                tbody.innerHTML = '';
                products.forEach(product => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${product.sku}</td>
                        <td>${product.name}</td>
                        <td>${product.category_name || '-'}</td>
                        <td>Rp ${Number(product.price).toLocaleString('id-ID')}</td>
                        <td>${product.stock}</td>
                        <td class="actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary edit-btn" data-id="${product.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger delete-btn" data-id="${product.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error loading products:', error);
                alert('Gagal memuat data barang: ' + error.message);
            }
        }

        addProductBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Tambah Barang';
            productForm.reset();
            productIdInput.value = '';
            productModal.show();
        });

        productForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const id = productIdInput.value;
            const url = id ? 'api/update_barang.php' : 'api/add_barang.php';
            const formData = new FormData(productForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (response.ok) {
                    alert(result.success);
                    productModal.hide();
                    loadProducts();
                } else {
                    throw new Error(result.error || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                alert(`Error: ${error.message}`);
            }
        });

        productsTable.addEventListener('click', async (event) => {
            const target = event.target;
            const id = target.dataset.id;

            if (target.classList.contains('edit-btn')) {
                try {
                    const response = await fetch(`api/get_barang_by_id.php?id=${id}`);
                    if (!response.ok) throw new Error('Gagal mengambil data barang.');
                    const product = await response.json();
                    modalTitle.textContent = 'Edit Barang';
                    productIdInput.value = product.id;
                    document.getElementById('sku').value = product.sku;
                    document.getElementById('name').value = product.name;
                    document.getElementById('price').value = product.price;
                    document.getElementById('stock').value = product.stock;
                    categorySelect.value = product.category_id;
                    productModal.show();
                } catch (error) {
                    alert(error.message);
                }
            }

            if (target.classList.contains('delete-btn')) {
                if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                    try {
                        const response = await fetch('api/delete_barang.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: id })
                        });
                        const result = await response.json();
                        if (response.ok) {
                            alert(result.success);
                            loadProducts();
                        } else {
                            throw new Error(result.error || 'Gagal menghapus barang');
                        }
                    } catch (error) {
                         alert(`Error: ${error.message}`);
                    }
                }
            }
        });

        loadCategories();
        loadProducts();
    }

    function initializeKasirPage() {
        const productSearchInput = document.getElementById('product-search');
        const productListDiv = document.getElementById('product-list');
        const cartItemsDiv = document.getElementById('cart-items');
        const cartTotalSpan = document.getElementById('cart-total');
        const cartSubtotalSpan = document.getElementById('cart-subtotal');
        const cartDiscountSpan = document.getElementById('cart-discount');
        const checkoutBtn = document.getElementById('checkout-btn');
        const discountSelect = document.getElementById('discount-select');
        const paymentMethodSelect = document.getElementById('payment-method');
        const qrisSection = document.getElementById('qris-section');
        const cashSection = document.getElementById('cash-section');
        const cashReceivedInput = document.getElementById('cash-received');
        const changeSection = document.getElementById('change-section');
        const changeAmountSpan = document.getElementById('change-amount');
        const holdTransactionBtn = document.getElementById('hold-transaction-btn');
        const resumeTransactionBtn = document.getElementById('resume-transaction-btn');
        const resumeModal = new bootstrap.Modal(document.getElementById('resumeModal'));
        const voidModal = new bootstrap.Modal(document.getElementById('voidModal'));

        let cart = [];
        let products = [];
        let discounts = [];
        let shippingConfigs = [];
        let currentDiscount = null;
        let currentShipping = null;

        const debounce = (func, delay) => {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        };

        async function loadDiscounts() {
            try {
                const response = await fetch('api/get_discounts.php');
                if (!response.ok) throw new Error('Gagal memuat diskon');
                discounts = await response.json();
                discountSelect.innerHTML = '<option value="">No Discount</option>';
                discounts.forEach(discount => {
                    const option = document.createElement('option');
                    option.value = discount.id;
                    option.textContent = discount.name;
                    discountSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading discounts:', error);
            }
        }

        async function loadShippingConfigs() {
            try {
                const response = await fetch('api/get_shipping.php');
                if (!response.ok) throw new Error('Gagal memuat konfigurasi pengiriman');
                shippingConfigs = await response.json();
                const shippingSelect = document.getElementById('shipping-select');
                shippingSelect.innerHTML = '<option value="">No Shipping</option>';
                shippingConfigs.forEach(config => {
                    const option = document.createElement('option');
                    option.value = config.id;
                    option.textContent = `${config.vehicle_type} - Rp ${Number(config.transport_fee).toLocaleString('id-ID')}`;
                    shippingSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading shipping configs:', error);
            }
        }

        async function searchProducts(term = '') {
            try {
                const response = await fetch(`api/search_barang.php?term=${encodeURIComponent(term)}`);
                if (!response.ok) throw new Error('Gagal mencari barang');
                products = await response.json();
                renderProductList();
            } catch (error) {
                console.error(error);
                productListDiv.innerHTML = '<p class="text-center text-muted">Gagal memuat barang.</p>';
            }
        }

        function renderProductList() {
            productListDiv.innerHTML = '';
            if (products.length === 0) {
                productListDiv.innerHTML = '<p class="text-center text-muted">Barang tidak ditemukan.</p>';
                return;
            }
            products.forEach(product => {
                const card = document.createElement('div');
                card.className = 'col-md-4 mb-3';
                card.innerHTML = `
                    <div class="card h-100 product-card" data-id="${product.id}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="card-text">${product.category_name || '-'}</p>
                            <p class="card-text fw-bold">Rp ${Number(product.price).toLocaleString('id-ID')}</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Stok: ${product.stock}</small>
                        </div>
                    </div>
                `;
                productListDiv.appendChild(card);
            });
        }

        function addToCart(productId) {
            const product = products.find(p => p.id == productId);
            if (!product) return;

            const cartItem = cart.find(item => item.id == productId);

            if (cartItem) {
                if (cartItem.quantity < product.stock) {
                    cartItem.quantity++;
                } else {
                    alert('Insufficient stock !');
                }
            } else {
                if (product.stock > 0) {
                    cart.push({ ...product, quantity: 1 });
                } else {
                    alert('Stok habis.');
                }
            }
            renderCart();
        }

        function renderCart() {
            cartItemsDiv.innerHTML = '';
            if (cart.length === 0) {
                cartItemsDiv.innerHTML = '<p class="text-center text-muted">Keranjang masih kosong.</p>';
                checkoutBtn.disabled = true;
            } else {
                cart.forEach(item => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'd-flex justify-content-between align-items-center mb-2';
                    itemDiv.innerHTML = `
                        <div>
                            <p class="mb-0 fw-bold">${item.name}</p>
                            <small class="text-muted">Rp ${Number(item.price).toLocaleString('id-ID')}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary qty-btn" data-id="${item.id}" data-action="decrease">-</button>
                            <span class="mx-2">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-secondary qty-btn" data-id="${item.id}" data-action="increase">+</button>
                            <button class="btn btn-sm btn-outline-danger remove-btn ms-2" data-id="${item.id}">x</button>
                        </div>
                    `;
                    cartItemsDiv.appendChild(itemDiv);
                });
                checkoutBtn.disabled = false;
            }
            updateCartTotal();
        }

        function updateCartTotal() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let shippingCost = 0;
            if (currentShipping) {
                shippingCost = parseFloat(currentShipping.transport_fee);
            }
            let discountAmount = 0;
            if (currentDiscount) {
                if (currentDiscount.type === 'percentage') {
                    discountAmount = (subtotal + shippingCost) * (parseFloat(currentDiscount.value) / 100);
                } else {
                    discountAmount = parseFloat(currentDiscount.value);
                }
            }
            const total = subtotal + shippingCost - discountAmount;

            cartSubtotalSpan.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            document.getElementById('cart-shipping').textContent = `Rp ${shippingCost.toLocaleString('id-ID')}`;
            cartDiscountSpan.textContent = `Rp ${discountAmount.toLocaleString('id-ID')}`;
            cartTotalSpan.textContent = `Rp ${total.toLocaleString('id-ID')}`;

            // Update hold button
            holdTransactionBtn.disabled = cart.length === 0;

            // Calculate change if cash
            calculateChange(total);
        }

        function updateCartItemQuantity(productId, action) {
            const cartItem = cart.find(item => item.id == productId);
            if (!cartItem) return;

            if (action === 'increase') {
                const product = products.find(p => p.id == productId);
                if (cartItem.quantity < product.stock) {
                    cartItem.quantity++;
                } else {
                    alert('Stok tidak mencukupi.');
                }
            } else if (action === 'decrease') {
                cartItem.quantity--;
                if (cartItem.quantity === 0) {
                    cart = cart.filter(item => item.id != productId);
                }
            }
            renderCart();
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.id != productId);
            renderCart();
        }

        function calculateChange(total) {
            const paymentMethod = paymentMethodSelect.value;
            if (paymentMethod === 'cash') {
                const cashReceived = parseFloat(cashReceivedInput.value) || 0;
                const change = cashReceived - total;
                if (change >= 0) {
                    changeAmountSpan.textContent = `Rp ${change.toLocaleString('id-ID')}`;
                    changeSection.style.display = 'block';
                } else {
                    changeSection.style.display = 'none';
                }
            } else {
                changeSection.style.display = 'none';
            }
        }

        function applyDiscount(discountId) {
            if (discountId === '') {
                currentDiscount = null;
            } else {
                currentDiscount = discounts.find(d => d.id == discountId);
            }
            updateCartTotal();
        }

        function applyShipping(shippingId) {
            if (shippingId === '') {
                currentShipping = null;
            } else {
                currentShipping = shippingConfigs.find(s => s.id == shippingId);
            }
            updateCartTotal();
        }

        function holdTransaction() {
            if (cart.length === 0) return;
            const heldTransaction = {
                id: Date.now(),
                cart: cart,
                discount: currentDiscount,
                shipping: currentShipping,
                timestamp: new Date().toISOString()
            };
            let heldTransactions = JSON.parse(localStorage.getItem('heldTransactions') || '[]');
            heldTransactions.push(heldTransaction);
            localStorage.setItem('heldTransactions', JSON.stringify(heldTransactions));
            cart = [];
            currentDiscount = null;
            currentShipping = null;
            discountSelect.value = '';
            document.getElementById('shipping-select').value = '';
            renderCart();
            alert('Transaction held successfully');
        }

        function loadHeldTransactions() {
            const heldTransactions = JSON.parse(localStorage.getItem('heldTransactions') || '[]');
            const listDiv = document.getElementById('held-transactions-list');
            listDiv.innerHTML = '';
            if (heldTransactions.length === 0) {
                listDiv.innerHTML = '<p class="text-center text-muted">No held transactions</p>';
                return;
            }
            heldTransactions.forEach(trx => {
                const item = document.createElement('div');
                item.className = 'p-2 border mb-2';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Held at ${new Date(trx.timestamp).toLocaleString()}</strong>
                            <br>
                            <small>${trx.cart.length} items</small>
                        </div>
                        <button class="btn btn-success btn-sm" onclick="resumeTransaction(${trx.id})">Resume</button>
                    </div>
                `;
                listDiv.appendChild(item);
            });
        }

        function resumeTransaction(id) {
            const heldTransactions = JSON.parse(localStorage.getItem('heldTransactions') || '[]');
            const trx = heldTransactions.find(t => t.id == id);
            if (trx) {
                cart = trx.cart;
                currentDiscount = trx.discount;
                currentShipping = trx.shipping;
                discountSelect.value = currentDiscount ? currentDiscount.id : '';
                document.getElementById('shipping-select').value = currentShipping ? currentShipping.id : '';
                renderCart();
                // Remove from held
                const updated = heldTransactions.filter(t => t.id != id);
                localStorage.setItem('heldTransactions', JSON.stringify(updated));
                resumeModal.hide();
            }
        }

        window.resumeTransaction = resumeTransaction; // Make global for onclick

        productSearchInput.addEventListener('input', debounce(e => searchProducts(e.target.value), 300));

        productListDiv.addEventListener('click', e => {
            const card = e.target.closest('.product-card');
            if (card) {
                addToCart(card.dataset.id);
            }
        });

        cartItemsDiv.addEventListener('click', e => {
            const target = e.target;
            const id = target.dataset.id;
            if (target.classList.contains('qty-btn')) {
                updateCartItemQuantity(id, target.dataset.action);
            } else if (target.classList.contains('remove-btn')) {
                removeFromCart(id);
            }
        });

        // Shipping select
        document.getElementById('shipping-select').addEventListener('change', e => {
            applyShipping(e.target.value);
        });

        // Discount select
        discountSelect.addEventListener('change', e => {
            applyDiscount(e.target.value);
        });

        // Payment method
        paymentMethodSelect.addEventListener('change', e => {
            const method = e.target.value;
            qrisSection.style.display = method === 'qris' ? 'block' : 'none';
            cashSection.style.display = method === 'cash' ? 'block' : 'none';
            if (method !== 'cash') {
                changeSection.style.display = 'none';
            } else {
                calculateChange(parseFloat(cartTotalSpan.textContent.replace(/[^0-9]/g, '')));
            }
        });

        // QRIS Modal
        document.getElementById('qrisModal').addEventListener('show.bs.modal', () => {
            const total = parseFloat(cartTotalSpan.textContent.replace(/[^0-9]/g, ''));
            document.getElementById('qris-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        });

        document.getElementById('confirm-qris-payment').addEventListener('click', () => {
            // Close QRIS modal and proceed with checkout
            bootstrap.Modal.getInstance(document.getElementById('qrisModal')).hide();
            // Trigger checkout
            document.getElementById('checkout-btn').click();
        });

        // Cash received
        cashReceivedInput.addEventListener('input', () => {
            const total = parseFloat(cartTotalSpan.textContent.replace(/[^0-9]/g, ''));
            calculateChange(total);
        });

        // Hold transaction
        holdTransactionBtn.addEventListener('click', holdTransaction);

        // Resume transaction
        resumeTransactionBtn.addEventListener('click', () => {
            loadHeldTransactions();
            resumeModal.show();
        });

        // Checkout handler
        checkoutBtn.addEventListener('click', async () => {
            if (cart.length === 0) {
                alert('Keranjang masih kosong.');
                return;
            }

            const paymentMethod = paymentMethodSelect.value;
            if (!paymentMethod) {
                alert('Pilih metode pembayaran.');
                return;
            }

            if (paymentMethod === 'cash') {
                const cashReceived = parseFloat(cashReceivedInput.value) || 0;
                const total = parseFloat(cartTotalSpan.textContent.replace(/[^0-9]/g, ''));
                if (cashReceived < total) {
                    alert('Uang tunai kurang.');
                    return;
                }
            }

            if (!confirm('Lanjutkan checkout?')) return;

            try {
                const data = {
                    cart,
                    discount: currentDiscount,
                    shipping: currentShipping,
                    payment_method: paymentMethod,
                    cash_received: paymentMethod === 'cash' ? parseFloat(cashReceivedInput.value) : null
                };
                const response = await fetch('api/proses_transaksi.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Checkout gagal');

                alert(result.success);
                // Open receipt in new window
                window.open(`print_struk.php?id=${result.transaction_id}`, '_blank');
                cart = [];
                currentDiscount = null;
                currentShipping = null;
                discountSelect.value = '';
                document.getElementById('shipping-select').value = '';
                cashReceivedInput.value = '';
                renderCart();
                searchProducts(); // Refresh product list to update stock

            } catch (error) {
                console.error('Checkout error:', error);
                alert(`Error: ${error.message}`);
            }
        });

        // Initial load
        loadDiscounts();
        loadShippingConfigs();
        searchProducts();
    }

    function initializeHistoryPage() {
        const transactionList = document.getElementById('transaction-list');
        if (!transactionList) {
            console.error('Transaction list container not found. Page might be outdated, please refresh.');
            return;
        }
        
        const dateRangeModal = new bootstrap.Modal(document.getElementById('dateRangeModal'));
        let currentPeriod = {
            start: new Date().toISOString().split('T')[0],
            end: new Date().toISOString().split('T')[0]
        };

        // Format currency helper
        const formatCurrency = (amount) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        };

        async function loadSummary() {
            try {
                const response = await fetch(`api/get_transaction_summary.php?start_date=${currentPeriod.start}&end_date=${currentPeriod.end}`);
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Failed to load summary');
            
                document.getElementById('total-income').textContent = formatCurrency(data.summary?.total_income || 0);
                document.getElementById('total-transactions').textContent = data.summary?.total_transactions || 0;
                document.getElementById('avg-transaction').textContent = formatCurrency(data.summary?.avg_transaction || 0);
                document.getElementById('best-seller').textContent = data.bestSeller?.product_name || '-';

            } catch (error) {
                console.error('Summary load error:', error);
                ['total-income', 'total-transactions', 'avg-transaction', 'best-seller'].forEach(id => {
                    document.getElementById(id).textContent = 'Error loading data';
                });
            }
        }

        async function loadHistori() {
            try {
                transactionList.innerHTML = '<p class="text-center"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Loading...</span></div></p>';
                
                const response = await fetch(`api/get_histori.php`);
                if (!response.ok) throw new Error('Gagal memuat riwayat transaksi.');
                const transactions = await response.json();

                transactionList.innerHTML = '';
                if (transactions.length === 0) {
                    transactionList.innerHTML = '<p class="text-center text-muted">No transactions found for this period.</p>';
                    return;
                }

                transactions.forEach(trx => {
                    const item = document.createElement('div');
                    item.className = 'accordion-item';
                    item.innerHTML = `
                        <h2 class="accordion-header" id="heading-${trx.id}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${trx.id}" aria-expanded="false" aria-controls="collapse-${trx.id}">
                                <div class="d-flex w-100 justify-content-between">
                                    <span>#${String(trx.id).padStart(6, '0')}</span>
                                    <span>${new Date(trx.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</span>
                                    <span>${trx.username}</span>
                                    <span class="fw-bold">Rp ${Number(trx.total_amount).toLocaleString('id-ID')}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-${trx.id}" class="accordion-collapse collapse" aria-labelledby="heading-${trx.id}" data-bs-parent="#transaction-list">
                            <div class="accordion-body">
                                Memuat detail...
                            </div>
                        </div>
                    `;
                    transactionList.appendChild(item);
                });

            } catch (error) {
                console.error(error);
                transactionList.innerHTML = `<p class="text-center text-danger">${error.message}</p>`;
            }
        }

        // Add collapse event to the transaction list container
        transactionList.addEventListener('show.bs.collapse', async e => {
            const accordionBody = e.target.querySelector('.accordion-body');
            if (accordionBody.innerHTML.trim() === 'Memuat detail...') {
                const id = e.target.id.replace('collapse-', '');
                try {
                    const response = await fetch(`api/get_histori_detail.php?id=${id}`);
                    if (!response.ok) {
                        const err = await response.json();
                        throw new Error(err.error || 'Gagal memuat detail.');
                    }
                    const details = await response.json();

                    let tableHTML = '<table class="table table-sm"><thead><tr><th>Barang</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>';
                    details.forEach(d => {
                        tableHTML += `
                            <tr>
                                <td>${d.name}</td>
                                <td>${d.quantity}</td>
                                <td>Rp ${Number(d.price).toLocaleString('id-ID')}</td>
                                <td>Rp ${(d.quantity * d.price).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });
                    tableHTML += '</tbody></table>';
                    accordionBody.innerHTML = tableHTML;

                    // Add view details button
                    const viewBtn = document.createElement('button');
                    viewBtn.className = 'btn btn-primary btn-sm mt-2';
                    viewBtn.textContent = 'View Full Details';
                    viewBtn.onclick = () => showTransactionModal(id);
                    accordionBody.appendChild(viewBtn);

                } catch (error) {
                    console.error(error);
                    accordionBody.innerHTML = `<p class="text-danger">${error.message}</p>`;
                }
            }
        });

        async function showTransactionModal(id) {
            const modal = document.getElementById('transactionModal');
            modal.dataset.transactionId = id;
            const detailsDiv = document.getElementById('transaction-details');
            detailsDiv.innerHTML = 'Loading...';
            const voidBtn = document.getElementById('void-transaction');
            voidBtn.style.display = 'none';

            try {
                const response = await fetch(`api/get_transaction_details.php?id=${id}`);
                if (!response.ok) throw new Error('Failed to load transaction details');
                const data = await response.json();

                let html = `<p><strong>Transaction ID:</strong> #${String(data.transaction.id).padStart(6, '0')}</p>`;
                html += `<p><strong>Date:</strong> ${new Date(data.transaction.created_at).toLocaleString()}</p>`;
                html += `<p><strong>User:</strong> ${data.transaction.username}</p>`;
                html += `<p><strong>Status:</strong> ${data.transaction.status}</p>`;
                html += `<p><strong>Payment Method:</strong> ${data.transaction.payment_method || 'N/A'}</p>`;
                if (data.transaction.discount_amount > 0) {
                    html += `<p><strong>Discount:</strong> Rp ${Number(data.transaction.discount_amount).toLocaleString('id-ID')}</p>`;
                }
                html += `<p><strong>Total:</strong> Rp ${Number(data.transaction.total_amount).toLocaleString('id-ID')}</p>`;

                html += '<h6>Items:</h6><ul>';
                data.details.forEach(item => {
                    html += `<li>${item.name} x${item.quantity} - Rp ${(item.quantity * item.price).toLocaleString('id-ID')}</li>`;
                });
                html += '</ul>';

                detailsDiv.innerHTML = html;

                // Show void button if admin and status completed
                if (data.canVoid) {
                    voidBtn.style.display = 'inline-block';
                }

                new bootstrap.Modal(modal).show();

            } catch (error) {
                console.error(error);
                detailsDiv.innerHTML = `<p class="text-danger">${error.message}</p>`;
            }
        }

        // Date range handling
        document.querySelectorAll('[data-period]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const period = e.target.dataset.period;
                const today = new Date();
                let start = new Date();
                let end = new Date();

                switch(period) {
                    case 'today':
                        break;
                    case 'week':
                        start.setDate(today.getDate() - 7);
                        break;
                    case 'month':
                        start.setMonth(today.getMonth() - 1);
                        break;
                    case 'year':
                        start.setFullYear(today.getFullYear() - 1);
                        break;
                    case 'custom':
                        dateRangeModal.show();
                        return;
                }

                currentPeriod = {
                    start: start.toISOString().split('T')[0],
                    end: end.toISOString().split('T')[0]
                };
                loadSummary();
                loadHistori();
            });
        });

        // Custom date range (button inside the modal)
        document.getElementById('apply-date-range').addEventListener('click', () => {
            const start = document.getElementById('start-date').value;
            const end = document.getElementById('end-date').value;
            if (!start || !end) {
                alert('Please select both start and end dates');
                return;
            }
            currentPeriod = { start, end };
            dateRangeModal.hide();
            loadSummary();
            loadHistori();
        });

        // Print summary
        document.getElementById('print-summary').addEventListener('click', () => {
            window.open(`print_summary.php?start_date=${currentPeriod.start}&end_date=${currentPeriod.end}`, '_blank');
        });

        // Void transaction
        document.getElementById('void-transaction').addEventListener('click', async () => {
            if (!confirm('Are you sure you want to void this transaction? This will restore the stock.')) return;
            const transactionId = document.getElementById('transactionModal').dataset.transactionId;
            try {
                const response = await fetch('api/void_transaction.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: transactionId })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Void failed');
                alert('Transaction voided successfully');
                bootstrap.Modal.getInstance(document.getElementById('transactionModal')).hide();
                loadSummary();
                loadHistori();
            } catch (error) {
                console.error('Void error:', error);
                alert(`Error: ${error.message}`);
            }
        });

        // Initial load
        loadSummary();
        loadHistori();
    }


