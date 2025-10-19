````markdown
# 🏥 Panduan Setup Awal Sistem Apotek

## Prasyarat
- PHP 8.1+
- MySQL/MariaDB
- Composer

## Instalasi

### 1. Clone & Install Dependencies
```bash
git clone [repo-url]
cd apotek-system
composer install --no-dev
````

### 2\. Setup Database

```bash
# Copy .env
cp .env.example .env

# Edit .env, pastikan:
APP_ENV=production
APP_DEBUG=false
DB_DATABASE=apotek_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Generate key
php artisan key:generate
```

### 3\. Migrasi Database (Setup Awal)

Perintah ini akan **menghapus semua data lama** dan mengisi dengan data minimal yang dibutuhkan (admin & struktur dasar).

```bash
php artisan migrate:fresh --seed --force
```

**Output yang muncul akan seperti ini:**

```
...
✅ Cabang Pusat created
✅ Default Shifts created (Pagi, Sore)
✅ Admin user created
📧 Email: admin@apotek.local
🔑 Password: admin123
⚠️  WAJIB GANTI PASSWORD SETELAH LOGIN PERTAMA!
...
```

### 4\. Login Pertama Kali

Gunakan kredensial default untuk login ke sistem:

  * **URL**: `http://your-domain.com/login/admin`
  * **Email**: `admin@apotek.local`
  * **Password**: `admin123`

### 5\. ⚠️ WAJIB: Ganti Password

Segera setelah login pertama kali:

1.  Klik **Profile** (biasanya di pojok kanan atas).
2.  Ganti password dengan yang kuat dan aman.
3.  Logout dan login kembali menggunakan password baru Anda.

### 6\. Setup Data Apotek

Setelah login, Anda perlu mengisi data master yang benar:

| Data Master | Menu | Aksi |
| :--- | :--- | :--- |
| **Cabang (Apotek)** | `Master Data > Cabang` | Edit cabang "Apotek Pusat", isi alamat dan telepon yang benar. |
| **User Kasir** | `Management User > Tambah User` | Tambah user untuk kasir. Pastikan **Role**-nya adalah **Kasir**. |
| **Supplier** | `Master Data > Supplier` | Input data supplier-supplier Anda. |
| **Obat** | `Master Data > Obat` | Input daftar obat dan harga dasarnya. |
| **Pelanggan Tetap** | `Master Data > Pelanggan` | (Opsional) Input data pelanggan tetap. |

### 7\. Mulai Transaksi

Setelah semua data master terisi:

1.  Kasir login di halaman khusus `/login/kasir`.
2.  Mulai shift (input modal awal).
3.  Transaksi siap digunakan\!

-----

## Troubleshooting

### Tidak Bisa Login

  * Pastikan Anda sudah menjalankan perintah `php artisan migrate:fresh --seed --force`.
  * Cek di database (tabel `users`) harus ada 1 *row* dengan email `admin@apotek.local`.

### Lupa Password Admin

Jika lupa password, Anda bisa meresetnya lewat *command line* dengan **Tinker**:

```bash
php artisan tinker

# Di dalam Tinker, jalankan 3 baris ini:
>>> $admin = App\Models\User::where('email', 'admin@apotek.local')->first();
>>> $admin->password = Hash::make('newpassword123'); # Ganti dengan password baru Anda
>>> $admin->save();
```

## Support

Hubungi: `[support@apotek.com]`

```
```