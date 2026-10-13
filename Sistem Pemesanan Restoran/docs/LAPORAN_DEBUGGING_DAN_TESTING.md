# DOKUMENTASI DEBUGGING & PENGUJIAN UNIT (SKKNI)
**Sistem Pemesanan Restoran — Resto Nusantara**  
*Mencakup Unit Kompetensi:*
* **J.620100.033.02** — Melaksanakan Pengujian Unit Program
* **J.620100.034.02** — Melakukan Debugging

---

## 1. LOG PENCATATAN KESALAHAN (DEBUGGING LOG) & PERBAIKANNYA

Berikut adalah dokumentasi kesalahan (*bugs/issues*) yang ditemukan selama pengujian/eksekusi beserta analisis penyebab dan solusi perbaikannya:

| ID Bug | Modul / Lokasi | Deskripsi Kesalahan (Error) | Penyebab (Root Cause) | Teknik Debugging | Solusi & Tindakan Perbaikan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **BUG-01** | `config/Database.php` | Koneksi database gagal saat PDO diganti ke MySQLi | Konfigurasi MySQLi standar memerlukan penanganan port, charset UTF-8, dan pelaporan error eksplisit | Static Code Inspection & Error Log | Mengimplementasikan Singleton MySQLi dengan mematikan `MYSQLI_REPORT_OFF` agar error dapat ditangkap mandiri dan menambahkan alert ramah pengguna. | **RESOLVED (Fixed)** |
| **BUG-02** | `models/Pemesanan.php` | Tabrakan pemesanan pada jam yang beririsan tidak terdeteksi | Logika operator waktu `<` dan `>` belum mencakup kasus pergeseran jam (*overlap boundary condition*) | Trace Table Execution & Boundary Testing | Memperbaiki formula interval SQL: `(? < waktu_selesai AND ? > waktu_mulai)` sehingga perpotongan waktu antara 2 reservasi di meja yang sama otomatis terblokir. | **RESOLVED (Fixed)** |
| **BUG-03** | `models/Meja.php` | Input kapasitas meja bernilai `0` atau minus lolos validasi | Pemeriksaan validasi hanya memeriksa keberadaan input (`isset`), belum memeriksa nilai numerik positif | Unit Test Assertion & Boundary Testing | Menambahkan percabangan: `if ((int)$data['kapasitas'] <= 0)` yang memicu pesan galat validasi. | **RESOLVED (Fixed)** |
| **BUG-04** | `models/DetailPemesanan.php` | Total harga bernilai negatif saat input jumlah item bernilai negatif | Tidak adanya pengecekan nilai negatif pada perhitungan subtotal menu | Unit Testing Calculation & Black Box Testing | Menambahkan proteksi pada method `calculateSubtotal()`: jika harga atau kuantitas `< 0`, subtotal otomatis dikembalikan `0.00`. | **RESOLVED (Fixed)** |
| **BUG-05** | `views/layout/navbar.php` | Link booking online publik belum tersedia dari navbar admin | Belum adanya routing cepat untuk simulasi tamu mandiri | User Experience Walkthrough | Menambahkan tombol aksi *"Form Tamu (Booking Online)"* dengan atribut `target="_blank"` pada navbar. | **RESOLVED (Fixed)** |

---

## 2. SKENARIO & HASIL PENGUJIAN UNIT PROGRAM (UNIT TESTING)

Pengujian dijalankan secara otomatis melalui runner script [`tests/run_tests.php`](file:///d:/Sistem%20Pemesanan%20Restoran/tests/run_tests.php).  
Total pengujian: **20 Kasus Uji** | Lolos: **20 (100%)** | Gagal: **0**.

### Rincian Skenario Pengujian:

| No | Modul Uji | Nama Kasus Uji | Input yang Diuji | Hasil yang Diharapkan | Hasil Nyata | Kesimpulan |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: |
| 1 | Validasi Pelanggan | `testValidCustomerData` | Nama: "Budi Santoso", Telp: "081234567890", Email valid | Validasi lolos (0 error) | 0 error | **PASS** |
| 2 | Validasi Pelanggan | `testEmptyCustomerName` | Nama kosong `""` | Error `'nama_pelanggan'` muncul | Error terdeteksi | **PASS** |
| 3 | Validasi Pelanggan | `testInvalidCustomerPhone` | Telp: `"abc-123"` (karakter huruf) | Error format telepon muncul | Error terdeteksi | **PASS** |
| 4 | Validasi Pelanggan | `testInvalidCustomerEmail` | Email: `"budi@"` (tanpa domain) | Error format email muncul | Error terdeteksi | **PASS** |
| 5 | Validasi Menu | `testValidMenuData` | Nama: "Nasi Goreng", Kategori: "Makanan", Harga: 25000 | Validasi lolos (0 error) | 0 error | **PASS** |
| 6 | Validasi Menu | `testNegativeMenuPrice` | Harga: `-10000` | Error harga tidak valid muncul | Error terdeteksi | **PASS** |
| 7 | Validasi Menu | `testInvalidMenuCategory` | Kategori: `"Elektronik"` | Error kategori tidak valid | Error terdeteksi | **PASS** |
| 8 | Perhitungan | `testCalculateSubtotalNormal`| Harga: 15.000, Qty: 3 | Subtotal = 45.000 | Subtotal 45.000 | **PASS** |
| 9 | Perhitungan | `testCalculateSubtotalZeroQty`| Harga: 15.000, Qty: 0 | Subtotal = 0 | Subtotal 0 | **PASS** |
| 10 | Perhitungan | `testCalculateSubtotalNegativePrice` | Harga: -5.000, Qty: 2 | Subtotal = 0 (proteksi minus) | Subtotal 0 | **PASS** |
| 11 | Format Mata Uang | `testFormatRupiahNoDecimal` | Nilai: 25.000 | Teks: `"Rp 25.000"` | `"Rp 25.000"` | **PASS** |
| 12 | Format Mata Uang | `testFormatRupiahWithDecimal` | Nilai: 50.000,00 | Teks: `"Rp 50.000"` | `"Rp 50.000"` | **PASS** |
| 13 | Aturan Meja | `testCapacityWithinLimit` | Tamu: 4, Kapasitas: 4 | Status valid: `true` | `true` | **PASS** |
| 14 | Aturan Meja | `testCapacityBelowLimit` | Tamu: 2, Kapasitas: 4 | Status valid: `true` | `true` | **PASS** |
| 15 | Aturan Meja | `testCapacityExceeded` | Tamu: 6, Kapasitas: 4 | Status valid: `false` | `false` | **PASS** |
| 16 | Aturan Meja | `testZeroGuestCapacity` | Tamu: 0, Kapasitas: 4 | Status valid: `false` | `false` | **PASS** |
| 17 | Konflik Jadwal | `testScheduleOverlapLogic` | Jam 12:00-14:00 beririsan dengan 13:00-15:00 | Konflik terdeteksi (`true`) | Terdeteksi (`true`) | **PASS** |
| 18 | OOP Inheritance | `testInheritanceHierarchy` | Cek `instanceof BaseModel` pada Pelanggan, Meja, Pemesanan, Menu | Semua objek turunan BaseModel | `true` | **PASS** |
| 19 | OOP Inheritance | `testInheritedMethods` | Cek warisan `getTable()` dari `BaseModel` | Mengembalikan nama tabel yang tepat | Sesuai | **PASS** |
| 20 | OOP Polymorphism | `testPolymorphismBehavior` | Pemanggilan dinamis `getSummaryInfo()` lintas objek | Mengembalikan string ringkasan spesifik entitas | Sesuai | **PASS** |

---

## 3. CARA MENJALANKAN ULANG PENGUJIAN & DOKUMENTASI

1. **Menjalankan Pengujian Unit:**
   ```bash
   php tests/run_tests.php
   ```
   *Atau akses melalui browser:* `http://localhost/tests/run_tests.php`

2. **Menghasilkan Ulang Dokumentasi Kode Otomatis:**
   ```bash
   php docs/generate_docs.php
   ```
   *Hasil dokumentasi dapat dibuka langsung di browser:* `http://localhost/docs/index.html`
