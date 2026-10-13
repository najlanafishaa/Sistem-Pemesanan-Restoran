<?php
/**
 * Controller Pelanggan
 * Menangani operasi bisnis manajemen pelanggan (tambah, edit, lihat, hapus, validasi).
 */

require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../config/helpers.php';

class PelangganController {
    private Pelanggan $pelangganModel;

    public function __construct() {
        $this->pelangganModel = new Pelanggan();
    }

    /**
     * Menampilkan daftar semua pelanggan
     */
    public function index(): void {
        $pelangganList = $this->pelangganModel->getAll();
        $pageTitle = 'Manajemen Pelanggan';
        require_once __DIR__ . '/../views/pelanggan/index.php';
    }

    /**
     * Menampilkan form atau memproses penambahan pelanggan baru
     */
    public function create(): void {
        $errors = [];
        $data = [
            'nama_pelanggan' => '',
            'no_telepon'     => '',
            'email'          => '',
            'alamat'         => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid atau permintaan kedaluwarsa.');
                redirect(baseUrl('index.php?page=pelanggan'));
            }

            $data = [
                'nama_pelanggan' => sanitize($_POST['nama_pelanggan'] ?? ''),
                'no_telepon'     => sanitize($_POST['no_telepon'] ?? ''),
                'email'          => sanitize($_POST['email'] ?? ''),
                'alamat'         => sanitize($_POST['alamat'] ?? ''),
            ];

            $errors = Pelanggan::validate($data);

            if (empty($errors)) {
                try {
                    $this->pelangganModel->create($data);
                    setFlash('success', 'Data pelanggan berhasil ditambahkan!');
                    redirect(baseUrl('index.php?page=pelanggan'));
                } catch (Exception $e) {
                    $errors['general'] = 'Gagal menyimpan data: ' . $e->getMessage();
                }
            }
        }

        $pageTitle = 'Tambah Pelanggan Baru';
        require_once __DIR__ . '/../views/pelanggan/create.php';
    }

    /**
     * Menampilkan form atau memproses perubahan data pelanggan
     */
    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $pelanggan = $this->pelangganModel->getById($id);

        if (!$pelanggan) {
            setFlash('danger', 'Data pelanggan tidak ditemukan.');
            redirect(baseUrl('index.php?page=pelanggan'));
        }

        $errors = [];
        $data = $pelanggan;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid atau permintaan kedaluwarsa.');
                redirect(baseUrl('index.php?page=pelanggan'));
            }

            $data = [
                'nama_pelanggan' => sanitize($_POST['nama_pelanggan'] ?? ''),
                'no_telepon'     => sanitize($_POST['no_telepon'] ?? ''),
                'email'          => sanitize($_POST['email'] ?? ''),
                'alamat'         => sanitize($_POST['alamat'] ?? ''),
            ];

            $errors = Pelanggan::validate($data);

            if (empty($errors)) {
                try {
                    $this->pelangganModel->update($id, $data);
                    setFlash('success', 'Data pelanggan berhasil diperbarui!');
                    redirect(baseUrl('index.php?page=pelanggan'));
                } catch (Exception $e) {
                    $errors['general'] = 'Gagal memperbarui data: ' . $e->getMessage();
                }
            }
        }

        $pageTitle = 'Edit Data Pelanggan';
        require_once __DIR__ . '/../views/pelanggan/edit.php';
    }

    /**
     * Menghapus data pelanggan
     */
    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Token CSRF tidak valid.');
                redirect(baseUrl('index.php?page=pelanggan'));
            }

            $id = (int)($_POST['id'] ?? 0);
            try {
                $this->pelangganModel->delete($id);
                setFlash('success', 'Data pelanggan berhasil dihapus.');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        }

        redirect(baseUrl('index.php?page=pelanggan'));
    }
}
