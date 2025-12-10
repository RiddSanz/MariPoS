<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Transaction History</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="print-summary">
                <i class="bi bi-printer"></i> Print Summary
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-calendar"></i> Filter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-period="today">Today</a></li>
                <li><a class="dropdown-item" href="#" data-period="week">This Week</a></li>
                <li><a class="dropdown-item" href="#" data-period="month">This Month</a></li>
                <li><a class="dropdown-item" href="#" data-period="year">This Year</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" data-period="custom">Custom Range</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Income</h5>
                <h3 class="card-text" id="total-income">Rp 0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Transaksi</h5>
                <h3 class="card-text" id="total-transactions">0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body">
                <h5 class="card-title">Avg. Transaction</h5>
                <h3 class="card-text" id="avg-transaction">Rp 0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h5 class="card-title">Best Selling Item</h5>
                <h3 class="card-text" id="best-seller">-</h3>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Modal -->
<div class="modal fade" id="dateRangeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Date Range</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start-date">
                </div>
                <div class="mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end-date">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="apply-date-range">Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="accordion" id="transaction-list">
    <p class="text-center text-muted">Loading transactions...</p>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transaction-details">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="void-transaction" style="display: none;">
                    <i class="bi bi-x-circle"></i> Void Transaction
                </button>
                <button type="button" class="btn btn-primary" id="print-transaction">
                    <i class="bi bi-printer"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>