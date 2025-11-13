# Sistem Apotek (sistem-apotek)

Sistem Apotek adalah aplikasi web berbasis Laravel yang dirancang untuk mengelola operasi apotek secara menyeluruh, mulai dari manajemen inventaris, penjualan di kasir (POS), hingga pelaporan.

## Fitur Utama

Project ini dibagi menjadi dua bagian utama: **Admin Dashboard** dan **Kasir (POS)**.

### 1. Admin
* **📈 Dashboard**: Ringkasan penjualan harian, stok menipis, stok habis, dan obat yang akan kadaluarsa.
* **📦 Manajemen Master Data**:
    * **Obat**: CRUD data obat, manajemen stok, harga dasar, harga jual, margin, dan data supplier.
    * **Supplier**: CRUD data supplier (PBF).
    * **Pelanggan**: CRUD data pelanggan tetap.
* **🛒 Manajemen Transaksi**:
    * **Pembelian**: Membuat Surat Pesanan (SP) ke supplier, memproses SP menjadi faktur pembelian, dan finalisasi faktur untuk menambah stok.
    * **Retur**: Mengelola retur barang (baik retur pembelian ke supplier maupun retur penjualan dari pelanggan).
    * **Biaya Operasional**: Mencatat pengeluaran operasional (gaji, listrik, dll).
* **📊 Laporan**:
    * Laporan penjualan harian dan bulanan.
    * Analisis profit (Laba Kotor & Laba Bersih).
    * Analisis pergerakan stok (Fast/Slow Moving).
    * Analisis pelanggan.
* **👥 Manajemen User**: Mengelola akun untuk Admin dan Kasir.

### 2. Kasir (POS)
* **🔒 Manajemen Shift**: Kasir harus memulai shift dengan memasukkan modal awal sebelum dapat mengakses POS.
* **💰 Point of Sale (POS)**:
    * Antarmuka kasir untuk transaksi penjualan.
    * Pencarian obat (autocomplete dan list).
    * Manajemen keranjang (tambah, update qty, hapus).
    * Pencarian dan penambahan pelanggan tetap saat transaksi.
    * Input diskon (persen atau nominal).
* **🧾 Cetak Struk**: Opsi untuk mencetak faktur (A4) atau struk thermal (58mm) setelah transaksi berhasil.
* **🕒 Riwayat Transaksi**: Kasir dapat melihat riwayat transaksi yang mereka lakukan pada hari itu.

## Teknologi yang Digunakan

* **Backend**: PHP 8.2+, Laravel 12
* **Frontend**: Vite, Tailwind CSS, Alpine.js
* **Database**: MySQL / MariaDB
* **Autentikasi**: Laravel Breeze
* **Lainnya**:
    * `barryvdh/laravel-dompdf` untuk generasi PDF (Struk, Faktur, Laporan).
    * `spatie/simple-excel` (terdapat di `composer.json`, kemungkinan untuk ekspor Excel).

## Prasyarat Instalasi

Sebelum memulai, pastikan Anda memiliki perangkat lunak berikut:
* PHP >= 8.2
* Composer
* Node.js & NPM
* Database Server (MySQL/MariaDB direkomendasikan)
* Web Server (Nginx/Apache)

## Susunan Project

Project ini mengikuti struktur standar Laravel:
sistem-apotek/ ├── app/ │ ├── Http/Controllers/ (Logika bisnis utama: POS, Obat, Pembelian, dll) │ ├── Models/ (Model Eloquent: Obat, Penjualan, User, dll) │ ├── Providers/ │ └── Services/ (Logika bisnis terpisah: Cart, Checkout, Laporan) ├── bootstrap/ ├── config/ (File konfigurasi, termasuk apotek.php) ├── database/ │ ├── factories/ │ ├── migrations/ (Struktur database) │ └── seeders/ (Data awal, termasuk akun admin) ├── public/ (Aset publik) ├── resources/ │ ├── css/ │ ├── js/ │ └── views/ (Semua file Blade) │ ├── admin/ (View untuk Admin) │ ├── kasir/ (View untuk Kasir/POS) │ └── layouts/ (Layout utama admin & kasir) ├── routes/ │ ├── web.php (Rute utama aplikasi) │ └── auth.php (Rute autentikasi dari Breeze) ├── storage/ ├── tests/ ├── .env.example ├── composer.json (Dependensi PHP) └── package.json (Dependensi Node.js)

## Contoh Penggunaan (Instalasi)

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/eruuuxz/sistem-apotek.git](https://github.com/eruuuxz/sistem-apotek.git)
    cd sistem-apotek
    ```

2.  **Install Dependensi**
    ```bash
    composer install
    npm install
    ```

3.  **Setup Lingkungan (.env)**
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan sesuaikan konfigurasi database (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

4.  **Generate Key & Migrasi**
    ```bash
    php artisan key:generate
    php artisan migrate:fresh --seed
    ```
    Perintah `--seed` akan membuat akun admin default.

5.  **Build Aset Frontend**
    ```bash
    npm run build
    ```

6.  **Jalankan Server**
    ```bash
    php artisan serve
    ```

7.  **Akses Aplikasi**
    * **Login Admin**:
        * URL: `http://localhost:8000/login/admin`
        * Email: `admin@apotek.local`
        * Password: `admin123`
    * **Login Kasir**:
        * URL: `http://localhost:8000/login/kasir`
        * (Buat akun kasir baru melalui dashboard Admin > Manajemen User)

## Kontribusi

Kontribusi, isu, dan permintaan fitur sangat diharapkan. Jangan ragu untuk membuka *issue* atau *pull request*.

1.  Fork project ini.
2.  Buat branch fitur Anda (`git checkout -b fitur/fitur-keren`).
3.  Commit perubahan Anda (`git commit -m 'Menambahkan fitur keren'`).
4.  Push ke branch (`git push origin fitur/fitur-keren`).
5.  Buka Pull Request.

## Lisensi

Project ini dilisensikan di bawah **Lisensi MIT**.

---

Copyright (c) 2024

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
