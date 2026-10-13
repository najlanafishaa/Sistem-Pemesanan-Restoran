-- ===================================================================
-- Skrip Database: db_pemesanan_restoran
-- Sistem Pemesanan Restoran - PHP Native PDO
-- Kompatibel dengan MySQL / MariaDB (Laragon, XAMPP, phpMyAdmin, HeidiSQL)
-- ===================================================================

CREATE DATABASE IF NOT EXISTS `db_pemesanan_restoran` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db_pemesanan_restoran`;

-- --------------------------------------------------------
-- 1. Struktur Tabel `pelanggan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detail_pemesanan`;
DROP TABLE IF EXISTS `pemesanan`;
DROP TABLE IF EXISTS `menu`;
DROP TABLE IF EXISTS `meja`;
DROP TABLE IF EXISTS `pelanggan`;

CREATE TABLE `pelanggan` (
  `id_pelanggan` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_pelanggan` VARCHAR(100) NOT NULL,
  `no_telepon` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `alamat` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Struktur Tabel `meja`
-- --------------------------------------------------------
CREATE TABLE `meja` (
  `id_meja` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_meja` VARCHAR(20) NOT NULL UNIQUE,
  `kapasitas` INT NOT NULL,
  `status_meja` ENUM('Tersedia', 'Terisi', 'Reservasi', 'Perbaikan') NOT NULL DEFAULT 'Tersedia',
  `lokasi` VARCHAR(50) DEFAULT 'Indoor',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Struktur Tabel `menu`
-- --------------------------------------------------------
CREATE TABLE `menu` (
  `id_menu` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_menu` VARCHAR(100) NOT NULL,
  `kategori` ENUM('Makanan', 'Minuman', 'Dessert', 'Paket') NOT NULL,
  `harga` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status_menu` ENUM('Tersedia', 'Habis') NOT NULL DEFAULT 'Tersedia',
  `deskripsi` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Struktur Tabel `pemesanan`
-- --------------------------------------------------------
CREATE TABLE `pemesanan` (
  `id_pemesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_pemesanan` VARCHAR(25) NOT NULL UNIQUE,
  `id_pelanggan` INT NOT NULL,
  `id_meja` INT NOT NULL,
  `tanggal_pemesanan` DATE NOT NULL,
  `waktu_mulai` TIME NOT NULL,
  `waktu_selesai` TIME NOT NULL,
  `jumlah_orang` INT NOT NULL,
  `status_pemesanan` ENUM('Diproses', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Diproses',
  `total_harga` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pemesanan_pelanggan` 
    FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) 
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pemesanan_meja` 
    FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Struktur Tabel `detail_pemesanan`
-- --------------------------------------------------------
CREATE TABLE `detail_pemesanan` (
  `id_detail` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pemesanan` INT NOT NULL,
  `id_menu` INT NOT NULL,
  `jumlah` INT NOT NULL DEFAULT 1,
  `harga_satuan` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `catatan_khusus` VARCHAR(255) DEFAULT NULL,
  CONSTRAINT `fk_detail_pemesanan` 
    FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_menu` 
    FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================================
-- DATA AWAL (SEED DATA UNTUK PENGUJIAN DAN DEMONSTRASI)
-- ===================================================================

-- Data Pelanggan
INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telepon`, `email`, `alamat`) VALUES
(1, 'Ahmad Fauzi', '081234567890', 'ahmad.fauzi@gmail.com', 'Jl. Raden Intan No. 45, Enggal, Bandar Lampung'),
(2, 'Siti Nurhaliza', '085712345678', 'siti.nurhaliza@yahoo.com', 'Jl. ZA. Pagar Alam No. 12, Kedaton, Bandar Lampung'),
(3, 'Budi Santoso', '082198765432', 'budi.santoso@gmail.com', 'Jl. Teuku Umar No. 88, Penengahan, Bandar Lampung'),
(4, 'Dewi Lestari', '081345678901', 'dewi.lestari@outlook.com', 'Jl. Kartini No. 23, Tanjung Karang Pusat, Bandar Lampung'),
(5, 'Rian Pratama', '087811223344', 'rian.pratama@gmail.com', 'Jl. Wolter Monginsidi No. 17, Teluk Betung, Bandar Lampung');

-- Data Meja Restoran
INSERT INTO `meja` (`id_meja`, `nomor_meja`, `kapasitas`, `status_meja`, `lokasi`) VALUES
(1, 'Meja 01', 2, 'Tersedia', 'Indoor Lantai 1'),
(2, 'Meja 02', 4, 'Tersedia', 'Indoor Lantai 1'),
(3, 'Meja 03', 4, 'Reservasi', 'Indoor Lantai 1'),
(4, 'Meja 04', 6, 'Terisi', 'Indoor Lantai 1'),
(5, 'Meja 05', 8, 'Tersedia', 'Outdoor Garden'),
(6, 'Meja 06', 4, 'Tersedia', 'Outdoor Garden'),
(7, 'VIP 01', 10, 'Tersedia', 'VIP Room Lantai 2'),
(8, 'VIP 02', 12, 'Tersedia', 'VIP Room Lantai 2');

-- Data Menu Makanan dan Minuman
INSERT INTO `menu` (`id_menu`, `nama_menu`, `kategori`, `harga`, `status_menu`, `deskripsi`) VALUES
(1, 'Nasi Goreng Spesial Resto', 'Makanan', 35000.00, 'Tersedia', 'Nasi goreng bumbu rempah pilihan dengan telur mata sapi, sate ayam, dan kerupuk udang'),
(2, 'Ayam Bakar Madu', 'Makanan', 42000.00, 'Tersedia', 'Ayam bakar dengan baluran madu murni dan bumbu rica manis pedas gurih'),
(3, 'Sop Buntut Gurih', 'Makanan', 65000.00, 'Tersedia', 'Potongan buntut sapi empuk berkuah kaldu bening kaya rempah nusantara'),
(4, 'Ikan Gurame Asam Manis', 'Makanan', 58000.00, 'Tersedia', 'Gurame goreng renyah disiram saus asam manis dengan irisan nanas segar'),
(5, 'Mie Goreng Seafood', 'Makanan', 38000.00, 'Tersedia', 'Mie kenyal dengan udang, cumi, bakso ikan dan sayuran segar'),
(6, 'Es Teh Manis', 'Minuman', 8000.00, 'Tersedia', 'Teh melati seduh segar dingin dengan pemanis gula tebu'),
(7, 'Jus Alpukat Kocok', 'Minuman', 20000.00, 'Tersedia', 'Alpukat mentega segar dengan lelehan susu kental manis cokelat'),
(8, 'Kopi Susu Gula Aren', 'Minuman', 18000.00, 'Tersedia', 'Espresso arabika blend dengan susu segar dan sirup aren organik'),
(9, 'Lemon Tea Dingin', 'Minuman', 15000.00, 'Tersedia', 'Teh hitam segar dipadukan dengan perasan lemon murni'),
(10, 'Pancake Cokelat Keju', 'Dessert', 25000.00, 'Tersedia', 'Pancake empuk lembut bertabur meses cokelat premium dan parutan keju cheddar'),
(11, 'Pisang Bakar Cokelat Keju', 'Dessert', 20000.00, 'Tersedia', 'Pisang kepok manis dipanggang empuk dengan taburan keju dan susu'),
(12, 'Paket Hemat Berempat', 'Paket', 165000.00, 'Tersedia', '4 Porsi Nasi + 1 Gurame Asam Manis + 1 Ayam Bakar + 4 Es Teh Manis');

-- Data Pemesanan Contoh
INSERT INTO `pemesanan` (`id_pemesanan`, `kode_pemesanan`, `id_pelanggan`, `id_meja`, `tanggal_pemesanan`, `waktu_mulai`, `waktu_selesai`, `jumlah_orang`, `status_pemesanan`, `total_harga`, `catatan`) VALUES
(1, 'ORD-20261011-001', 1, 2, '2026-10-11', '12:00:00', '14:00:00', 4, 'Selesai', 149000.00, 'Dekat jendela jika memungkinkan'),
(2, 'ORD-20261011-002', 2, 3, '2026-10-11', '18:30:00', '20:30:00', 3, 'Diproses', 105000.00, 'Ulang tahun kecil keluarga'),
(3, 'ORD-20261012-003', 3, 5, '2026-10-12', '13:00:00', '15:00:00', 6, 'Diproses', 165000.00, 'Acara reuni teman kantor');

-- Data Detail Pemesanan Contoh
INSERT INTO `detail_pemesanan` (`id_detail`, `id_pemesanan`, `id_menu`, `jumlah`, `harga_satuan`, `subtotal`, `catatan_khusus`) VALUES
-- Detail untuk Pemesanan 1
(1, 1, 1, 2, 35000.00, 70000.00, '1 pedas sedang, 1 tidak pedas'),
(2, 1, 2, 1, 42000.00, 42000.00, 'Paha'),
(3, 1, 6, 2, 8000.00, 16000.00, 'Gula sedikit'),
(4, 1, 9, 1, 15000.00, 15000.00, 'Kurang es'),
(5, 1, 11, 1, 20000.00, 20000.00, 'Ekstra keju'),

-- Detail untuk Pemesanan 2
(6, 2, 3, 1, 65000.00, 65000.00, 'Sambal dipisah'),
(7, 2, 7, 2, 20000.00, 40000.00, 'Normal'),

-- Detail untuk Pemesanan 3
(8, 3, 12, 1, 165000.00, 165000.00, 'Paket berempat');
