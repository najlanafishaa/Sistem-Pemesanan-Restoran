<?php
/**
 * Model DetailPemesanan (MySQLi)
 * Mengelola item pesanan menu yang terkait dengan transaksi pemesanan meja.
 */

require_once __DIR__ . '/../config/Database.php';

class DetailPemesanan {
    private mysqli $db;
    private string $table = 'detail_pemesanan';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /** Ambil detail pesanan berdasarkan ID pemesanan */
    public function getByPemesananId(int $idPemesanan): array {
        $stmt = $this->db->prepare(
            "SELECT dp.*, m.nama_menu, m.kategori
             FROM {$this->table} dp
             JOIN menu m ON dp.id_menu = m.id_menu
             WHERE dp.id_pemesanan = ?
             ORDER BY dp.id_detail ASC"
        );
        $stmt->bind_param('i', $idPemesanan);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** Simpan satu baris detail pesanan */
    public function create(array $data): int {
        $subtotal = self::calculateSubtotal(
            (float)$data['harga_satuan'],
            (int)$data['jumlah']
        );
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table}
             (id_pemesanan, id_menu, jumlah, harga_satuan, subtotal, catatan_khusus)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $idPemesanan  = (int)$data['id_pemesanan'];
        $idMenu       = (int)$data['id_menu'];
        $jumlah       = (int)$data['jumlah'];
        $hargaSatuan  = (float)$data['harga_satuan'];
        $catatan      = !empty($data['catatan_khusus']) ? trim($data['catatan_khusus']) : null;

        $stmt->bind_param('iiidds', $idPemesanan, $idMenu, $jumlah, $hargaSatuan, $subtotal, $catatan);
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        return (int)$id;
    }

    /** Hapus semua detail untuk satu pemesanan */
    public function deleteByPemesananId(int $idPemesanan): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_pemesanan = ?");
        $stmt->bind_param('i', $idPemesanan);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Hitung subtotal harga menu.
     * Mengembalikan 0.00 jika harga atau jumlah negatif.
     */
    public static function calculateSubtotal(float $hargaSatuan, int $jumlah): float {
        if ($hargaSatuan < 0 || $jumlah < 0) {
            return 0.00;
        }
        return round($hargaSatuan * $jumlah, 2);
    }
}
