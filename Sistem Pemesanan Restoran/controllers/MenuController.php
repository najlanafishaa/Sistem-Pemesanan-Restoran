<?php
/**
 * Controller Menu
 * Mengelola daftar makanan, minuman, hidangan penutup, harga, dan ketersediaan.
 */

require_once __DIR__ . '/../models/Menu.php';
require_once __DIR__ . '/../config/helpers.php';

class MenuController {
    private Menu $menuModel;

    public function __construct() {
        $this->menuModel = new Menu();
    }

    /**
     * Menampilkan daftar menu dengan opsi filter kategori
     */
    public function index(): void {
        $kategori = !empty($_GET['kategori']) ? sanitize($_GET['kategori']) : null;
        $menuList = $this->menuModel->getAll($kategori);
        $pageTitle = 'Manajemen Menu Makanan & Minuman';
        require_once __DIR__ . '/../views/menu/index.php';
    }

    /**
     * Menampilkan form atau memproses penambahan menu baru
     */
    public function create(): void {
        $errors = [];
        $data = [
            'nama_menu'   => '',
            'kategori'    => 'Makanan',
            'harga'       => '',
            'status_menu' => 'Tersedia',
            'deskripsi'   => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid.');
                redirect(baseUrl('index.php?page=menu'));
            }

            $data = [
                'nama_menu'   => sanitize($_POST['nama_menu'] ?? ''),
                'kategori'    => sanitize($_POST['kategori'] ?? ''),
                'harga'       => str_replace(['.', ','], ['', '.'], sanitize($_POST['harga'] ?? '')),
                'status_menu' => sanitize($_POST['status_menu'] ?? 'Tersedia'),
                'deskripsi'   => sanitize($_POST['deskripsi'] ?? ''),
            ];

            $errors = Menu::validate($data);

            if (empty($errors)) {
                try {
                    $this->menuModel->create($data);
                    setFlash('success', 'Menu baru berhasil ditambahkan!');
                    redirect(baseUrl('index.php?page=menu'));
                } catch (Exception $e) {
                    $errors['general'] = 'Gagal menyimpan menu: ' . $e->getMessage();
                }
            }
        }

        $pageTitle = 'Tambah Menu Baru';
        require_once __DIR__ . '/../views/menu/create.php';
    }

    /**
     * Menampilkan form atau memproses pembaruan menu
     */
    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $menu = $this->menuModel->getById($id);

        if (!$menu) {
            setFlash('danger', 'Data menu tidak ditemukan.');
            redirect(baseUrl('index.php?page=menu'));
        }

        $errors = [];
        $data = $menu;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Sesi tidak valid.');
                redirect(baseUrl('index.php?page=menu'));
            }

            $data = [
                'nama_menu'   => sanitize($_POST['nama_menu'] ?? ''),
                'kategori'    => sanitize($_POST['kategori'] ?? ''),
                'harga'       => str_replace(['.', ','], ['', '.'], sanitize($_POST['harga'] ?? '')),
                'status_menu' => sanitize($_POST['status_menu'] ?? 'Tersedia'),
                'deskripsi'   => sanitize($_POST['deskripsi'] ?? ''),
            ];

            $errors = Menu::validate($data);

            if (empty($errors)) {
                try {
                    $this->menuModel->update($id, $data);
                    setFlash('success', 'Data menu berhasil diperbarui!');
                    redirect(baseUrl('index.php?page=menu'));
                } catch (Exception $e) {
                    $errors['general'] = 'Gagal memperbarui menu: ' . $e->getMessage();
                }
            }
        }

        $pageTitle = 'Edit Data Menu';
        require_once __DIR__ . '/../views/menu/edit.php';
    }

    /**
     * Menghapus data menu
     */
    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('danger', 'Token CSRF tidak valid.');
                redirect(baseUrl('index.php?page=menu'));
            }

            $id = (int)($_POST['id'] ?? 0);
            try {
                $this->menuModel->delete($id);
                setFlash('success', 'Menu berhasil dihapus.');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        }

        redirect(baseUrl('index.php?page=menu'));
    }
}
