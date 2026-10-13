<?php
/**
 * Controller Pemesanan
 * Mengelola proses reservasi meja, penambahan pesanan menu makanan/minuman,
 * validasi kapasitas meja, pencegahan tabrakan jadwal, dan transaksi keuangan.
 */

require_once __DIR__ . '/../models/Pemesanan.php';
require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Meja.php';
require_once __DIR__ . '/../models/Menu.php';
require_once __DIR__ . '/../models/DetailPemesanan.php';
require_once __DIR__ . '/../config/helpers.php';

class PemesananController {
    private Pemesanan $pemesananModel;
    private Pelanggan $pelangganModel;
    private Meja $mejaModel;
    private Menu $menuModel;

    public function __construct() {
        $this->pemesananModel = new Pemesanan();
        $this->pelangganModel = new Pelanggan();
        $this->mejaModel = new Meja();
        $this->menuModel = new Menu();
    }

    /**
     * Menampilkan daftar pemesanan dengan filter
     */
    public function index(): void {
        // Sinkronkan status meja agar status meja selalu terhubung dengan pesanan aktif
        $this->mejaModel->syncSemuaStatusMeja();

        $status = !empty($_GET['status']) ? sanitize($_GET['status']) : null;
        $tanggalDari = !empty($_GET['tanggal_dari']) ? sanitize($_GET['tanggal_dari']) : null;
        $tanggalSampai = !empty($_GET['tanggal_sampai']) ? sanitize($_GET['tanggal_sampai']) : null;

        $pemesananList = $this->pemesananModel->getAll($status, $tanggalDari, $tanggalSampai);

        // Hitung jumlah untuk tiga kotak ringkasan status sederhana
        $statusDiproses   = $this->pemesananModel->countByStatus('Diproses');
        $statusSelesai    = $this->pemesananModel->countByStatus('Selesai');
        $statusDibatalkan = $this->pemesananModel->countByStatus('Dibatalkan');

        $pageTitle = 'Daftar Pemesanan Restoran';
        require_once __DIR__ . '/../views/pemesanan/index.php';
    }

    /**
     * Menampilkan rincian lengkap sebuah pemesanan (Meja, Pelanggan, Menu, Status)
     */
    public function detail(): void {
        $id = (int)($_GET['id'] ?? 0);
        $pemesanan = $this->pemesananModel->getById($id);

        if (!$pemesanan) {
            setFlash('danger', 'Data pemesanan tidak ditemukan.');
            redirect(baseUrl('index.php?page=pemesanan'));
        }

        $pageTitle = 'Detail Pemesanan ' . $pemesanan['kode_pemesanan'];
        require_once __DIR__ . '/../views/pemesanan/detail.php';
    }

    /**
     * Menampilkan bukti pemesanan kasir siap cetak (Struk / Nota Pembayaran)
     */
    public function cetakBukti(): void {
        $id = (int)($_GET['id'] ?? 0);
        $pemesanan = $this->pemesananModel->getById($id);

        if (!$pemesanan) {
            setFlash('danger', 'Data pemesanan tidak ditemukan.');
            redirect(baseUrl('index.php?page=pemesanan'));
        }

        $pageTitle = 'Bukti Pemesanan Kasir - ' . $pemesanan['kode_pemesanan'];
        require_once __DIR__ . '/../views/pemesanan/bukti.php';
    }

    /**
     * Menampilkan form atau memproses pembuatan pemesanan baru
     */
    public function create(): void {
        // Sinkronkan status meja terlebih dahulu agar data selalu akurat
        $this->mejaModel->syncSemuaStatusMeja();

        $pelangganList = $this->pelangganModel->getAll();
        $mejaList = $this->mejaModel->getAll();
        $menuList = $this->menuModel->getAvailable();

        $errors = [];
        $data = [
            'id_pelanggan'      => '',
            'id_meja'           => '',
            'tanggal_pemesanan' => date('Y-m-d'),
            'waktu_mulai'       => date('H:i'),
            'waktu_selesai'     => date('H:i', strtotime('+2 hour')),
            'jumlah_orang'      => 1,
            'status_pemesanan'  => 'Diproses',
            'catatan'           => '',
        ];
        $selectedItems = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid.');
                redirect(baseUrl('index.php?page=pemesanan'));
            }

            // Opsi 1: Cek apakah kasir memilih menambahkan pelanggan baru secara langsung
            $tipePelanggan = sanitize($_POST['tipe_pelanggan'] ?? 'existing');
            $idPelanggan = (int)($_POST['id_pelanggan'] ?? 0);

            if ($tipePelanggan === 'new') {
                $newPelangganData = [
                    'nama_pelanggan' => sanitize($_POST['nama_pelanggan_baru'] ?? ''),
                    'no_telepon'     => sanitize($_POST['no_telepon_baru'] ?? ''),
                    'email'          => sanitize($_POST['email_baru'] ?? ''),
                    'alamat'         => sanitize($_POST['alamat_baru'] ?? ''),
                ];

                $pelErrors = Pelanggan::validate($newPelangganData);
                if (!empty($pelErrors)) {
                    $errors = array_merge($errors, $pelErrors);
                } else {
                    try {
                        $idPelanggan = $this->pelangganModel->create($newPelangganData);
                        // Refresh daftar pelanggan
                        $pelangganList = $this->pelangganModel->getAll();
                    } catch (Exception $e) {
                        $errors['id_pelanggan'] = 'Gagal menyimpan pelanggan baru: ' . $e->getMessage();
                    }
                }
            }

            $idMeja = (int)($_POST['id_meja'] ?? 0);

            $data = [
                'id_pelanggan'      => $idPelanggan,
                'id_meja'           => $idMeja,
                'tanggal_pemesanan' => !empty($_POST['tanggal_pemesanan']) ? sanitize($_POST['tanggal_pemesanan']) : date('Y-m-d'),
                'waktu_mulai'       => date('H:i'),
                'waktu_selesai'     => date('H:i', strtotime('+2 hours')),
                'jumlah_orang'      => (int)($_POST['jumlah_orang'] ?? 1),
                'status_pemesanan'  => sanitize($_POST['status_pemesanan'] ?? 'Diproses'),
                'catatan'           => sanitize($_POST['catatan'] ?? ''),
            ];

            // Parsing menu items dari form
            $rawMenus = $_POST['menu_id'] ?? [];
            $rawQty = $_POST['menu_qty'] ?? [];
            $rawNotes = $_POST['menu_notes'] ?? [];

            if (is_array($rawMenus)) {
                for ($i = 0; $i < count($rawMenus); $i++) {
                    $mId = (int)($rawMenus[$i] ?? 0);
                    $qty = (int)($rawQty[$i] ?? 0);
                    $notes = sanitize($rawNotes[$i] ?? '');
                    if ($mId > 0 && $qty > 0) {
                        $selectedItems[] = [
                            'id_menu' => $mId,
                            'jumlah'  => $qty,
                            'catatan' => $notes,
                        ];
                    }
                }
            }

            // Validasi Input Kasir
            if ($data['id_pelanggan'] <= 0 && empty($errors['nama_pelanggan']) && empty($errors['no_telepon'])) {
                $errors['id_pelanggan'] = 'Silakan pilih pelanggan terdaftar atau isi data pelanggan baru.';
            }

            if ($data['id_meja'] <= 0) {
                $errors['id_meja'] = 'Silakan pilih meja yang digunakan.';
            } else {
                // Cek status meja di database: Meja yang sedang digunakan (Terisi) tidak dapat dipilih
                $mejaDipilih = $this->mejaModel->getById($data['id_meja']);
                if ($mejaDipilih && $mejaDipilih['status_meja'] === 'Terisi') {
                    $errors['id_meja'] = "Meja {$mejaDipilih['nomor_meja']} sedang 'Terisi' (memiliki pesanan aktif) dan tidak dapat dipilih.";
                }
            }

            if (empty($selectedItems)) {
                $errors['menu'] = 'Pilih minimal satu menu pesanan.';
            }

            if (empty($errors)) {
                try {
                    $newId = $this->pemesananModel->createWithItems($data, $selectedItems);
                    setFlash('success', 'Pemesanan berhasil disimpan ke sistem kasir!');
                    redirect(baseUrl('index.php?page=pemesanan&action=detail&id=' . $newId));
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }

        $pageTitle = 'Catat Pemesanan Kasir';
        require_once __DIR__ . '/../views/pemesanan/create.php';
    }

    /**
     * Menampilkan form atau memproses edit pemesanan
     */
    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $pemesanan = $this->pemesananModel->getById($id);

        if (!$pemesanan) {
            setFlash('danger', 'Data pemesanan tidak ditemukan.');
            redirect(baseUrl('index.php?page=pemesanan'));
        }

        $pelangganList = $this->pelangganModel->getAll();
        $mejaList = $this->mejaModel->getAll();
        $menuList = $this->menuModel->getAvailable();

        $errors = [];
        $data = [
            'id_pelanggan'      => $pemesanan['id_pelanggan'],
            'id_meja'           => $pemesanan['id_meja'],
            'tanggal_pemesanan' => $pemesanan['tanggal_pemesanan'],
            'waktu_mulai'       => substr($pemesanan['waktu_mulai'], 0, 5),
            'waktu_selesai'     => substr($pemesanan['waktu_selesai'], 0, 5),
            'jumlah_orang'      => $pemesanan['jumlah_orang'],
            'status_pemesanan'  => $pemesanan['status_pemesanan'],
            'catatan'           => $pemesanan['catatan'],
        ];

        $selectedItems = [];
        foreach ($pemesanan['items'] as $item) {
            $selectedItems[] = [
                'id_menu' => $item['id_menu'],
                'jumlah'  => $item['jumlah'],
                'catatan' => $item['catatan_khusus'],
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid.');
                redirect(baseUrl('index.php?page=pemesanan'));
            }

            $data = [
                'id_pelanggan'      => (int)($_POST['id_pelanggan'] ?? 0),
                'id_meja'           => (int)($_POST['id_meja'] ?? 0),
                'tanggal_pemesanan' => sanitize($_POST['tanggal_pemesanan'] ?? ''),
                'waktu_mulai'       => sanitize($_POST['waktu_mulai'] ?? ''),
                'waktu_selesai'     => !empty($_POST['waktu_selesai']) ? sanitize($_POST['waktu_selesai']) : (!empty($_POST['waktu_mulai']) ? date('H:i', strtotime($_POST['waktu_mulai'] . ' +2 hours')) : ''),
                'jumlah_orang'      => (int)($_POST['jumlah_orang'] ?? 0),
                'status_pemesanan'  => sanitize($_POST['status_pemesanan'] ?? 'Menunggu'),
                'catatan'           => sanitize($_POST['catatan'] ?? ''),
            ];

            // Re-parse submitted items
            $rawMenus = $_POST['menu_id'] ?? [];
            $rawQty = $_POST['menu_qty'] ?? [];
            $rawNotes = $_POST['menu_notes'] ?? [];

            $selectedItems = [];
            if (is_array($rawMenus)) {
                for ($i = 0; $i < count($rawMenus); $i++) {
                    $mId = (int)($rawMenus[$i] ?? 0);
                    $qty = (int)($rawQty[$i] ?? 0);
                    $notes = sanitize($rawNotes[$i] ?? '');
                    if ($mId > 0 && $qty > 0) {
                        $selectedItems[] = [
                            'id_menu' => $mId,
                            'jumlah'  => $qty,
                            'catatan' => $notes,
                        ];
                    }
                }
            }

            // Validasi Input
            if ($data['id_pelanggan'] <= 0) {
                $errors['id_pelanggan'] = 'Silakan pilih pelanggan.';
            }

            if ($data['id_meja'] <= 0) {
                $errors['id_meja'] = 'Silakan pilih meja.';
            }

            if (empty($selectedItems)) {
                $errors['menu'] = 'Pilih minimal satu menu pesanan.';
            }

            if (empty($errors)) {
                try {
                    $this->pemesananModel->updateWithItems($id, $data, $selectedItems);
                    setFlash('success', 'Pemesanan berhasil diperbarui!');
                    redirect(baseUrl('index.php?page=pemesanan&action=detail&id=' . $id));
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }

        $pageTitle = 'Edit Pemesanan ' . $pemesanan['kode_pemesanan'];
        require_once __DIR__ . '/../views/pemesanan/edit.php';
    }

    /**
     * Memperbarui status pemesanan secara cepat
     */
    public function updateStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Token CSRF tidak valid.');
                redirect(baseUrl('index.php?page=pemesanan'));
            }

            $id = (int)($_POST['id'] ?? 0);
            $status = sanitize($_POST['status'] ?? '');

            try {
                $this->pemesananModel->updateStatus($id, $status);
                setFlash('success', "Status pemesanan berhasil diubah menjadi: $status");
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }

            $redirectUrl = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : baseUrl('index.php?page=pemesanan');
            redirect($redirectUrl);
        }

        redirect(baseUrl('index.php?page=pemesanan'));
    }

    /**
     * Menghapus pemesanan
     */
    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Token CSRF tidak valid.');
                redirect(baseUrl('index.php?page=pemesanan'));
            }

            $id = (int)($_POST['id'] ?? 0);
            try {
                $this->pemesananModel->delete($id);
                setFlash('success', 'Data pemesanan berhasil dihapus.');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        }

        redirect(baseUrl('index.php?page=pemesanan'));
    }
}
