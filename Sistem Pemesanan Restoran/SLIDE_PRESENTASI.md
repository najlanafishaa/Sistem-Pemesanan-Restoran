# MATERI SLIDE PRESENTASI DEMONSTRASI PRAKTIK
## SISTEM PEMESANAN RESTORAN BERBASIS PHP NATIVE & MYSQLI

*Panduan Naskah Presentasi & Konten Slide untuk Uji Kompetensi Pemrograman (BNSP)*

---

### SLIDE 1: JUDUL & IDENTITAS
* **Judul:** Demonstrasi Praktik: Sistem Pemesanan Restoran
* **Sub-judul:** Pengembangan Aplikasi Web Berbasis PHP Native, OOP, dan Akses Basis Data MySQLi
* **Presenter:** Peserta Uji Kompetensi
* **Skema:** Junior Web Developer / Pemrograman Web
* **Poin Bicara (Talking Points):**
  > "Selamat pagi/siang Bapak/Ibu Asesor. Pada kesempatan kali ini, saya akan mendemonstrasikan hasil rancang bangun aplikasi Sistem Pemesanan Restoran yang dikembangkan menggunakan PHP Native tanpa framework, dengan menerapkan prinsip pemrograman terstruktur, Object-Oriented Programming, dan akses basis data MySQLi."

---

### SLIDE 2: LATAR BELAKANG & TUJUAN
* **Latar Belakang Permasalahan:**
  * Pengelolaan pemesanan meja restoran secara konvensional rentan terjadi tabrakan jadwal (*double booking*).
  * Kapasitas meja sering tidak sesuai dengan jumlah tamu yang hadir.
  * Pencatatan pesanan hidangan dan rekapitulasi omset harian membutuhkan waktu lama.
* **Tujuan Pengembangan:**
  1. Mengelola data meja, pelanggan, dan katalog menu hidangan secara terpusat.
  2. Mencegah tabrakan waktu reservasi meja pada rentang waktu yang sama.
  3. Memastikan jumlah tamu tidak melebihi kapasitas meja.
  4. Mengotomatiskan kalkulasi harga pesanan dan menyediakan laporan transaksi siap cetak.

---

### SLIDE 3: TEKNOLOGI & ARSITEKTUR APLIKASI
* **Stack Teknologi:**
  * **Backend:** PHP 8.2 Native (Struktur MVC Sederhana & OOP).
  * **Database:** MySQL / MariaDB dengan koneksi MySQLi & Database Transactions.
  * **Frontend:** HTML5, CSS3 kustom, Bootstrap 5.3, JavaScript murni (ES6).
  * **Lingkungan Kerja:** Laragon / XAMPP / PHP Built-in Server.
* **Arsitektur Modular:**
  * `config/`: Singleton MySQLi Database Connection & Helper Functions.
  * `models/`: Enkapsulasi query dan logika entitas data.
  * `controllers/`: Logika pemrosesan bisnis dan validasi request.
  * `views/`: Template antarmuka pengguna responsif.
  * `tests/`: Modul automated unit testing.

---

### SLIDE 4: PERANCANGAN BASIS DATA (ERD)
* **Database:** `db_pemesanan_restoran`
* **5 Tabel Utama & Relasi:**
  1. `pelanggan`: Menyimpan identitas tamu (Nama, Telepon, Email, Alamat).
  2. `meja`: Menyimpan kapasitas kursi, lokasi area, dan status meja.
  3. `menu`: Menyimpan nama menu, kategori (Makanan/Minuman/Dessert/Paket), harga, dan status stok.
  4. `pemesanan`: Menyimpan kode reservasi unik, relasi pelanggan & meja, jadwal reservasi, jumlah tamu, dan status.
  5. `detail_pemesanan`: Menyimpan rincian item hidangan, jumlah, harga satuan, dan catatan khusus.
* **Integritas Data:** Menerapkan Foreign Key Constraint (`ON DELETE RESTRICT` dan `ON DELETE CASCADE`) serta Prepared Statements untuk seluruh operasi query.

---

### SLIDE 5: FITUR OPERASIONAL: MEJA, PELANGGAN & MENU
* **Manajemen Meja:**
  * Validasi keunikan nomor meja dan kapasitas minimal kursi.
  * Visualisasi lokasi meja (Indoor, Outdoor, VIP Room).
* **Manajemen Pelanggan:**
  * Validasi regex untuk nomor telepon dan format email standar.
* **Manajemen Menu:**
  * Filter kategori menu hidangan secara interaktif.
  * Format mata uang Rupiah otomatis (`formatRupiah`).

---

### SLIDE 6: LOGIKA INTI (CORE LOGIC): RESERVASI & ANTI-TABRAKAN
* **Validasi Kapasitas Meja:**
  * Pengecekan real-time di form input dan validasi di backend:
    $$\text{Tamu} \le \text{Kapasitas Meja}$$
* **Pencegahan Konflik Jadwal (Conflict Prevention):**
  * Memastikan meja yang sama tidak dapat dipesan pada rentang jam yang beririsan:
    $$\text{Konflik} = (WaktuMulai_{baru} < WaktuSelesai_{lama}) \land (WaktuSelesai_{baru} > WaktuMulai_{lama})$$
  * Otomatis mengabaikan pemesanan yang berstatus 'Dibatalkan'.
* **Kode Pemesanan Unik:** Otomatis menghasilkan format `ORD-YYYYMMDD-XXX`.

---

### SLIDE 7: PENGELOLAAN PESANAN & TRANSAKSI ATOMIK
* **Dynamic Menu Builder (Frontend):**
  * Pengguna dapat menambah atau menghapus baris menu pilihan secara dinamis tanpa reload halaman.
  * Subtotal per item dan total harga terhitung secara real-time via JavaScript.
* **Database Transactions (Backend):**
  * Penyimpanan data pemesanan dan tabel `detail_pemesanan` dibungkus dalam blok atomik:
    ```php
    $db->begin_transaction();
    // 1. Kunci Baris Meja (SELECT FOR UPDATE)
    // 2. Simpan Pemesanan Header
    // 3. Simpan Detail Items Menu & Hitung Subtotal
    // 4. Update Total Pembayaran (Subtotal + Layanan 5% + Pajak 10%)
    $db->commit();
    ```
  * Menjamin tidak ada data menggantung jika terjadi kegagalan sistem (*Rollback on error*).

---

### SLIDE 8: LAPORAN PEMESANAN & FITUR CETAK
* **Rekapitulasi Laporan:**
  * Filter laporan fleksibel berdasarkan rentang tanggal (`dari` - `sampai`) dan status pemesanan.
  * Ringkasan otomatis: Total Pemesanan, Total Tamu, Total Omset Pendapatan, dan Rata-rata per Transaksi.
* **Cetak Dokumen Resmi (Print-Friendly):**
  * Layout khusus cetak tanpa elemen navigasi admin (`@media print`).
  * Dilengkapi kop surat resmi restoran, rincian transaksi, dan kolom pengesahan tanda tangan kasir & manager.

---

### SLIDE 9: PENGUJIAN UNIT PROGRAM (UNIT TESTING)
* **Kepatuhan Unit Kompetensi J.620100.033.02:**
  * Pengujian otomatis fungsi program menggunakan PHP CLI test runner (`php tests/run_tests.php`).
* **Cakupan Pengujian (17 Kasus Uji):**
  * **Validasi:** Data pelanggan valid, nama kosong, telepon salah, email salah, harga negatif, kategori invalid.
  * **Kalkulasi:** Subtotal normal, kuantitas nol, harga negatif, pemformatan mata uang Rupiah.
  * **Aturan Reservasi:** Batas kapasitas pas, batas kapasitas kurang, tamu melebihi kapasitas, tamu nol, dan pendeteksian tumpang tindih waktu.
* **Hasil Pengujian:** **17 dari 17 Kasus Uji LOLOS (100% PASS, 0 FAIL)**.

---

### SLIDE 10: KESIMPULAN & DEMONSTRASI LANGSUNG
* **Kesimpulan:**
  1. Seluruh spesifikasi kebutuhan tugas praktik demonstrasi telah terpenuhi secara utuh.
  2. Kode program terstruktur rapi, menerapkan prinsip OOP, aman dari celah keamanan (SQLi, XSS, CSRF), dan memiliki performa tinggi.
  3. Dokumen kode program, laporan teknis, dan suite pengujian unit telah lengkap tersedia.
* **Sesi Demonstrasi Langsung (Live Demo):**
  * *Demo Alur Reservasi Meja -> Pengecekan Kapasitas -> Input Pesanan Menu -> Cetak Laporan.*
  * **Terima Kasih! Siap menjawab pertanyaan dan evaluasi dari Dewan Asesor.**
