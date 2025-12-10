<?php
// File ini berisi logika untuk membuat tabel dan mengisi data awal (seeding)

try {
    // 1. Tabel Users
    $conn->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL, -- Increased length for future hashing
        `role` enum('admin','kasir') NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Tabel Categories
    $conn->exec("CREATE TABLE IF NOT EXISTS `categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `prefix` varchar(50) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 3. Tabel Products
    $conn->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `sku` varchar(50) NOT NULL,
        `name` varchar(100) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        `stock` int(11) NOT NULL,
        `category_id` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `sku` (`sku`),
        KEY `category_id` (`category_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 4. Tabel Transactions
    $conn->exec("CREATE TABLE IF NOT EXISTS `transactions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `total_amount` decimal(10,2) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 5. Tabel Transaction Details
    $conn->exec("CREATE TABLE IF NOT EXISTS `transaction_details` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `transaction_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `quantity` int(11) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `transaction_id` (`transaction_id`),
        KEY `product_id` (`product_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 6. Tabel Suppliers
    $conn->exec("CREATE TABLE IF NOT EXISTS `suppliers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `contact` varchar(50) DEFAULT NULL,
        `address` text DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 7. Tabel Discounts
    $conn->exec("CREATE TABLE IF NOT EXISTS `discounts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `type` enum('percentage','fixed') NOT NULL,
        `value` decimal(10,2) NOT NULL,
        `applicable_to` enum('all','categories','products') NOT NULL DEFAULT 'all',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 8. Tabel Shipping Configs
    $conn->exec("CREATE TABLE IF NOT EXISTS `shipping_configs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `vehicle_type` varchar(50) NOT NULL,
        `fuel_price_per_liter` decimal(10,2) NOT NULL,
        `fuel_consumption_per_km` decimal(5,2) NOT NULL,
        `driver_wage` decimal(10,2) NOT NULL,
        `transport_fee` decimal(10,2) NOT NULL,
        `max_volume` decimal(10,2) DEFAULT NULL,
        `max_weight` decimal(10,2) DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 6. Isi data awal (seeding) untuk users jika tabelnya kosong
    $stmt = $conn->query("SELECT id FROM users");
    if ($stmt->rowCount() == 0) {
        $conn->exec("INSERT INTO `users` (`username`, `password`, `role`) VALUES
            ('admin', 'admin', 'admin'),
      ('kasir', 'kasir', 'kasir');");
    }

    // 7. Isi data awal untuk categories jika tabelnya kosong
    $stmt = $conn->query("SELECT id FROM categories");
    if ($stmt->rowCount() == 0) {
    $conn->exec("INSERT INTO `categories` (`name`, `prefix`) VALUES
      ('Product', 'PROD'),
      ('Services', 'SVC'),
      ('Keyboard', 'KEYB');
      ");
    }

    // 9. Alter transactions table to add new columns (check if columns exist first)
    $stmt = $conn->query("SHOW COLUMNS FROM `transactions` LIKE 'status'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE `transactions` ADD COLUMN `status` enum('pending','hold','completed','voided','refunded') NOT NULL DEFAULT 'completed'");
    }

    $stmt = $conn->query("SHOW COLUMNS FROM `transactions` LIKE 'payment_method'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE `transactions` ADD COLUMN `payment_method` varchar(50) DEFAULT NULL");
    }

    $stmt = $conn->query("SHOW COLUMNS FROM `transactions` LIKE 'discount_amount'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE `transactions` ADD COLUMN `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00");
    }

    // 10. Create settings table for app-wide settings
  $conn->exec("CREATE TABLE IF NOT EXISTS `settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `k` varchar(100) NOT NULL,
    `v` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `k` (`k`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  // Seed settings if empty
  $stmt = $conn->query("SELECT id FROM settings");
  if ($stmt->rowCount() == 0) {
    $conn->exec("INSERT INTO `settings` (`k`,`v`) VALUES
      ('app_name','Parid Store'),
      ('address','Jl. Raya Kediri - Pare'),
      ('phone','0889-9160-8239'),
      ('logo_path','assets/img/logo.png')
    ;");
  }

} catch (PDOException $e) {
    die("Error saat setup tabel: " . $e->getMessage());
}
?>
