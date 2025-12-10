# MariPoS (Modern Point of Sales)

**MariPoS** adalah sistem Point of Sales (POS) yang komprehensif, ringan, dan efisien yang dibangun menggunakan PHP native. Sistem ini dirancang untuk menyederhanakan operasional ritel, menawarkan alat yang lengkap untuk pemrosesan transaksi, manajemen inventaris, dan analitik bisnis.

## 🚀 Fitur Utama

### 🛒 Point of Sales (Kasir)
- **Pemrosesan Transaksi Cepat**: Interface yang dioptimalkan untuk checkout yang cepat.
- **Manajemen Keranjang**: Tambah, hapus, dan sesuaikan jumlah item dengan mudah.
- **Cetak Struk**: Menghasilkan struk profesional (`print_struk.php`) untuk printer thermal.
- **Hold/Void Transaksi**: Fleksibilitas status transaksi (pending, hold, completed, voided).

### 📦 Manajemen Inventaris
- **Pelacakan Produk**: Kelola produk, stok, dan harga.
- **Kategori**: Mengatur produk ke dalam kategori agar mudah dicari.
- **Manajemen Supplier**: Melacak data pemasok dan kontak.
- **Dukungan Barcode/SKU**: Pencarian produk yang efisien menggunakan SKU.

### 📊 Dashboard & Analytics
- **Statistik Real-time**: Lihat penjualan harian, total transaksi, dan produk terlaris.
- **Riwayat Penjualan**: Log transaksi mendetail dengan opsi penyaringan (filter).
- **Laporan**: Hasilkan ringkasan untuk wawasan bisnis.

### ⚙️ Utilitas & Pengaturan
- **Manajemen Pengguna**: Role-based access control (Admin & Kasir).
- **Diskon & Penawaran**: Kelola diskon promosi.
- **Konfigurasi Pengiriman**: Hitung biaya pengiriman berdasarkan jenis kendaraan dan jarak konsumsi BBM. (Pending)
- **Pengaturan Aplikasi**: Kontrol penuh atas detail toko (Nama, Alamat, Logo).

## 🛠 Teknologi yang Digunakan

- **Backend**: PHP (Native, PDO)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, Bootstrap (Desain Responsif)
- **Scripting**: jQuery / AJAX (untuk interaksi dinamis)

## 📂 Struktur Proyek

```bash
📦 MariPoS
├── 📂 api/          # Endpoint API Backend untuk request AJAX
├── 📂 assets/       # Static assets (CSS, JS, Gambar)
├── 📂 config/       # Database configuration and setup script
├── 📂 includes/     # Reusable PHP components (Header, Footer, Navbar)
├── 📂 pages/        # Main application views (Dashboard, Kasir, Admin)
├── 📄 index.php     # Entry point application
├── 📄 login.php     # Authentication page (Login)
└── 📄 README.md     # Project Documentations
```

## 🔧 Installation Guide

1. **Clone Repository**
   ```bash
   git clone https://github.com/RiddSanz/MariPoS.git
   cd MariPoS
   ```

2. **Database Configuration**
   - Open file `config/database.php`.
   - Update database credentials to match your local environment (XAMPP/Laragon/etc).

   ```php
   // config/database.php
   $host = 'localhost';
   $db_name = 'maripos'; // Database will be created automatically if it doesn't exist
   $username = 'root';
   $password = '';
   ```

3. **Database Initialization**
   - **Good News!** You don't need to manually import the SQL file.
   - Just access the application through your browser. The system is designed to **automatically create the database and tables needed** when first run.

4. **Run Application**
   - Open your browser and visit:
     ```
     http://localhost/MariPoS/
     ```
   - You will be redirected to the login page.

## 🔑 Default Account

The system is preloaded with default accounts (created automatically by `setup.php`):

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `admin` |
| **Kasir** | `kasir` | `kasir` |

> ⚠️ **Security Note**: Please change the password immediately after the first login.
