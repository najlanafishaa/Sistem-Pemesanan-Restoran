<?php
/**
 * Model Menu (MySQLi)
 * Mengelola data menu makanan, minuman, dessert, dan paket restoran.
 */

require_once __DIR__ . '/BaseModel.php';

class Menu extends BaseModel {
    protected string $table = 'menu';
    protected string $primaryKey = 'id_menu';

    public function __construct() {
        parent::__construct();
    }

    /** Ambil semua menu, opsional filter kategori */
    public function getAll(?string $kategori = null): array {
        if ($kategori) {
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE kategori = ? ORDER BY nama_menu ASC"
            );
            $stmt->bind_param('s', $kategori);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        }
        $result = $this->db->query(
            "SELECT * FROM {$this->table} ORDER BY kategori ASC, nama_menu ASC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Ambil menu yang statusnya Tersedia */
    public function getAvailable(): array {
        $result = $this->db->query(
            "SELECT * FROM {$this->table}
             WHERE status_menu = 'Tersedia'
             ORDER BY kategori ASC, nama_menu ASC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Ambil menu berdasarkan ID */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_menu = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Tambah menu baru */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (nama_menu, kategori, harga, status_menu, deskripsi)
             VALUES (?, ?, ?, ?, ?)"
        );
        $nama      = trim($data['nama_menu']);
        $kategori  = $data['kategori'];
        $harga     = (float)$data['harga'];
        $status    = $data['status_menu'] ?? 'Tersedia';
        $deskripsi = !empty($data['deskripsi']) ? trim($data['deskripsi']) : null;
        $stmt->bind_param('ssdss', $nama, $kategori, $harga, $status, $deskripsi);
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        return (int)$id;
    }

    /** Perbarui data menu */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET nama_menu = ?, kategori = ?, harga = ?, status_menu = ?, deskripsi = ?
             WHERE id_menu = ?"
        );
        $nama      = trim($data['nama_menu']);
        $kategori  = $data['kategori'];
        $harga     = (float)$data['harga'];
        $status    = $data['status_menu'];
        $deskripsi = !empty($data['deskripsi']) ? trim($data['deskripsi']) : null;
        $stmt->bind_param('ssdssi', $nama, $kategori, $harga, $status, $deskripsi, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Hapus menu — cek riwayat detail_pemesanan */
    public function delete(int $id): bool {
        $chk = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM detail_pemesanan WHERE id_menu = ?"
        );
        $chk->bind_param('i', $id);
        $chk->execute();
        $cnt = (int)$chk->get_result()->fetch_assoc()['cnt'];
        $chk->close();

        if ($cnt > 0) {
            throw new Exception("Menu tidak dapat dihapus karena sudah tercatat dalam riwayat pesanan.");
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_menu = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Hitung total menu */
    public function count(): int {
        $row = $this->db->query("SELECT COUNT(*) AS cnt FROM {$this->table}")->fetch_assoc();
        return (int)$row['cnt'];
    }

    /** Validasi input menu */
    public static function validate(array $data): array {
        $errors = [];

        if (empty(trim($data['nama_menu'] ?? ''))) {
            $errors['nama_menu'] = 'Nama menu wajib diisi.';
        }

        $validKategori = ['Makanan', 'Minuman', 'Dessert', 'Paket'];
        if (empty($data['kategori']) || !in_array($data['kategori'], $validKategori, true)) {
            $errors['kategori'] = 'Pilih kategori menu yang valid.';
        }

        if (!isset($data['harga']) || !is_numeric($data['harga']) || (float)$data['harga'] < 0) {
            $errors['harga'] = 'Harga harus berupa angka yang valid dan tidak boleh negatif.';
        }

        $validStatus = ['Tersedia', 'Habis'];
        if (!empty($data['status_menu']) && !in_array($data['status_menu'], $validStatus, true)) {
            $errors['status_menu'] = 'Status menu tidak valid.';
        }

        return $errors;
    }

    /**
     * Polymorphism: Representasi ringkas objek menu
     */
    public function getSummaryInfo(int $id): string {
        $data = $this->getById($id);
        if (!$data) return 'Menu tidak ditemukan';
        $hargaFormatted = number_format($data['harga'], 0, ',', '.');
        return "Menu: {$data['nama_menu']} [{$data['kategori']}] - Rp {$hargaFormatted} ({$data['status_menu']})";
    }
}
