<?php
/**
 * Model Pelanggan (MySQLi)
 * Mengelola operasi basis data untuk entitas pelanggan.
 */

require_once __DIR__ . '/BaseModel.php';

class Pelanggan extends BaseModel {
    protected string $table = 'pelanggan';
    protected string $primaryKey = 'id_pelanggan';

    public function __construct() {
        parent::__construct();
    }

    /** Ambil semua pelanggan */
    public function getAll(): array {
        $result = $this->db->query("SELECT * FROM {$this->table} ORDER BY id_pelanggan DESC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Ambil pelanggan berdasarkan ID */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_pelanggan = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /**
     * Tambah pelanggan baru.
     * @return int ID pelanggan yang baru dibuat
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (nama_pelanggan, no_telepon, email, alamat)
             VALUES (?, ?, ?, ?)"
        );
        $email  = !empty($data['email'])  ? $data['email']  : null;
        $alamat = !empty($data['alamat']) ? $data['alamat'] : null;
        $stmt->bind_param('ssss', $data['nama_pelanggan'], $data['no_telepon'], $email, $alamat);
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        return (int)$id;
    }

    /** Perbarui data pelanggan */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET nama_pelanggan = ?, no_telepon = ?, email = ?, alamat = ?
             WHERE id_pelanggan = ?"
        );
        $email  = !empty($data['email'])  ? $data['email']  : null;
        $alamat = !empty($data['alamat']) ? $data['alamat'] : null;
        $stmt->bind_param('ssssi', $data['nama_pelanggan'], $data['no_telepon'], $email, $alamat, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Hapus pelanggan — cek riwayat pemesanan terlebih dahulu */
    public function delete(int $id): bool {
        $chk = $this->db->prepare("SELECT COUNT(*) AS cnt FROM pemesanan WHERE id_pelanggan = ?");
        $chk->bind_param('i', $id);
        $chk->execute();
        $cnt = (int)$chk->get_result()->fetch_assoc()['cnt'];
        $chk->close();

        if ($cnt > 0) {
            throw new Exception("Pelanggan tidak dapat dihapus karena memiliki riwayat pemesanan.");
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_pelanggan = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Hitung total pelanggan */
    public function count(): int {
        $row = $this->db->query("SELECT COUNT(*) AS cnt FROM {$this->table}")->fetch_assoc();
        return (int)$row['cnt'];
    }

    /** Validasi input pelanggan — kembalikan array error (kosong = valid) */
    public static function validate(array $data): array {
        $errors = [];

        if (empty(trim($data['nama_pelanggan'] ?? ''))) {
            $errors['nama_pelanggan'] = 'Nama pelanggan wajib diisi.';
        } elseif (strlen(trim($data['nama_pelanggan'])) < 3) {
            $errors['nama_pelanggan'] = 'Nama pelanggan minimal 3 karakter.';
        }

        if (empty(trim($data['no_telepon'] ?? ''))) {
            $errors['no_telepon'] = 'Nomor telepon wajib diisi.';
        } elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', trim($data['no_telepon']))) {
            $errors['no_telepon'] = 'Format nomor telepon tidak valid (hanya angka, tanda +, -).';
        }

        if (!empty($data['email']) && !filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        return $errors;
    }

    /**
     * Polymorphism: Representasi ringkas objek pelanggan
     */
    public function getSummaryInfo(int $id): string {
        $data = $this->getById($id);
        if (!$data) return 'Pelanggan tidak ditemukan';
        return "Pelanggan: {$data['nama_pelanggan']} ({$data['no_telepon']})";
    }
}
