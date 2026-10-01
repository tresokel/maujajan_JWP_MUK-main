# 🍽️ MauJajan - Sistem Pemesanan Makanan & Manajemen Menu

Aplikasi sistem pemesanan makanan dan manajemen menu (CRUD) berbasis **Laravel 11** dan **Tailwind CSS**. Dibuat untuk kebutuhan Uji Kompetensi / Junior Web Programmer (JWP).

---

## 📋 Daftar Isi
- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
- [Akun Login Admin](#-akun-login-admin)
- [Alur Fitur & CRUD](#-alur-fitur--crud)
- [Troubleshooting](#-troubleshooting)

---

## ✨ Fitur Utama

### 👨‍🍳 Sisi Pelanggan (Customer)
* **Katalog Menu Interaktif**: Menampilkan daftar makanan & minuman beserta foto, deskripsi, dan harga tanpa perlu login.
* **Filter Kategori Cepat**: Memfilter menu (*Semua, Makanan, Minuman, Cemilan*) secara langsung tanpa reload halaman.
* **Pemesanan Mudah**: Mengisi Nama Pemesan dan Nomor Meja, serta menentukan jumlah porsi dengan tombol stepper (`+` / `-`).
* **Popup Konfirmasi & Ringkasan Biaya**: Rincian total pesanan dan harga dihitung otomatis sebelum dikirim ke database.

### 🛡️ Sisi Admin (Dashboard)
* **Manajemen Menu Makanan (Full CRUD)**:
  * **Create**: Tambah menu makanan baru beserta upload file gambar/foto.
  * **Read**: Melihat daftar menu makanan, kategori, dan harga.
  * **Update**: Mengedit data menu dan memperbarui foto makanan.
  * **Delete**: Menghapus menu makanan dari sistem (foto lama otomatis terhapus dari server).
* **Monitoring & Kelola Status Pesanan**:
  * Melihat rekapitulasi pesanan masuk secara real-time, rincian item, dan total bayar.
  * Mengubah status pesanan (*Pending* ➔ *Diproses* ➔ *Selesai / Lunas* ➔ *Dibatalkan*).

---

## 🛠️ Teknologi yang Digunakan

* **Backend**: Laravel 11 (PHP 8.2+)
* **Frontend**: Blade Templating, Tailwind CSS, Vite
* **Database**: MySQL / MariaDB
* **Autentikasi**: Laravel Breeze

---

## ⚡ Panduan Instalasi & Menjalankan

Jalankan perintah berikut di terminal (PowerShell atau Git Bash) secara berurutan:

### 1. Masuk ke Direktori Proyek
```bash
cd maujajan_JWP_MUK-main
