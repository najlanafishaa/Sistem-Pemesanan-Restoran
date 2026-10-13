# Sistem Pemesanan Restoran — Resto Nusantara (PHP Native & MySQLi)

Aplikasi web **Sistem Pemesanan Restoran** yang dibangun secara sederhana, terstruktur, dan mudah dipahami menggunakan **PHP Native 8.2** dan **MySQLi**, antarmuka **Bootstrap 5**, yang dirancang khusus untuk memenuhi **8 Unit Kompetensi Asesmen SKKNI**:

1. **J.620100.016.01** — Menulis Kode dengan Prinsip Sesuai Guidelines dan Best Practices
2. **J.620100.017.02** — Mengimplementasikan Pemrograman Terstruktur
3. **J.620100.018.02** — Mengimplementasikan Pemrograman Berorientasi Objek (OOP: Inheritance & Polymorphism)
4. **J.620100.019.02** — Menggunakan Library atau Komponen Pre-Existing (Bootstrap 5, Bootstrap Icons, MySQLi)
5. **J.620100.021.02** — Menerapkan Akses Basis Data (Prepared Statements & Database Transactions)
6. **J.620100.023.02** — Membuat Dokumen Kode Program (PHPDoc & Generator Otomatis Reflection)
7. **J.620100.025.02** — Melakukan Debugging (Pencatatan Error & Solusi Perbaikan)
8. **J.620100.033.02** — Melaksanakan Pengujian Unit Program (Automated Unit Testing 20 Kasus)

---

## 1. Teknologi yang Digunakan
* **Bahasa Backend:** PHP 8.2 Native (Murni tanpa framework)
* **Basis Data:** MySQL / MariaDB via MySQLi Extension
* **Antarmuka (Frontend):** HTML5, CSS3 (Forest Green Theme), Bootstrap 5.3, Bootstrap Icons, JavaScript
* **Tools Ekstra:** Automated Unit Tester (`tests/run_tests.php`), Documentation Generator (`docs/generate_docs.php`)

---

## 2. Fitur Utama Sistem

### A. Dashboard Admin
* Statistik ringkasan: Total Meja, Data Pelanggan, Katalog Menu, Total Pemesanan, dan Total Omset Pendapatan.
* Kartu pemantauan status reservasi: Menunggu, Dikonfirmasi, Selesai, dan Dibatalkan.
* Tabel pemesanan terbaru dengan navigasi langsung ke detail.

### B. Manajemen Meja Restoran
* Daftar meja lengkap dengan informasi nomor meja, kapasitas tempat duduk tamu, tata letak/lokasi (Indoor, Outdoor, VIP), dan status meja.
* Tambah, ubah, dan hapus data meja.
* Validasi keunikan nomor meja dan kapasitas minimal.

### C. Manajemen Data Pelanggan
* Daftar seluruh pelanggan dan riwayat nomor telepon / WhatsApp serta email.
* Tambah, ubah, dan hapus data pelanggan.
* Validasi nomor telepon dan format email.

### D. Manajemen Menu Hidangan
* Katalog makanan, minuman, hidangan penutup (dessert), dan paket hemat.
* Filter cepat berdasarkan kategori menu.
* Format harga otomatis dalam mata uang Rupiah (`Rp`).
* Status ketersediaan menu (Tersedia / Habis).

### E. Pemesanan Meja (Reservasi)
* Pemilihan pelanggan dan meja restoran.
* Penentuan tanggal, waktu mulai, dan waktu selesai.
* **Validasi Kapasitas:** Memastikan jumlah tamu tidak melebihi kapasitas meja yang dipilih (disertai peringatan interaktif realtime).
* **Pencegahan Konflik Jadwal:** Mencegah pemesanan meja yang sama pada rentang waktu yang bertabrakan (*schedule collision prevention*).
* Pembuatan kode reservasi otomatis unik (`ORD-YYYYMMDD-XXX`).

### F. Pengelolaan Pesanan Menu (Order Management)
* Menambahkan satu atau beberapa menu makanan/minuman secara dinamis ke dalam reservasi.
* Penentuan kuantitas (jumlah porsi) dan catatan khusus tiap menu (contoh: *pedas sedang*, *kurang es*).
* **Kalkulasi Otomatis Realtime:** Subtotal dan total biaya dihitung langsung via JavaScript di form dan divalidasi ulang secara aman di backend.
* **Database Transactions:** Penyimpanan data pemesanan dan detail menu diproses secara atomik (`beginTransaction`, `commit`, `rollBack`).

### G. Laporan Pemesanan & Cetak Dokumen
* Rekapitulasi pemesanan dengan filter tanggal (dari - sampai) dan filter status.
* Perhitungan otomatis total transaksi, total tamu terlayani, total omset, dan rata-rata per transaksi.
* Halaman **Cetak Laporan Ramah Cetak (Print-Friendly)** berstandar resmi restoran lengkap dengan kop surat dan kolom tanda tangan pengesahan.

---

## 3. Struktur Folder Proyek

```
Sistem Pemesanan Restoran/
├── assets/
│   ├── css/
│   │   └── style.css            # Desain dashboard responsif & print styling
│   ├── js/
│   │   └── app.js               # Kalkulasi dinamis, toggle sidebar, cek kapasitas
│   └── img/                     # Folder aset grafis/logo
├── config/
│   ├── Database.php             # Class koneksi MySQLi (Singleton pattern)
│   └── helpers.php              # Kumpulan helper function (XSS, flash, rupiah, CSRF)
├── controllers/
│   ├── DashboardController.php  # Kontroler ringkasan statistik beranda
│   ├── MejaController.php       # Kontroler CRUD meja restoran
│   ├── PelangganController.php  # Kontroler CRUD pelanggan
│   ├── MenuController.php       # Kontroler CRUD menu makanan/minuman
│   ├── PemesananController.php  # Kontroler logika reservasi & pesanan
│   └── LaporanController.php    # Kontroler laporan transaksi & cetak
├── database/
│   └── database.sql             # Skema DDL tabel dan data awal (seeders)
├── models/
│   ├── Meja.php                 # Model meja restoran
│   ├── Pelanggan.php            # Model pelanggan
│   ├── Menu.php                 # Model hidangan & minuman
│   ├── Pemesanan.php            # Model transaksi reservasi (transaksi & konflik)
│   └── DetailPemesanan.php      # Model rincian item pesanan menu
├── tests/
│   ├── ValidationTest.php       # Pengujian unit validasi pelanggan & menu
│   ├── CalculationTest.php      # Pengujian unit kalkulasi harga & subtotal
│   ├── ReservationRulesTest.php # Pengujian unit kapasitas & tabrakan jadwal
│   └── run_tests.php            # CLI & Web test runner
├── views/
│   ├── layout/
│   │   ├── header.php           # Template header Bootstrap 5 & flash alert
│   │   ├── sidebar.php          # Menu navigasi sidebar
│   │   ├── navbar.php           # Top bar dengan user info
│   │   └── footer.php           # Template footer & script loader
│   ├── dashboard/
│   │   └── index.php            # Tampilan statistik dashboard
│   ├── meja/
│   │   ├── index.php, create.php, edit.php
│   ├── pelanggan/
│   │   ├── index.php, create.php, edit.php
│   ├── menu/
│   │   ├── index.php, create.php, edit.php
│   ├── pemesanan/
│   │   ├── index.php, create.php, edit.php, detail.php
│   └── laporan/
│       ├── index.php, cetak.php
├── index.php                    # Front Controller & Router aplikasi
├── LAPORAN_PENGEMBANGAN.md      # Dokumen laporan pengembangan program (SKKNI)
├── SLIDE_PRESENTASI.md          # Naskah slide materi presentasi demonstrasi
└── README.md                    # Petunjuk instalasi & dokumentasi
```

---

## 4. Panduan Instalasi & Penggunaan

### A. Persiapan Database
1. Pastikan servis **MySQL / MariaDB** sudah aktif di Laragon atau XAMPP.
2. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`) atau **HeidiSQL**.
3. Buat database baru bernama `db_pemesanan_restoran`, atau langsung impor file berikut:
   ```
   database/database.sql
   ```
4. Seluruh tabel (`pelanggan`, `meja`, `menu`, `pemesanan`, `detail_pemesanan`) beserta data demonstrasi awal akan otomatis terbuat.

### B. Konfigurasi Koneksi Database
File konfigurasi berada di `config/Database.php`:
```php
private string $host = '127.0.0.1';
private string $port = '3306';
private string $db_name = 'db_pemesanan_restoran';
private string $username = 'root';
private string $password = '';
```
*(Sesuaikan username dan password jika instalasi MySQL Anda menggunakan password khusus).*

### C. Menjalankan Aplikasi

#### Opsi 1: Menggunakan PHP Built-in Web Server (Paling Cepat)
Buka terminal / PowerShell di folder proyek ini (`d:\Sistem Pemesanan Restoran`), lalu ketik:
```bash
php -S localhost:8000
```
Buka browser dan akses alamat:
```
http://localhost:8000
```

#### Opsi 2: Menggunakan Laragon
1. Tempatkan atau buat tautan symlink folder proyek ini ke `C:\laragon\www\sistem-pemesanan-restoran`
2. Buka browser dan akses:
   ```
   http://localhost/sistem-pemesanan-restoran/
   ```
   atau `http://sistem-pemesanan-restoran.test/`

#### Opsi 3: Menggunakan XAMPP
1. Salin folder ke `C:\xampp\htdocs\sistem-pemesanan-restoran`
2. Akses alamat:
   ```
   http://localhost/sistem-pemesanan-restoran/
   ```

---

## 5. Menjalankan Pengujian Unit (Unit Testing)

Sesuai tuntutan unit kompetensi **J.620100.033.02**, sistem telah dilengkapi pengujian otomatis untuk menguji kebenaran algoritma validasi, kalkulasi subtotal harga, aturan kapasitas meja, dan pencegahan tabrakan waktu reservasi.

Jalankan perintah berikut di terminal:
```bash
php tests/run_tests.php
```

Contoh output hasil pengujian:
```
=======================================================
  PENGUJIAN UNIT PROGRAM - SISTEM PEMESANAN RESTORAN   
=======================================================

[PASS] ValidationTest: Pelanggan Valid
[PASS] ValidationTest: Nama Pelanggan Kosong
[PASS] ValidationTest: No Telepon Format Salah
[PASS] ValidationTest: Email Format Salah
[PASS] ValidationTest: Menu Valid
[PASS] ValidationTest: Harga Menu Negatif
[PASS] ValidationTest: Kategori Menu Tidak Valid
[PASS] CalculationTest: Subtotal Normal
[PASS] CalculationTest: Subtotal Qty Nol
[PASS] CalculationTest: Subtotal Harga Negatif
[PASS] CalculationTest: Format Rupiah Standar
[PASS] CalculationTest: Format Rupiah Desimal
[PASS] ReservationTest: Kapasitas Pas
[PASS] ReservationTest: Kapasitas Kurang dari Max
[PASS] ReservationTest: Kapasitas Melebihi Batas
[PASS] ReservationTest: Tamu Nol
[PASS] ReservationTest: Pencegahan Konflik Jadwal

-------------------------------------------------------
Hasil Akhir: Total: 17 | Lolos: 17 | Gagal: 0
=======================================================
```

---

## 6. Standar Keamanan Sistem

1. **SQL Injection Protection:** Menggunakan MySQLi Prepared Statements dengan parameter binding pada seluruh query SQL (`?`).
2. **Cross-Site Scripting (XSS) Prevention:** Output HTML di-escape menggunakan fungsi pembantu `e()` atau `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
3. **Cross-Site Request Forgery (CSRF) Protection:** Seluruh form tindakan `POST` dilengkapi token rahasia sesi (`csrfToken()`) yang diverifikasi pada saat request diproses.
4. **Data Integrity:** Menerapkan foreign key constraint (`ON DELETE RESTRICT` dan `ON DELETE CASCADE`) untuk menjaga konsistensi relasi data.
