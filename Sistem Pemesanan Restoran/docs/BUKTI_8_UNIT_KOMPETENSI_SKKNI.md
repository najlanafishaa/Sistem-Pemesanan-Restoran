# PANDUAN ASESMEN & PEMENUHAN 8 UNIT KOMPETENSI SKKNI
## SISTEM PEMESANAN RESTORAN — RESTO NUSANTARA

Dokumen ini adalah **panduan resmi pembuktian** untuk Asesor / Penguji. Sistem ini dikembangkan secara **fokus, sederhana, dan tepat sasaran** hanya pada **8 Unit Kompetensi SKKNI** berikut:

---

### TABEL RINGKASAN BUKTI KODINGAN & 8 UNIT KOMPETENSI

| No | Kode Unit | Judul Unit Kompetensi | Bukti Nyata File & Baris Kodingan |
| :---: | :--- | :--- | :--- |
| **1** | **J.620100.016.01** | **Menulis Kode dengan Prinsip Sesuai Guidelines dan Best Practices** | • Arsitektur folder terpisah rapi: `config/`, `models/`, `controllers/`, `views/`<br>• Penamaan variabel camelCase & snake_case yang konsisten<br>• Standar format kode PSR-12 di seluruh file PHP |
| **2** | **J.620100.017.02** | **Mengimplementasikan Pemrograman Terstruktur** | • [`config/helpers.php`](file:///d:/Sistem%20Pemesanan%20Restoran/config/helpers.php): Fungsi modular `formatRupiah()`, `formatTanggalIndonesia()`, `e()` (sanitasi XSS)<br>• Struktur kontrol: percabangan `if/else`, `switch/case` router di [`index.php`](file:///d:/Sistem%20Pemesanan%20Restoran/index.php)<br>• Perulangan `foreach` untuk iterasi data tabel dan array pesanan |
| **3** | **J.620100.018.02** | **Mengimplementasikan Pemrograman Berorientasi Objek (OOP)** | • **Class & Encapsulation:** Properti `protected` dan `private` pada Model & Controller<br>• **Inheritance (Pewarisan):** [`models/BaseModel.php`](file:///d:/Sistem%20Pemesanan%20Restoran/models/BaseModel.php) diwarisi oleh `class Pelanggan extends BaseModel`, `class Meja extends BaseModel`, `class Pemesanan extends BaseModel`<br>• **Polymorphism:** Method `getSummaryInfo(int $id)` diimplementasikan dengan output berbeda pada tiap model anak |
| **4** | **J.620100.019.02** | **Menggunakan Library atau Komponen Pre-Existing** | • **Bootstrap 5.3.2:** Library CSS/JS untuk grid responsif, card, alert, modal, dan tabel<br>• **Bootstrap Icons 1.11.3:** Library icon pack vektor<br>• **MySQLi Driver:** Library driver bawaan PHP untuk koneksi dan query database |
| **5** | **J.620100.021.02** | **Menerapkan Akses Basis Data** | • [`config/Database.php`](file:///d:/Sistem%20Pemesanan%20Restoran/config/Database.php): Koneksi MySQLi Singleton<br>• **Prepared Statements:** Menggunakan `$stmt->bind_param()` untuk mencegah SQL Injection<br>• **Database Transaction:** `begin_transaction()`, `commit()`, `rollback()` pada method `createWithItems()` di [`models/Pemesanan.php`](file:///d:/Sistem%20Pemesanan%20Restoran/models/Pemesanan.php) |
| **6** | **J.620100.023.02** | **Membuat Dokumen Kode Program** | • Standar dokumentasi blok komentar **PHPDoc** (`@param`, `@return`) di setiap file<br>• Generator otomatis Reflection API: [`docs/generate_docs.php`](file:///d:/Sistem%20Pemesanan%20Restoran/docs/generate_docs.php)<br>• Dokumen hasil ekstraksi: [`docs/API_DOCUMENTATION.md`](file:///d:/Sistem%20Pemesanan%20Restoran/docs/API_DOCUMENTATION.md) dan web [`docs/index.html`](file:///d:/Sistem%20Pemesanan%20Restoran/docs/index.html) |
| **7** | **J.620100.025.02** | **Melakukan Debugging** | • Dokumen analisis pelacakan bug & teknik debugging: [`docs/LAPORAN_DEBUGGING_DAN_TESTING.md`](file:///d:/Sistem%20Pemesanan%20Restoran/docs/LAPORAN_DEBUGGING_DAN_TESTING.md)<br>• Mencatat 5 bug nyata (koneksi MySQLi, logika boundary jadwal meja, validasi minus, dll) beserta solusi perbaikannya |
| **8** | **J.620100.033.02** | **Melaksanakan Pengujian Unit Program** | • Test runner otomatis: [`tests/run_tests.php`](file:///d:/Sistem%20Pemesanan%20Restoran/tests/run_tests.php)<br>• 20 kasus pengujian unit mencakup validasi input, rumus subtotal, pencegahan tabrakan meja, dan OOP<br>• Hasil uji: **20/20 PASS (Lolos 100%)** |

---

### CARA DEMONSTRASI KE ASESOR (HANYA 3 LANGKAH MUDAH):

1. **Jalankan Aplikasi di Browser:**
   * Buka: `http://localhost/` $\rightarrow$ Muncul form reservasi pelanggan.
   * Klik tombol *Panel Admin / Kasir* di pojok kanan atas $\rightarrow$ Buka dashboard operasional kasir.

2. **Tunjukkan Pengujian Unit (Unit Testing - Unit 8):**
   * Buka terminal / command prompt, jalankan:
     ```bash
     php tests/run_tests.php
     ```
   * Asesor akan langsung melihat 20 test berstatus hijau **[PASS]**.

3. **Tunjukkan Dokumentasi Kode Otomatis (Unit 6):**
   * Buka terminal, jalankan:
     ```bash
     php docs/generate_docs.php
     ```
   * Buka di browser: `http://localhost/docs/index.html`.
