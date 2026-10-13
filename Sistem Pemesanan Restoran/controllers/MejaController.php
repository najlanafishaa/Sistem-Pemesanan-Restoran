<?php
/**
 * Controller Meja
 * Mengelola data meja, kapasitas, lokasi, dan status operasional meja restoran.
 */

require_once __DIR__ . '/../models/Meja.php';
require_once __DIR__ . '/../config/helpers.php';

class MejaController {
    private Meja $mejaModel;

    public function __construct() {
        $this->mejaModel = new Meja();
    }

    /**
     * Menampilkan daftar seluruh meja
     */
    public function index(): void {
        $mejaList = $this->mejaModel->getAll();
        $pageTitle = 'Manajemen Meja Restoran';
        require_once __DIR__ . '/../views/meja/index.php';
    }

    /**
     * Menampilkan form atau memproses tambah meja baru
     */
    public function create(): void {
        $errors = [];
        $data = [
            'nomor_meja'  => '',
            'kapasitas'   => '4',
            'status_meja' => 'Tersedia',
            'lokasi'      => 'Indoor',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid.');
                redirect(baseUrl('index.php?page=meja'));
            }

            $data = [
                'nomor_meja'  => sanitize($_POST['nomor_meja'] ?? ''),
                'kapasitas'   => (int)($_POST['kapasitas'] ?? 0),
                'status_meja' => sanitize($_POST['status_meja'] ?? 'Tersedia'),
                'lokasi'      => sanitize($_POST['lokasi'] ?? 'Indoor'),
            ];

            $errors = $this->mejaModel->validate($data);

            if (empty($errors)) {
                try {
                    $this->mejaModel->create($data);
                    setFlash('success', 'Meja baru berhasil ditambahkan!');
                    redirect(baseUrl('index.php?page=meja'));
                } catch (Exception $e) {
                    $errors['general'] = 'Gagal menyimpan meja: ' . $e->getMessage();
                }
            }
        }

        $pageTitle = 'Tambah Meja Baru';
        require_once __DIR__ . '/../views/meja/create.php';
    }

    /**
     * Menampilkan form atau memproses update meja
     */
    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $meja = $this->mejaModel->getById($id);

        if (!$meja) {
            setFlash('danger', 'Data meja tidak ditemukan.');
            redirect(baseUrl('index.php?page=meja'));
        }

        $errors = [];
        $data = $meja;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid.');
                redirect(baseUrl('index.php?page=meja'));
            }

            $data = [
                'nomor_meja'  => sanitize($_POST['nomor_meja'] ?? ''),
                'kapasitas'   => (int)($_POST['kapasitas'] ?? 0),
                'status_meja' => sanitize($_POST['status_meja'] ?? 'Tersedia'),
                'lokasi'      => sanitize($_POST['lokasi'] ?? 'Indoor'),
            ];

            $errors = $this->mejaModel->validate($data, $id);

            if (empty($errors)) {
                try {
                    $this->mejaModel->update($id, $data);
                    setFlash('success', 'Data meja berhasil diperbarui!');
                    redirect(baseUrl('index.php?page=meja'));
                } catch (Exception $e) {
                    $errors['general'] = 'Gagal memperbarui meja: ' . $e->getMessage();
                }
            }
        }

        $pageTitle = 'Edit Data Meja';
        require_once __DIR__ . '/../views/meja/edit.php';
    }

    /**
     * Menghapus data meja
     */
    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Token CSRF tidak valid.');
                redirect(baseUrl('index.php?page=meja'));
            }

            $id = (int)($_POST['id'] ?? 0);
            try {
                $this->mejaModel->delete($id);
                setFlash('success', 'Data meja berhasil dihapus.');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        }

        redirect(baseUrl('index.php?page=meja'));
    }
}
