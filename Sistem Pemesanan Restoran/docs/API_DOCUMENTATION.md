# DOKUMENTASI KODE SUMBER — RESTO NUSANTARA

*Dihasilkan secara otomatis pada: 2026-10-12 08:01:55*

Dokumentasi teknis arsitektur kelas, method, parameter, dan nilai balik sistem pemesanan restoran.

---

## Kelas `BaseModel`
- **Induk (Inheritance)**: `-`
- **Deskripsi**: Abstract Class BaseModel
  Base model yang mengimplementasikan dasar Inheritance & Polymorphism
  untuk seluruh entitas sistem (Pelanggan, Meja, Pemesanan, Menu).

### Daftar Method:

| Nama Method | Visibilitas | Parameter | Return Type | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `__construct()` | `public` | `` | `void` | Constructor BaseModel - Menginisialisasi koneksi database Singleton |
| `getTable()` | `public` | `` | `string` | Mendapatkan nama tabel dari entitas |
| `count()` | `public` | `` | `int` | Menghitung total data pada tabel |
| `getSummaryInfo()` | `public` | `int $id` | `string` | Polymorphism: Setiap model wajib mengimplementasikan representasi |

---

## Kelas `Pelanggan`
- **Induk (Inheritance)**: `BaseModel`
- **Deskripsi**: Model Pelanggan (MySQLi)
  Mengelola operasi basis data untuk entitas pelanggan.

### Daftar Method:

| Nama Method | Visibilitas | Parameter | Return Type | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `__construct()` | `public` | `` | `void` | - |
| `getAll()` | `public` | `` | `array` | Ambil semua pelanggan |
| `getById()` | `public` | `int $id` | `array` | Ambil pelanggan berdasarkan ID |
| `create()` | `public` | `array $data` | `int` | Tambah pelanggan baru. |
| `update()` | `public` | `int $id, array $data` | `bool` | Perbarui data pelanggan |
| `delete()` | `public` | `int $id` | `bool` | Hapus pelanggan — cek riwayat pemesanan terlebih dahulu |
| `count()` | `public` | `` | `int` | Hitung total pelanggan |
| `validate()` | `public static` | `array $data` | `array` | Validasi input pelanggan — kembalikan array error (kosong = valid) |
| `getSummaryInfo()` | `public` | `int $id` | `string` | Polymorphism: Representasi ringkas objek pelanggan |
| `getTable()` | `public` | `` | `string` | Mendapatkan nama tabel dari entitas |

---

## Kelas `Meja`
- **Induk (Inheritance)**: `BaseModel`
- **Deskripsi**: Model Meja (MySQLi)
  Mengelola data meja restoran: kapasitas, lokasi, dan status ketersediaan.

### Daftar Method:

| Nama Method | Visibilitas | Parameter | Return Type | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `__construct()` | `public` | `` | `void` | - |
| `getAll()` | `public` | `` | `array` | Ambil semua meja |
| `getById()` | `public` | `int $id` | `array` | Ambil meja berdasarkan ID |
| `getAvailable()` | `public` | `` | `array` | Ambil meja yang tersedia |
| `create()` | `public` | `array $data` | `int` | Tambah meja baru |
| `update()` | `public` | `int $id, array $data` | `bool` | Perbarui data meja |
| `updateStatus()` | `public` | `int $id, string $status` | `bool` | Perbarui hanya status meja |
| `delete()` | `public` | `int $id` | `bool` | Hapus meja — cek riwayat pemesanan |
| `count()` | `public` | `` | `int` | Hitung total meja |
| `validate()` | `public` | `array $data, int $excludeId = NULL` | `array` | Validasi input meja |
| `getSummaryInfo()` | `public` | `int $id` | `string` | Polymorphism: Representasi ringkas objek meja |
| `getTable()` | `public` | `` | `string` | Mendapatkan nama tabel dari entitas |

---

## Kelas `Menu`
- **Induk (Inheritance)**: `BaseModel`
- **Deskripsi**: Model Menu (MySQLi)
  Mengelola data menu makanan, minuman, dessert, dan paket restoran.

### Daftar Method:

| Nama Method | Visibilitas | Parameter | Return Type | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `__construct()` | `public` | `` | `void` | - |
| `getAll()` | `public` | `string $kategori = NULL` | `array` | Ambil semua menu, opsional filter kategori |
| `getAvailable()` | `public` | `` | `array` | Ambil menu yang statusnya Tersedia |
| `getById()` | `public` | `int $id` | `array` | Ambil menu berdasarkan ID |
| `create()` | `public` | `array $data` | `int` | Tambah menu baru |
| `update()` | `public` | `int $id, array $data` | `bool` | Perbarui data menu |
| `delete()` | `public` | `int $id` | `bool` | Hapus menu — cek riwayat detail_pemesanan |
| `count()` | `public` | `` | `int` | Hitung total menu |
| `validate()` | `public static` | `array $data` | `array` | Validasi input menu |
| `getSummaryInfo()` | `public` | `int $id` | `string` | Polymorphism: Representasi ringkas objek menu |
| `getTable()` | `public` | `` | `string` | Mendapatkan nama tabel dari entitas |

---

## Kelas `Pemesanan`
- **Induk (Inheritance)**: `BaseModel`
- **Deskripsi**: Model Pemesanan (MySQLi)
  Mengelola reservasi meja: transaksi atomik, validasi kapasitas,
  pencegahan tabrakan jadwal, dan kalkulasi total harga.

### Daftar Method:

| Nama Method | Visibilitas | Parameter | Return Type | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `__construct()` | `public` | `` | `void` | - |
| `getAll()` | `public` | `string $status = NULL, string $tanggalDari = NULL, string $tanggalSampai = NULL` | `array` | Ambil daftar pemesanan dengan filter opsional |
| `getRecent()` | `public` | `int $limit = 5` | `array` | Ambil ringkasan pemesanan terbaru untuk dashboard |
| `getById()` | `public` | `int $id` | `array` | Ambil pemesanan berdasarkan ID beserta detail item menu |
| `checkConflict()` | `public` | `int $idMeja, string $tanggal, string $waktuMulai, string $waktuSelesai, int $excludeId = NULL` | `bool` | Cek tabrakan waktu pada meja yang sama. |
| `validateCapacity()` | `public static` | `int $jumlahOrang, int $kapasitasMeja` | `bool` | Validasi kapasitas meja |
| `generateKodePemesanan()` | `public` | `string $tanggal` | `string` | Generate kode pemesanan unik: ORD-YYYYMMDD-XXX |
| `createWithItems()` | `public` | `array $data, array $items = array (
)` | `int` | Buat pemesanan baru + detail menu dalam satu transaksi atomik. |
| `updateWithItems()` | `public` | `int $id, array $data, array $items = array (
)` | `bool` | Perbarui pemesanan + detail menu dalam transaksi atomik. |
| `updateStatus()` | `public` | `int $id, string $status` | `bool` | Perbarui status pemesanan |
| `delete()` | `public` | `int $id` | `bool` | Hapus pemesanan (detail dihapus otomatis via ON DELETE CASCADE) |
| `count()` | `public` | `` | `int` | Hitung total pemesanan |
| `countByStatus()` | `public` | `string $status` | `int` | Hitung pemesanan berdasarkan status |
| `getTotalPendapatan()` | `public` | `` | `float` | Hitung total pendapatan dari pemesanan Dikonfirmasi & Selesai |
| `getSummaryInfo()` | `public` | `int $id` | `string` | Polymorphism: Representasi ringkas objek pemesanan |
| `getTable()` | `public` | `` | `string` | Mendapatkan nama tabel dari entitas |

---

## Kelas `DetailPemesanan`
- **Induk (Inheritance)**: `-`
- **Deskripsi**: Model DetailPemesanan (MySQLi)
  Mengelola item pesanan menu yang terkait dengan transaksi pemesanan meja.

### Daftar Method:

| Nama Method | Visibilitas | Parameter | Return Type | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `__construct()` | `public` | `` | `void` | - |
| `getByPemesananId()` | `public` | `int $idPemesanan` | `array` | Ambil detail pesanan berdasarkan ID pemesanan |
| `create()` | `public` | `array $data` | `int` | Simpan satu baris detail pesanan |
| `deleteByPemesananId()` | `public` | `int $idPemesanan` | `bool` | Hapus semua detail untuk satu pemesanan |
| `calculateSubtotal()` | `public static` | `float $hargaSatuan, int $jumlah` | `float` | Hitung subtotal harga menu. |

---

