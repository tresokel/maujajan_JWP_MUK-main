# 🍽️ MauJajan - Panduan Cepat

Sistem pemesanan makanan dan manajemen menu (CRUD) berbasis **Laravel 11**.

---

## ⚡ Cara Menjalankan Aplikasi

Jalankan perintah berikut di terminal secara berurutan:

```bash
# 1. Install dependensi
composer install
npm install

# 2. Siapkan file environment & key
cp .env.example .env
php artisan key:generate

# 3. Migrasi database & isi data awal (seeder)
php artisan migrate --seed

# 4. Buat tautan storage (agar gambar menu muncul)
php artisan storage:link

# 5. Jalankan server (buka 2 terminal)
php artisan serve    # Terminal 1 -> http://localhost:8000
npm run dev          # Terminal 2 -> Asset compiler
```

---

## 🔐 Akun Login Admin

- **URL Login**: `http://localhost:8000/login`
- **Email**: `admin@gmail.com`
- **Password**: `password123`

---

## 📌 Alur Fitur & CRUD

### 1. Sisi Pelanggan (`http://localhost:8000/`)
* **[Read] Katalog Menu**: Pelanggan dapat melihat daftar menu tanpa login.
* **[Create] Pesan Makanan**: Isi Nama & Nomor Meja, tentukan jumlah porsi menu, lalu klik **Checkout/Pesan**.

---

### 2. Sisi Admin (`http://localhost:8000/login`)
Login menggunakan akun admin untuk mengelola:

* **Pesanan Masuk (`/admin/dashboard`)**:
  * **[Read]**: Melihat daftar pesanan pelanggan, nomor meja, dan total harga.
  * **[Update]**: Mengubah status pesanan (*Pending* -> *Diproses* -> *Selesai* / *Dibatalkan*).

* **Menu Makanan (`/admin/foods`)**:
  * **[Read]**: Melihat tabel seluruh menu makanan, kategori, harga, dan gambar.
  * **[Create]**: Tambah menu baru beserta upload foto menu.
  * **[Update]**: Edit data menu & ganti foto makanan.
  * **[Delete]**: Hapus menu (file gambar otomatis terhapus dari server).

---

## 💡 Catatan
Jika gambar menu tidak tampil di browser, pastikan sudah menjalankan:
```bash
php artisan storage:link
```
