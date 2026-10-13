<?php
/**
 * Model Pemesanan (MySQLi)
 * Mengelola reservasi meja: transaksi atomik, validasi kapasitas,
 * pencegahan tabrakan jadwal, dan kalkulasi total harga.
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/DetailPemesanan.php';
require_once __DIR__ . '/Meja.php';
require_once __DIR__ . '/Menu.php';

class Pemesanan extends BaseModel {
    protected string $table = 'pemesanan';
    protected string $primaryKey = 'id_pemesanan';

    public function __construct() {
        parent::__construct();
    }

    // ---------------------------------------------------------------
    // READ
    // ---------------------------------------------------------------

    /** Ambil daftar pemesanan dengan filter opsional */
    public function getAll(
        ?string $status = null,
        ?string $tanggalDari = null,
        ?string $tanggalSampai = null
    ): array {
        $sql = "SELECT p.*,
                       pel.nama_pelanggan, pel.no_telepon, pel.email,
                       m.nomor_meja, m.kapasitas, m.lokasi
                FROM {$this->table} p
                JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
                JOIN meja m ON p.id_meja = m.id_meja
                WHERE 1=1";

        $types  = '';
        $params = [];

        if (!empty($status)) {
            $sql   .= " AND p.status_pemesanan = ?";
            $types .= 's';
            $params[] = $status;
        }
        if (!empty($tanggalDari)) {
            $sql   .= " AND p.tanggal_pemesanan >= ?";
            $types .= 's';
            $params[] = $tanggalDari;
        }
        if (!empty($tanggalSampai)) {
            $sql   .= " AND p.tanggal_pemesanan <= ?";
            $types .= 's';
            $params[] = $tanggalSampai;
        }

        $sql .= " ORDER BY p.tanggal_pemesanan DESC, p.waktu_mulai DESC";

        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** Ambil ringkasan pemesanan terbaru untuk dashboard */
    public function getRecent(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    pel.nama_pelanggan, pel.no_telepon,
                    m.nomor_meja, m.kapasitas
             FROM {$this->table} p
             JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
             JOIN meja m ON p.id_meja = m.id_meja
             ORDER BY p.id_pemesanan DESC
             LIMIT ?"
        );
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** Ambil pemesanan berdasarkan ID beserta detail item menu */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    pel.nama_pelanggan, pel.no_telepon, pel.email, pel.alamat,
                    m.nomor_meja, m.kapasitas, m.lokasi, m.status_meja
             FROM {$this->table} p
             JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
             JOIN meja m ON p.id_meja = m.id_meja
             WHERE p.id_pemesanan = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return null;
        }

        $detailModel = new DetailPemesanan();
        $row['items'] = $detailModel->getByPemesananId($id);
        return $row;
    }

    // ---------------------------------------------------------------
    // BUSINESS RULES
    // ---------------------------------------------------------------

    /**
     * Cek tabrakan waktu pada meja yang sama.
     * Interval tumpang tindih: (mulai_baru < selesai_lama) DAN (selesai_baru > mulai_lama)
     * @return bool true jika konflik
     */
    public function checkConflict(
        int $idMeja,
        string $tanggal,
        string $waktuMulai,
        string $waktuSelesai,
        ?int $excludeId = null
    ): bool {
        $sql = "SELECT COUNT(*) AS cnt
                FROM {$this->table}
                WHERE id_meja = ?
                  AND tanggal_pemesanan = ?
                  AND status_pemesanan != 'Dibatalkan'
                  AND (? < waktu_selesai AND ? > waktu_mulai)";

        $types  = 'isss';
        $params = [$idMeja, $tanggal, $waktuMulai, $waktuSelesai];

        if ($excludeId !== null) {
            $sql   .= " AND id_pemesanan != ?";
            $types .= 'i';
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $cnt = (int)$stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();
        return $cnt > 0;
    }

    /** Validasi kapasitas meja */
    public static function validateCapacity(int $jumlahOrang, int $kapasitasMeja): bool {
        return ($jumlahOrang > 0) && ($jumlahOrang <= $kapasitasMeja);
    }

    /** Generate kode pemesanan unik: ORD-YYYYMMDD-XXX */
    public function generateKodePemesanan(string $tanggal): string {
        $dateFormatted = date('Ymd', strtotime($tanggal));
        $prefix        = "ORD-{$dateFormatted}-";

        $stmt = $this->db->prepare(
            "SELECT kode_pemesanan FROM {$this->table}
             WHERE kode_pemesanan LIKE ?
             ORDER BY id_pemesanan DESC LIMIT 1"
        );
        $like = $prefix . '%';
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $row      = $stmt->get_result()->fetch_assoc();
        $lastCode = $row ? $row['kode_pemesanan'] : null;
        $stmt->close();

        $newNumber = $lastCode ? ((int)substr($lastCode, -3) + 1) : 1;
        return $prefix . str_pad((string)$newNumber, 3, '0', STR_PAD_LEFT);
    }

    // ---------------------------------------------------------------
    // CREATE  (dengan Database Transaction MySQLi)
    // ---------------------------------------------------------------

    /**
     * Buat pemesanan baru + detail menu dalam satu transaksi atomik.
     * @throws Exception
     */
    public function createWithItems(array $data, array $items = []): int {
        // 1. Generate kode jika belum ada
        if (empty($data['kode_pemesanan'])) {
            $data['kode_pemesanan'] = $this->generateKodePemesanan($data['tanggal_pemesanan'] ?? date('Y-m-d'));
        }

        // 2. Mulai transaksi MySQLi
        $this->db->begin_transaction();

        try {
            $idMeja  = (int)$data['id_meja'];
            $statusP = !empty($data['status_pemesanan']) ? $data['status_pemesanan'] : 'Diproses';

            // PENCEGAHAN RACE CONDITION:
            // Kunci baris meja dengan FOR UPDATE untuk mencegah dua kasir memesan meja yang sama secara bersamaan
            $stmtLock = $this->db->prepare("SELECT id_meja, nomor_meja, status_meja FROM meja WHERE id_meja = ? FOR UPDATE");
            $stmtLock->bind_param('i', $idMeja);
            $stmtLock->execute();
            $mejaLocked = $stmtLock->get_result()->fetch_assoc();
            $stmtLock->close();

            if (!$mejaLocked) {
                throw new Exception("Meja yang dipilih tidak ditemukan dalam sistem.");
            }

            // Validasi di sisi server dalam lock transaksi:
            // Cek apakah ada pesanan aktif (Diproses) yang sedang menggunakan meja ini
            $stmtCheck = $this->db->prepare(
                "SELECT COUNT(*) AS active_cnt FROM {$this->table} WHERE id_meja = ? AND status_pemesanan = 'Diproses' FOR UPDATE"
            );
            $stmtCheck->bind_param('i', $idMeja);
            $stmtCheck->execute();
            $activeCount = (int)$stmtCheck->get_result()->fetch_assoc()['active_cnt'];
            $stmtCheck->close();

            if ($statusP === 'Diproses' && ($activeCount > 0 || $mejaLocked['status_meja'] === 'Terisi')) {
                throw new Exception("Meja {$mejaLocked['nomor_meja']} sedang digunakan oleh pesanan lain yang aktif. Silakan pilih meja lain.");
            }

            // Simpan header pemesanan
            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table}
                 (kode_pemesanan, id_pelanggan, id_meja, tanggal_pemesanan,
                  waktu_mulai, waktu_selesai, jumlah_orang, status_pemesanan, total_harga, catatan)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.00, ?)"
            );
            $kode         = $data['kode_pemesanan'];
            $idPelanggan  = (int)$data['id_pelanggan'];
            $tgl          = !empty($data['tanggal_pemesanan']) ? $data['tanggal_pemesanan'] : date('Y-m-d');
            $wMulai       = !empty($data['waktu_mulai']) ? $data['waktu_mulai'] : date('H:i');
            $wSelesai     = !empty($data['waktu_selesai']) ? $data['waktu_selesai'] : date('H:i', strtotime('+2 hours'));
            $jmlOrang     = (int)($data['jumlah_orang'] ?? 1);
            $catatan      = !empty($data['catatan']) ? trim($data['catatan']) : null;

            $stmt->bind_param('siisssiss', $kode, $idPelanggan, $idMeja, $tgl, $wMulai, $wSelesai, $jmlOrang, $statusP, $catatan);
            $stmt->execute();
            $idPemesanan = (int)$this->db->insert_id;
            $stmt->close();

            // Simpan detail menu
            $detailModel = new DetailPemesanan();
            $menuModel   = new Menu();
            $subtotalSemua = 0.00;

            foreach ($items as $item) {
                $idMenu = (int)($item['id_menu'] ?? 0);
                $qty    = (int)($item['jumlah'] ?? 0);
                if ($idMenu > 0 && $qty > 0) {
                    $menu = $menuModel->getById($idMenu);
                    if ($menu) {
                        $hargaSatuan = (float)$menu['harga'];
                        $subtotal    = DetailPemesanan::calculateSubtotal($hargaSatuan, $qty);
                        $subtotalSemua += $subtotal;
                        $detailModel->create([
                            'id_pemesanan'   => $idPemesanan,
                            'id_menu'        => $idMenu,
                            'jumlah'         => $qty,
                            'harga_satuan'   => $hargaSatuan,
                            'catatan_khusus' => $item['catatan'] ?? null,
                        ]);
                    }
                }
            }

            // Hitung total dengan rumus: Subtotal + Layanan (5%) + Pajak (10%)
            $financials = calculateOrderFinancials($subtotalSemua);
            $totalBayar = $financials['total_pembayaran'];

            // Update total harga
            $stmtTot = $this->db->prepare(
                "UPDATE {$this->table} SET total_harga = ? WHERE id_pemesanan = ?"
            );
            $stmtTot->bind_param('di', $totalBayar, $idPemesanan);
            $stmtTot->execute();
            $stmtTot->close();

            $this->db->commit();

            // Sinkronkan status meja: jika status pesanan 'Diproses', ubah meja menjadi 'Terisi'
            $mejaModel = new Meja();
            $mejaModel->syncStatusMeja($idMeja);

            return $idPemesanan;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // ---------------------------------------------------------------
    // UPDATE (dengan Database Transaction MySQLi)
    // ---------------------------------------------------------------

    /** Perbarui pemesanan + detail menu dalam transaksi atomik. */
    public function updateWithItems(int $id, array $data, array $items = []): bool {
        // Ambil data pemesanan saat ini untuk mengetahui meja lama
        $curPemesanan = $this->getById($id);
        $oldMejaId = $curPemesanan ? (int)$curPemesanan['id_meja'] : null;

        $this->db->begin_transaction();

        try {
            $idPelanggan = (int)$data['id_pelanggan'];
            $idMeja      = (int)$data['id_meja'];
            $statusP     = $data['status_pemesanan'];

            // PENCEGAHAN RACE CONDITION PADA EDIT:
            // Kunci baris meja dengan FOR UPDATE
            $stmtLock = $this->db->prepare("SELECT id_meja, nomor_meja, status_meja FROM meja WHERE id_meja = ? FOR UPDATE");
            $stmtLock->bind_param('i', $idMeja);
            $stmtLock->execute();
            $mejaLocked = $stmtLock->get_result()->fetch_assoc();
            $stmtLock->close();

            if (!$mejaLocked) {
                throw new Exception("Meja yang dipilih tidak ditemukan dalam sistem.");
            }

            // Jika statusnya 'Diproses', pastikan meja ini tidak dipakai oleh pesanan Diproses LAIN
            if ($statusP === 'Diproses') {
                $stmtCheck = $this->db->prepare(
                    "SELECT COUNT(*) AS active_cnt FROM {$this->table} WHERE id_meja = ? AND status_pemesanan = 'Diproses' AND id_pemesanan != ? FOR UPDATE"
                );
                $stmtCheck->bind_param('ii', $idMeja, $id);
                $stmtCheck->execute();
                $activeCount = (int)$stmtCheck->get_result()->fetch_assoc()['active_cnt'];
                $stmtCheck->close();

                if ($activeCount > 0) {
                    throw new Exception("Meja {$mejaLocked['nomor_meja']} sedang digunakan oleh pesanan aktif lain.");
                }
            }

            // Update header
            $stmt = $this->db->prepare(
                "UPDATE {$this->table}
                 SET id_pelanggan = ?, id_meja = ?, tanggal_pemesanan = ?,
                     waktu_mulai = ?, waktu_selesai = ?, jumlah_orang = ?,
                     status_pemesanan = ?, catatan = ?
                 WHERE id_pemesanan = ?"
            );
            $tgl         = $data['tanggal_pemesanan'];
            $wMulai      = $data['waktu_mulai'];
            $wSelesai    = $data['waktu_selesai'];
            $jmlOrang    = (int)$data['jumlah_orang'];
            $catatan     = !empty($data['catatan']) ? trim($data['catatan']) : null;

            $stmt->bind_param('iisssissi', $idPelanggan, $idMeja, $tgl, $wMulai, $wSelesai, $jmlOrang, $statusP, $catatan, $id);
            $stmt->execute();
            $stmt->close();

            // Hapus detail lama, simpan detail baru
            $detailModel = new DetailPemesanan();
            $menuModel   = new Menu();
            $detailModel->deleteByPemesananId($id);

            $subtotalSemua = 0.00;
            foreach ($items as $item) {
                $idMenu = (int)($item['id_menu'] ?? 0);
                $qty    = (int)($item['jumlah'] ?? 0);
                if ($idMenu > 0 && $qty > 0) {
                    $menu = $menuModel->getById($idMenu);
                    if ($menu) {
                        $hargaSatuan = (float)$menu['harga'];
                        $subtotal    = DetailPemesanan::calculateSubtotal($hargaSatuan, $qty);
                        $subtotalSemua += $subtotal;
                        $detailModel->create([
                            'id_pemesanan'   => $id,
                            'id_menu'        => $idMenu,
                            'jumlah'         => $qty,
                            'harga_satuan'   => $hargaSatuan,
                            'catatan_khusus' => $item['catatan'] ?? null,
                        ]);
                    }
                }
            }

            // Hitung total dengan rumus: Subtotal + Layanan (5%) + Pajak (10%)
            $financials = calculateOrderFinancials($subtotalSemua);
            $totalBayar = $financials['total_pembayaran'];

            // Update total harga
            $stmtTot = $this->db->prepare(
                "UPDATE {$this->table} SET total_harga = ? WHERE id_pemesanan = ?"
            );
            $stmtTot->bind_param('di', $totalBayar, $id);
            $stmtTot->execute();
            $stmtTot->close();

            $this->db->commit();

            // Sinkronkan status meja lama dan baru
            $mejaModel = new Meja();
            $mejaModel->syncStatusMeja($idMeja);
            if ($oldMejaId && $oldMejaId !== $idMeja) {
                $mejaModel->syncStatusMeja($oldMejaId);
            }

            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // ---------------------------------------------------------------
    // STATUS, DELETE, COUNT
    // ---------------------------------------------------------------

    /** Perbarui status pemesanan */
    public function updateStatus(int $id, string $status): bool {
        $validStatus = ['Diproses', 'Selesai', 'Dibatalkan'];
        if (!in_array($status, $validStatus, true)) {
            throw new Exception("Status pemesanan tidak valid.");
        }

        $pemesanan = $this->getById($id);
        $idMeja = $pemesanan ? (int)$pemesanan['id_meja'] : null;

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status_pemesanan = ? WHERE id_pemesanan = ?"
        );
        $stmt->bind_param('si', $status, $id);
        $ok = $stmt->execute();
        $stmt->close();

        // Sinkronkan status meja: jika selesai atau dibatalkan, meja kembali 'Tersedia'
        if ($idMeja) {
            $mejaModel = new Meja();
            $mejaModel->syncStatusMeja($idMeja);
        }

        return $ok;
    }

    /** Hapus pemesanan (detail dihapus otomatis via ON DELETE CASCADE) */
    public function delete(int $id): bool {
        $pemesanan = $this->getById($id);
        $idMeja = $pemesanan ? (int)$pemesanan['id_meja'] : null;

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_pemesanan = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();

        // Sinkronkan status meja
        if ($idMeja) {
            $mejaModel = new Meja();
            $mejaModel->syncStatusMeja($idMeja);
        }

        return $ok;
    }

    /** Hitung total pemesanan */
    public function count(): int {
        $row = $this->db->query("SELECT COUNT(*) AS cnt FROM {$this->table}")->fetch_assoc();
        return (int)$row['cnt'];
    }

    /** Hitung pemesanan berdasarkan status */
    public function countByStatus(string $status): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM {$this->table} WHERE status_pemesanan = ?"
        );
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $cnt = (int)$stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();
        return $cnt;
    }

    /** Hitung total pendapatan dari pemesanan Selesai */
    public function getTotalPendapatan(): float {
        $row = $this->db->query(
            "SELECT COALESCE(SUM(total_harga), 0) AS total
             FROM {$this->table}
             WHERE status_pemesanan = 'Selesai'"
        )->fetch_assoc();
        return (float)$row['total'];
    }

    /**
     * Polymorphism: Representasi ringkas objek pemesanan
     */
    public function getSummaryInfo(int $id): string {
        $data = $this->getById($id);
        if (!$data) return 'Pemesanan tidak ditemukan';
        return "Reservasi #{$data['kode_pemesanan']} - Meja {$data['nomor_meja']} ({$data['nama_pelanggan']}, Status: {$data['status_pemesanan']})";
    }
}
