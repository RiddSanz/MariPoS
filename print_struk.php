<?php
require_once 'config/database.php';
check_login();

$transaction_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($transaction_id <= 0) {
    die('Invalid Transaction ID.');
}

// Ambil data transaksi utama
$stmt = $conn->prepare("SELECT t.id, t.created_at, t.total_amount, u.username FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id = :id");
$stmt->execute(['id' => $transaction_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die('Transaction not found.');
}

// Ambil detail item transaksi
$stmt = $conn->prepare("SELECT td.quantity, td.price, p.name FROM transaction_details td JOIN products p ON td.product_id = p.id WHERE td.transaction_id = :id");
$stmt->execute(['id' => $transaction_id]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi #<?php echo $transaction['id']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        h2 { margin: 0; }
        p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px 0; }
        .right { text-align: right; }
        .separator { border-top: 1px dashed #000; margin: 10px 0; }
        .footer { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <?php
        $app_name = get_setting('app_name', 'Parid Store');
        $address = get_setting('address', '');
        $phone = get_setting('phone', '');
        $logo = get_setting('logo_path', '');
        ?>
        <?php if ($logo && file_exists($logo)): ?>
            <div><img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="max-width:120px; max-height:80px;"></div>
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($app_name); ?></h2>
        <p><?php echo htmlspecialchars($address); ?></p>
        <p>Telp: <?php echo htmlspecialchars($phone); ?></p>
    </div>

    <p>No: <?php echo str_pad($transaction['id'], 6, '0', STR_PAD_LEFT); ?></p>
    <p>Kasir: <?php echo htmlspecialchars($transaction['username']); ?></p>
    <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></p>

    <div class="separator"></div>

    <table>
        <tbody>
            <?php foreach ($details as $item): ?>
            <tr>
                <td colspan="2"><?php echo htmlspecialchars($item['name']); ?></td>
            </tr>
            <tr>
                <td><?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                <td class="right"><?php echo number_format($item['quantity'] * $item['price'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="separator"></div>

    <table>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="right"><strong>Rp <?php echo number_format($transaction['total_amount'], 0, ',', '.'); ?></strong></td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="footer">
        <p>Terima kasih telah berbelanja!</p>
    </div>

    <script>
        // Auto open print dialog and close after printing
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
        window.onafterprint = function() {
            try { window.close(); } catch(e) {}
        }
    </script>
</body>
</html>
