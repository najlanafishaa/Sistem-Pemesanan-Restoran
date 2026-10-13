# DOKUMEN LAPORAN PENGEMBANGAN PROGRAM
## SISTEM INFORMASI PEMESANAN RESTORAN BERBASIS PHP NATIVE & MYSQLI

**Skema Sertifikasi:** Junior Web Developer / Pemrograman Web  
**Standar Kompetensi Kerja Nasional Indonesia (SKKNI)**  

---

### DAFTAR UNIT KOMPETENSI YANG DITERAPKAN

| No | Kode Unit | Judul Unit Kompetensi |
|:---|:---|:---|
| 1 | **J.620100.016.01** | Menulis Kode dengan Prinsip Sesuai Guidelines dan Best Practices |
| 2 | **J.620100.017.02** | Mengimplementasikan Pemrograman Terstruktur |
| 3 | **J.620100.018.02** | Mengimplementasikan Pemrograman Berorientasi Objek |
| 4 | **J.620100.019.02** | Menggunakan Library atau Komponen Pre-Existing |
| 5 | **J.620100.021.02** | Menerapkan Akses Basis Data |
| 6 | **J.620100.023.02** | Membuat Dokumen Kode Program |
| 7 | **J.620100.025.02** | Melakukan Debugging |
| 8 | **J.620100.033.02** | Melaksanakan Pengujian Unit Program |

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang
Operasional restoran modern membutuhkan pencatatan reservasi meja dan pemesanan hidangan yang cepat, akurat, dan transparan. Pengelolaan manual seringkali menimbulkan kendala seperti terjadinya tabrakan waktu reservasi meja (*double booking*), ketidaksesuaian jumlah tamu dengan kapasitas kursi yang tersedia, keterlambatan pencatatan pesanan menu, serta kesulitan dalam merekap omset harian.

Oleh karena itu, dikembangkan **Sistem Pemesanan Restoran** berbasis web menggunakan PHP Native dengan pendekatan Object-Oriented Programming (OOP) dan MySQLi. Aplikasi ini dirancang tanpa ketergantungan pada framework PHP guna mendemonstrasikan penguasaan algoritma dasar, arsitektur modular, keamanan web, dan integrasi basis data relasional.

### 1.2 Tujuan Pengembangan
1. Membangun sistem manajemen meja, data pelanggan, dan katalog menu restoran.
2. Mengotomatiskan proses reservasi meja dengan validasi kapasitas dan deteksi tabrakan jadwal (*schedule collision prevention*).
3. Mengelola pemesanan menu hidangan dengan kalkulasi subtotal dan grand total secara otomatis.
4. Menyediakan modul pelaporan transaksi dan fitur cetak dokumen fisik/PDF (*print-friendly*).
5. Menerapkan best practice rekayasa perangkat lunak sesuai unit kompetensi SKKNI.

---

## BAB II: ANALISIS KEBUTUHAN SISTEM

### 2.1 Kebutuhan Fungsional (Functional Requirements)
1. **F-01 (Dashboard):** Menampilkan ringkasan metrik statistik operasional (jumlah meja, pelanggan, menu, total reservasi, dan pendapatan omset) serta daftar pemesanan terbaru.
2. **F-02 (Manajemen Meja):** Menyimpan, menampilkan, mengubah, dan menghapus data meja dengan atribut nomor meja, kapasitas orang, status ketersediaan, dan lokasi.
3. **F-03 (Manajemen Pelanggan):** Mengelola data pelanggan (nama, nomor telepon, email, alamat) dengan validasi format kontak.
4. **F-04 (Manajemen Menu):** Mengelola hidangan makanan, minuman, dessert, dan paket hemat lengkap dengan harga berformat Rupiah dan status stok.
5. **F-05 (Pemesanan Meja):** Melakukan reservasi meja dengan pengecekan kapasitas meja dan pencegahan jadwal tumpang tindih.
6. **F-06 (Pengelolaan Pesanan Menu):** Menambahkan satu atau beberapa menu pilihan ke dalam transaksi pemesanan secara dinamis dan menghitung total harga dalam transaksi database atomik.
7. **F-07 (Laporan & Cetak):** Menampilkan laporan transaksi terfilter berdasarkan rentang tanggal dan status, serta menyediakan halaman cetak laporan berstandar nota resmi.

### 2.2 Kebutuhan Non-Fungsional (Non-Functional Requirements)
1. **Keamanan (Security):**
   * Pencegahan SQL Injection melalui *MySQLi Prepared Statements*.
   * Pencegahan Cross-Site Scripting (XSS) melalui sanitasi output HTML `htmlspecialchars()`.
   * Pencegahan Cross-Site Request Forgery (CSRF) melalui token rahasia sesi pada form POST.
2. **Keandalan Data (Data Integrity):** Pemanfaatan *Database Transactions* (`begin_transaction`, `commit`, `rollback`) pada penyimpanan transaksi pemesanan dan detail menu.
3. **Usability & Responsiveness:** Tampilan dashboard modern menggunakan Bootstrap 5 yang kompatibel pada desktop maupun perangkat bergerak (mobile).

---

## BAB III: PERANCANGAN SISTEM

### 3.1 Arsitektur Aplikasi
Aplikasi dirancang menggunakan pemisahan tanggung jawab berbasis pola modular **MVC (Model-View-Controller)** sederhana pada PHP Native:
* **Config:** Pengaturan koneksi basis data (`Database.php`) dan pustaka fungsi pembantu (`helpers.php`).
* **Models:** Entitas bisnis yang berinteraksi langsung dengan tabel database (`Meja`, `Pelanggan`, `Menu`, `Pemesanan`, `DetailPemesanan`).
* **Controllers:** Logika kontrol alur bisnis dan validasi input (`DashboardController`, `MejaController`, `PelangganController`, `MenuController`, `PemesananController`, `LaporanController`).
* **Views:** Lapisan antarmuka pengguna (HTML5, Bootstrap 5) yang dipisahkan ke dalam folder modul dan layout bersama (`header`, `sidebar`, `navbar`, `footer`).
* **Router (`index.php`):** Titik masuk tunggal (*Front Controller*) yang mengarahkan parameter request `?page=...&action=...`.

### 3.2 Perancangan Basis Data (Entity Relationship)
Nama Database: `db_pemesanan_restoran`

1. **Tabel `pelanggan`**
   * `id_pelanggan` (INT, PK, AUTO_INCREMENT)
   * `nama_pelanggan` (VARCHAR(100), NOT NULL)
   * `no_telepon` (VARCHAR(20), NOT NULL)
   * `email` (VARCHAR(100), NULL)
   * `alamat` (VARCHAR(255), NULL)
   * `created_at` (TIMESTAMP)

2. **Tabel `meja`**
   * `id_meja` (INT, PK, AUTO_INCREMENT)
   * `nomor_meja` (VARCHAR(20), UNIQUE, NOT NULL)
   * `kapasitas` (INT, NOT NULL)
   * `status_meja` (ENUM: 'Tersedia', 'Terisi', 'Reservasi', 'Perbaikan')
   * `lokasi` (VARCHAR(50))
   * `created_at` (TIMESTAMP)

3. **Tabel `menu`**
   * `id_menu` (INT, PK, AUTO_INCREMENT)
   * `nama_menu` (VARCHAR(100), NOT NULL)
   * `kategori` (ENUM: 'Makanan', 'Minuman', 'Dessert', 'Paket')
   * `harga` (DECIMAL(12,2), NOT NULL)
   * `status_menu` (ENUM: 'Tersedia', 'Habis')
   * `deskripsi` (TEXT)
   * `created_at` (TIMESTAMP)

4. **Tabel `pemesanan`**
   * `id_pemesanan` (INT, PK, AUTO_INCREMENT)
   * `kode_pemesanan` (VARCHAR(25), UNIQUE, NOT NULL)
   * `id_pelanggan` (INT, FK -> `pelanggan.id_pelanggan`)
   * `id_meja` (INT, FK -> `meja.id_meja`)
   * `tanggal_pemesanan` (DATE, NOT NULL)
   * `waktu_mulai` (TIME, NOT NULL)
   * `waktu_selesai` (TIME, NOT NULL)
   * `jumlah_orang` (INT, NOT NULL)
   * `status_pemesanan` (ENUM: 'Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan')
   * `total_harga` (DECIMAL(14,2), DEFAULT 0.00)
   * `catatan` (TEXT)
   * `created_at`, `updated_at` (TIMESTAMP)

5. **Tabel `detail_pemesanan`**
   * `id_detail` (INT, PK, AUTO_INCREMENT)
   * `id_pemesanan` (INT, FK -> `pemesanan.id_pemesanan`, ON DELETE CASCADE)
   * `id_menu` (INT, FK -> `menu.id_menu`)
   * `jumlah` (INT, NOT NULL)
   * `harga_satuan` (DECIMAL(12,2), NOT NULL)
   * `subtotal` (DECIMAL(14,2), NOT NULL)
   * `catatan_khusus` (VARCHAR(255))

---

## BAB IV: IMPLEMENTASI SISTEM

### 4.1 Pemrograman Terstruktur & Berorientasi Objek
* **Struktur Kelas & Enkapsulasi:** Seluruh operasi data dibungkus dalam class model dengan atribut private (`$db`, `$table`) dan method publik yang terdefinisi secara ketat.
* **Singleton Database Pattern:** Koneksi basis data dikelola melalui method statis `Database::getConnection()` untuk mencegah pemborosan koneksi soket TCP/IP.
* **Helper Functions:** Fungsi sanitasi data, konversi mata uang Rupiah, dan penanganan flash message diimplementasikan secara terstruktur dan modular pada `config/helpers.php`.

### 4.2 Logika Pencegahan Konflik Reservasi
Sistem menerapkan algoritma deteksi interval tumpang tindih waktu pada meja yang sama:
$$\text{Konflik Terjadi jika: } (WaktuMulai_{baru} < WaktuSelesai_{lama}) \land (WaktuSelesai_{baru} > WaktuMulai_{lama})$$
Query SQL prepared statements:
```sql
SELECT COUNT(*) FROM pemesanan 
WHERE id_meja = :id_meja 
  AND tanggal_pemesanan = :tanggal 
  AND status_pemesanan != 'Dibatalkan'
  AND (:waktu_mulai < waktu_selesai AND :waktu_selesai > waktu_mulai)
```

---

## BAB V: PENGUJIAN DAN DEBUGGING

### 5.1 Pengujian Unit Program (Unit Testing)
Sesuai standar unit kompetensi **J.620100.033.02**, telah diimplementasikan automated test runner pada direktori `tests/` yang dapat dijalankan melalui CLI:
```bash
php tests/run_tests.php
```

#### Hasil Pengujian Unit:
| Kasus Uji | Modul Pengujian | Ekspektasi | Hasil Aktual | Status |
|:---|:---|:---|:---|:---:|
| TC-01 | `ValidationTest` | Data pelanggan valid lolos tanpa error | Array error kosong | **PASS** |
| TC-02 | `ValidationTest` | Nama pelanggan kosong ditolak | Error terdeteksi | **PASS** |
| TC-03 | `ValidationTest` | Format nomor telepon salah ditolak | Error terdeteksi | **PASS** |
| TC-04 | `ValidationTest` | Format email salah ditolak | Error terdeteksi | **PASS** |
| TC-05 | `ValidationTest` | Data menu valid diterima | Array error kosong | **PASS** |
| TC-06 | `ValidationTest` | Harga menu negatif ditolak | Error terdeteksi | **PASS** |
| TC-07 | `ValidationTest` | Kategori menu invalid ditolak | Error terdeteksi | **PASS** |
| TC-08 | `CalculationTest` | Subtotal 3 porsi @ Rp 35.000 = Rp 105.000 | Tepat Rp 105.000 | **PASS** |
| TC-09 | `CalculationTest` | Subtotal kuantitas 0 menghasilkan 0 | Nilai 0.00 | **PASS** |
| TC-10 | `CalculationTest` | Subtotal harga negatif menghasilkan 0 | Nilai 0.00 | **PASS** |
| TC-11 | `CalculationTest` | Format Rupiah tanpa desimal (`Rp 75.000`) | Sesuai | **PASS** |
| TC-12 | `CalculationTest` | Format Rupiah dengan desimal (`Rp 75.000,00`) | Sesuai | **PASS** |
| TC-13 | `ReservationRulesTest` | Jumlah tamu 4 orang pada kapasitas 4 lolos | Valid (true) | **PASS** |
| TC-14 | `ReservationRulesTest` | Jumlah tamu 2 orang pada kapasitas 6 lolos | Valid (true) | **PASS** |
| TC-15 | `ReservationRulesTest` | Jumlah tamu 7 orang pada kapasitas 6 ditolak | Tidak Valid (false) | **PASS** |
| TC-16 | `ReservationRulesTest` | Jumlah tamu 0 orang ditolak | Tidak Valid (false) | **PASS** |
| TC-17 | `ReservationRulesTest` | Pengecekan interval jadwal tumpang tindih | Konflik terdeteksi | **PASS** |

**Kesimpulan Pengujian:** 17 dari 17 unit test berhasil (0 kegagalan).

---

## BAB VI: KESIMPULAN

Aplikasi **Sistem Pemesanan Restoran** telah berhasil dibangun sesuai seluruh instruksi kerja dan kriteria unjuk kerja tugas praktik demonstrasi:
1. Menggunakan **PHP Native** tanpa framework PHP dengan menerapkan pemrograman terstruktur dan OOP.
2. Menggunakan **MySQLi** dan *prepared statements* untuk seluruh operasi basis data MySQL guna menjamin performa dan pencegahan serangan SQL Injection.
3. Mengakomodasi kebutuhan operasional pemesanan meja kasir langsung, validasi kapasitas tempat duduk, pencegahan tabrakan pesanan aktif, dan pengelolaan pesanan hidangan secara atomik melalui transaksi basis data.
4. Memiliki antarmuka admin profesional berbasis Bootstrap 5 dengan modul pelaporan transaksi dan fitur cetak dokumen fisik yang rapi.
5. Memiliki bukti dokumentasi kode program, prosedur debugging, dan pengujian unit otomatis (*unit testing*) yang siap dipresentasikan pada asesmen praktik kompetensi.
