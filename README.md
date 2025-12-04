# Sistem Informasi Manajemen Apotek & Klinik

Sistem Apotek adalah aplikasi berbasis web yang dibangun menggunakan framework Laravel. Aplikasi ini dirancang untuk mempermudah operasional apotek dan klinik, mulai dari manajemen stok obat, transaksi penjualan (POS), pembelian ke supplier, hingga pencatatan tindakan medis dan konsultasi.

## 🌟 Fitur Utama

### 1. Manajemen Inventaris (Inventory)
* **Master Data Obat:** Manajemen data obat lengkap dengan dukungan *batch*, tanggal kedaluwarsa (expired date), dan kategori (termasuk psikotropika).
* **Stok Opname:** Fitur untuk penyesuaian stok fisik dan sistem.
* **Pergerakan Stok (Stock Movement):** Melacak riwayat masuk dan keluarnya barang.
* **Kartu Stok:** Monitoring detail perubahan stok per item.

### 2. Point of Sales (POS) / Kasir
* Antarmuka kasir yang responsif dan mudah digunakan.
* Dukungan untuk shift kasir (Buka/Tutup Shift).
* Cetak struk belanja dan invoice.
* Manajemen keranjang belanja (Cart).
* Integrasi data pelanggan saat transaksi.

### 3. Pembelian & Supplier
* **Surat Pesanan (SP):** Pembuatan surat pesanan (Reguler/Prekursor) dengan fitur ekspor PDF.
* **Pembelian:** Pencatatan faktur pembelian dari supplier.
* **Manajemen Supplier:** Database pemasok obat.

### 4. Layanan Klinis (Clinic Integration)
* **Pendaftaran & Konsultasi:** Pencatatan data konsultasi pasien.
* **Tindakan Medis:** Manajemen jenis tindakan medis dan biayanya.
* Integrasi resep obat dari konsultasi ke penjualan.

### 5. Keuangan & Laporan
* **Laporan Penjualan:** Harian, bulanan, dan detail per transaksi.
* **Laporan Laba/Rugi:** Analisis profitabilitas.
* **Biaya Operasional:** Pencatatan pengeluaran operasional apotek.
* Ekspor laporan ke format Excel dan PDF.

### 6. Manajemen Pengguna & Cabang
* **Multi-Role:** Mendukung peran Admin, Kasir, dan user lainnya.
* **Multi-Cabang:** Dukungan untuk manajemen data berbasis cabang.
* Autentikasi aman (Login, Register, Verifikasi Email).

## 🛠 Teknologi yang Digunakan

* **Backend:** [Laravel](https://laravel.com) (PHP Framework)
* **Frontend:** [Tailwind CSS](https://tailwindcss.com), Blade Templates, JavaScript
* **Database:** MySQL / MariaDB
* **Build Tools:** Vite, NPM, Composer
* **Library Tambahan:**
    * `dompdf/dompdf`: Untuk mencetak laporan/surat pesanan ke PDF.
    * `maatwebsite/excel`: Untuk ekspor laporan Excel.

## 📋 Prasyarat Instalasi

Sebelum memulai, pastikan komputer Anda telah terinstal:

* PHP >= 8.1
* Composer
* Node.js & NPM
* MySQL Database
* Git

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/username/sistem-apotek.git](https://github.com/username/sistem-apotek.git)
    cd sistem-apotek
    ```

2.  **Install Dependencies (Backend)**
    ```bash
    composer install
    ```

3.  **Install Dependencies (Frontend)**
    ```bash
    npm install
    npm run build
    ```

4.  **Konfigurasi Environment**
    Salin file `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan sesuaikan konfigurasi database Anda:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_apotek
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Migrasi Database & Seeder**
    Jalankan migrasi untuk membuat tabel dan mengisi data awal (user admin, master data dummy, dll):
    ```bash
    php artisan migrate --seed
    ```

7.  **Jalankan Server**
    ```bash
    php artisan serve
    ```
    Akses aplikasi melalui browser di `http://localhost:8000`.

## 📖 Contoh Penggunaan

**Login:**
Gunakan akun yang telah digenerate oleh seeder (cek `database/seeders/UserFactory.php` atau `AdminUserSeeder.php` untuk detail kredensial default, biasanya `admin@example.com` / `password`).

**Alur Transaksi Kasir:**
1.  Login sebagai User dengan role Kasir.
2.  Masuk ke menu **POS**.
3.  Buka shift baru (masukkan modal awal).
4.  Cari obat, masukkan ke keranjang, dan proses pembayaran.
5.  Cetak struk.

## 🤝 Kontribusi

Kontribusi sangat diterima! Jika Anda ingin meningkatkan fitur atau memperbaiki bug:

1.  *Fork* repository ini.
2.  Buat *branch* fitur baru (`git checkout -b fitur-keren`).
3.  *Commit* perubahan Anda (`git commit -m 'Menambahkan fitur keren'`).
4.  *Push* ke branch (`git push origin fitur-keren`).
5.  Buat **Pull Request**.