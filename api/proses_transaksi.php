<?php
header('Content-Type: application/json');
require_once '../config/database.php';

check_login_api(); // Pastikan user sudah login

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metode tidak diizinkan']);
    exit();
}

    $data = json_decode(file_get_contents('php://input'), true);
    $cart = $data['cart'] ?? [];
    $discount = $data['discount'] ?? null;
    $shipping = $data['shipping'] ?? null;
    $payment_method = $data['payment_method'] ?? 'cash';
    $cash_received = $data['cash_received'] ?? null;

if (empty($cart)) {
    http_response_code(400);
    echo json_encode(['error' => 'Keranjang tidak boleh kosong.']);
    exit();
}

$subtotal = 0;
$discount_amount = 0;
$user_id = $_SESSION['user_id'];

// Mulai transaction
$conn->beginTransaction();

try {
    // 1. Validasi stok dan hitung subtotal
    foreach ($cart as $item) {
        $stmt = $conn->prepare("SELECT price, stock FROM products WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $item['id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Produk dengan ID {$item['id']}' tidak ditemukan.");
        }
        if ($product['stock'] < $item['quantity']) {
            throw new Exception("Stok untuk '{$item['name']}' tidak mencukupi.");
        }
        $subtotal += $product['price'] * $item['quantity'];
    }

    // 2. Hitung shipping
    $shipping_cost = 0;
    if ($shipping) {
        $shipping_cost = $shipping['transport_fee'];
    }

    // 3. Hitung discount
    if ($discount) {
        if ($discount['type'] === 'percentage') {
            $discount_amount = ($subtotal + $shipping_cost) * ($discount['value'] / 100);
        } else {
            $discount_amount = $discount['value'];
        }
    }
    $total_amount = $subtotal + $shipping_cost - $discount_amount;

    // 3. Buat entri di tabel transactions
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, total_amount, payment_method, discount_amount, status) VALUES (:user_id, :total_amount, :payment_method, :discount_amount, 'completed')");
    $stmt->execute([
        'user_id' => $user_id,
        'total_amount' => $total_amount,
        'payment_method' => $payment_method,
        'discount_amount' => $discount_amount
    ]);
    $transaction_id = $conn->lastInsertId();

    // 4. Buat entri di transaction_details dan update stok
    $update_stock_stmt = $conn->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :id");
    $insert_detail_stmt = $conn->prepare("INSERT INTO transaction_details (transaction_id, product_id, quantity, price) VALUES (:transaction_id, :product_id, :quantity, :price)");

    foreach ($cart as $item) {
        // Masukkan ke detail transaksi
        $insert_detail_stmt->execute([
            'transaction_id' => $transaction_id,
            'product_id' => $item['id'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ]);

        // Kurangi stok
        $update_stock_stmt->execute([
            'quantity' => $item['quantity'],
            'id' => $item['id']
        ]);
    }

    // Jika semua berhasil, commit transaction
    $conn->commit();

    http_response_code(201);
    echo json_encode(['success' => 'Transaksi berhasil!', 'transaction_id' => $transaction_id]);

} catch (Exception $e) {
    // Jika ada error, rollback
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>