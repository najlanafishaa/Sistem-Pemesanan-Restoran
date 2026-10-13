<?php
/**
 * Model Meja (MySQLi)
 * Mengelola data meja restoran: kapasitas, lokasi, dan status ketersediaan.
 */

require_once __DIR__ . '/BaseModel.php';

class Meja extends BaseModel {
    protected string $table = 'meja';
    protected string $primaryKey = 'id_meja';

    public function __construct() {
        parent::__construct();
    }

    /** Ambil semua meja */
    public function getAll(): array {
        $result = $this->db->query("SELECT * FROM {$this->table} ORDER BY nomor_meja ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Ambil meja berdasarkan ID */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_meja = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Ambil meja yang tersedia */
    public function getAvailable(): array {
        $result = $this->db->query(
            "SELECT * FROM {$this->table} WHERE status_meja = 'Tersedia' ORDER BY kapasitas ASC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Tambah meja baru */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (nomor_meja, kapasitas, status_meja, lokasi)
             VALUES (?, ?, ?, ?)"
        );
        $nomor    = trim($data['nomor_meja']);
        $kapasitas = (int)$data['kapasitas'];
        $status   = $data['status_meja'] ?? 'Tersedia';
        $lokasi   = !empty($data['lokasi']) ? trim($data['lokasi']) : 'Indoor';
        $stmt->bind_param('siss', $nomor, $kapasitas, $status, $lokasi);
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        return (int)$id;
    }

    /** Perbarui data meja */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET nomor_meja = ?, kapasitas = ?, status_meja = ?, lokasi = ?
             WHERE id_meja = ?"
        );
        $nomor    = trim($data['nomor_meja']);
        $kapasitas = (int)$data['kapasitas'];
        $status   = $data['status_meja'];
        $lokasi   = !empty($data['lokasi']) ? trim($data['lokasi']) : 'Indoor';
        $stmt->bind_param('sissi', $nomor, $kapasitas, $status, $lokasi, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Perbarui hanya status meja */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status_meja = ? WHERE id_meja = ?");
        $stmt->bind_param('si', $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Sinkronkan status satu meja berdasarkan pemesanan aktif (Diproses).
     * Jika ada pesanan dengan status 'Diproses', meja diset 'Terisi'.
     * Jika tidak ada dan saat ini 'Terisi', meja dikembalikan menjadi 'Tersedia'.
     */
    public function syncStatusMeja(int $idMeja): void {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM pemesanan WHERE id_meja = ? AND status_pemesanan = 'Diproses'"
        );
        $stmt->bind_param('i', $idMeja);
        $stmt->execute();
        $cnt = (int)$stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();

        $curMeja = $this->getById($idMeja);
        if (!$curMeja) return;

        if ($cnt > 0) {
            if ($curMeja['status_meja'] !== 'Terisi') {
                $this->updateStatus($idMeja, 'Terisi');
            }
        } else {
            if ($curMeja['status_meja'] === 'Terisi') {
                $this->updateStatus($idMeja, 'Tersedia');
            }
        }
    }

    /**
     * Sinkronkan seluruh status meja dengan data pemesanan aktif di database
     */
    public function syncSemuaStatusMeja(): void {
        // Meja dengan pesanan Diproses -> Terisi
        $this->db->query(
            "UPDATE {$this->table} m
             JOIN (SELECT DISTINCT id_meja FROM pemesanan WHERE status_pemesanan = 'Diproses') active
             ON m.id_meja = active.id_meja
             SET m.status_meja = 'Terisi'"
        );

        // Meja yang statusnya 'Terisi' tapi tidak memiliki pesanan Diproses -> Tersedia
        $this->db->query(
            "UPDATE {$this->table} m
             LEFT JOIN (SELECT DISTINCT id_meja FROM pemesanan WHERE status_pemesanan = 'Diproses') active
             ON m.id_meja = active.id_meja
             SET m.status_meja = 'Tersedia'
             WHERE m.status_meja = 'Terisi' AND active.id_meja IS NULL"
        );
    }

    /** Hapus meja — cek riwayat pemesanan */
    public function delete(int $id): bool {
        $chk = $this->db->prepare("SELECT COUNT(*) AS cnt FROM pemesanan WHERE id_meja = ?");
        $chk->bind_param('i', $id);
        $chk->execute();
        $cnt = (int)$chk->get_result()->fetch_assoc()['cnt'];
        $chk->close();

        if ($cnt > 0) {
            throw new Exception("Meja tidak dapat dihapus karena memiliki riwayat pemesanan.");
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_meja = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Hitung total meja */
    public function count(): int {
        $row = $this->db->query("SELECT COUNT(*) AS cnt FROM {$this->table}")->fetch_assoc();
        return (int)$row['cnt'];
    }

    /** Validasi input meja */
    public function validate(array $data, ?int $excludeId = null): array {
        $errors = [];

        if (empty(trim($data['nomor_meja'] ?? ''))) {
            $errors['nomor_meja'] = 'Nomor meja wajib diisi.';
        } else {
            // Cek keunikan nomor meja
            if ($excludeId !== null) {
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) AS cnt FROM {$this->table} WHERE nomor_meja = ? AND id_meja != ?"
                );
                $nomor = trim($data['nomor_meja']);
                $stmt->bind_param('si', $nomor, $excludeId);
            } else {
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) AS cnt FROM {$this->table} WHERE nomor_meja = ?"
                );
                $nomor = trim($data['nomor_meja']);
                $stmt->bind_param('s', $nomor);
            }
            $stmt->execute();
            $cnt = (int)$stmt->get_result()->fetch_assoc()['cnt'];
            $stmt->close();

            if ($cnt > 0) {
                $errors['nomor_meja'] = 'Nomor meja sudah digunakan. Masukkan nomor meja lain.';
            }
        }

        if ((int)($data['kapasitas'] ?? 0) <= 0) {
            $errors['kapasitas'] = 'Kapasitas meja harus lebih dari 0.';
        }

        $validStatus = ['Tersedia', 'Terisi', 'Reservasi', 'Perbaikan'];
        if (!empty($data['status_meja']) && !in_array($data['status_meja'], $validStatus, true)) {
            $errors['status_meja'] = 'Status meja tidak valid.';
        }

        return $errors;
    }

    /**
     * Polymorphism: Representasi ringkas objek meja
     */
    public function getSummaryInfo(int $id): string {
        $data = $this->getById($id);
        if (!$data) return 'Meja tidak ditemukan';
        return "Meja: {$data['nomor_meja']} (Kapasitas: {$data['kapasitas']} org, Status: {$data['status_meja']})";
    }
}
