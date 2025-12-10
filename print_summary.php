<?php
require_once 'config/database.php';
require_once 'config/setup.php';
check_login();

$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$settings = get_all_settings();

// Get summary data
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_transactions,
        COALESCE(SUM(total_amount), 0) as total_income,
        COALESCE(AVG(total_amount), 0) as avg_transaction
    FROM transactions 
    WHERE DATE(created_at) BETWEEN :start AND :end
");
$stmt->execute(['start' => $start_date, 'end' => $end_date]);
$summary = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize with zeros if null
$summary['total_transactions'] = (int)$summary['total_transactions'];
$summary['total_income'] = (float)$summary['total_income'];
$summary['avg_transaction'] = (float)$summary['avg_transaction'];

// Get category breakdown
$stmt = $conn->prepare("
    SELECT 
        c.name as category,
        COUNT(DISTINCT t.id) as transaction_count,
        SUM(td.quantity) as items_sold,
        SUM(td.price * td.quantity) as revenue
    FROM transaction_details td
    JOIN products p ON p.id = td.product_id
    JOIN categories c ON c.id = p.category_id
    JOIN transactions t ON t.id = td.transaction_id
    WHERE DATE(t.created_at) BETWEEN :start AND :end
    GROUP BY c.id, c.name
    ORDER BY revenue DESC
");
$stmt->execute(['start' => $start_date, 'end' => $end_date]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top products
$stmt = $conn->prepare("
    SELECT 
        p.name as product_name,
        SUM(td.quantity) as quantity_sold,
        SUM(td.price * td.quantity) as revenue
    FROM transaction_details td
    JOIN products p ON p.id = td.product_id
    JOIN transactions t ON t.id = td.transaction_id
    WHERE DATE(t.created_at) BETWEEN :start AND :end
    GROUP BY p.id, p.name
    ORDER BY quantity_sold DESC
    LIMIT 10
");
$stmt->execute(['start' => $start_date, 'end' => $end_date]);
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Transaction Summary</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary-box { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 20px;
            border-radius: 5px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        th, td { 
            padding: 8px; 
            border: 1px solid #ddd; 
            text-align: left; 
        }
        th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        @media print {
            body { margin: 0; }
            button { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo htmlspecialchars($settings['app_name'] ?? 'Parid Store'); ?></h1>
        <p><?php echo htmlspecialchars($settings['address'] ?? ''); ?></p>
        <h2>Transaction Summary Report</h2>
        <p>Period: <?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?></p>
    </div>

    <div class="summary-box">
        <h3>Summary</h3>
        <table>
            <tr>
                <td>Total Transactions:</td>
                <td class="text-right"><?php echo number_format($summary['total_transactions']); ?></td>
            </tr>
            <tr>
                <td>Total Income:</td>
                <td class="text-right">Rp <?php echo number_format($summary['total_income']); ?></td>
            </tr>
            <tr>
                <td>Average Transaction:</td>
                <td class="text-right">Rp <?php echo number_format($summary['avg_transaction']); ?></td>
            </tr>
        </table>
    </div>

    <h3>Category Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Transactions</th>
                <th>Items Sold</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
            <tr>
                <td colspan="4" class="text-center">No data available for this period</td>
            </tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cat['category']); ?></td>
                    <td class="text-right"><?php echo number_format($cat['transaction_count']); ?></td>
                    <td class="text-right"><?php echo number_format($cat['items_sold']); ?></td>
                    <td class="text-right">Rp <?php echo number_format($cat['revenue']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Top Products</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity Sold</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($topProducts)): ?>
            <tr>
                <td colspan="3" class="text-center">No products sold in this period</td>
            </tr>
            <?php else: ?>
                <?php foreach ($topProducts as $prod): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['product_name']); ?></td>
                    <td class="text-right"><?php echo number_format($prod['quantity_sold']); ?></td>
                    <td class="text-right">Rp <?php echo number_format($prod['revenue']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p style="text-align: center; margin-top: 30px;">
        Generated on <?php echo date('d M Y H:i:s'); ?>
    </p>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>