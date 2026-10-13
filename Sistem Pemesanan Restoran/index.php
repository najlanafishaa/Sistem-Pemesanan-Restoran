<?php
/**
 * Titik Masuk Utama Aplikasi (Main Entry Point & Router)
 * Sistem Pemesanan Restoran - PHP Native
 * 
 * Mengatur routing request berdasarkan parameter query string 'page' dan 'action'.
 */

// Mulai sesi aplikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Muat konfigurasi dan helper
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/Database.php';

// Muat controller
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/PelangganController.php';
require_once __DIR__ . '/controllers/MejaController.php';
require_once __DIR__ . '/controllers/MenuController.php';
require_once __DIR__ . '/controllers/PemesananController.php';
require_once __DIR__ . '/controllers/LaporanController.php';

// Ambil parameter rute (Default: Dashboard Utama)
$page   = sanitize($_GET['page'] ?? 'dashboard');
$action = sanitize($_GET['action'] ?? 'index');

// Dispatching request
switch ($page) {
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'pelanggan':
        $controller = new PelangganController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'index':
            default:
                $controller->index();
                break;
        }
        break;

    case 'meja':
        $controller = new MejaController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'index':
            default:
                $controller->index();
                break;
        }
        break;

    case 'menu':
        $controller = new MenuController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'index':
            default:
                $controller->index();
                break;
        }
        break;

    case 'pemesanan':
        $controller = new PemesananController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'detail':
                $controller->detail();
                break;
            case 'cetakBukti':
            case 'cetak':
                $controller->cetakBukti();
                break;
            case 'updateStatus':
                $controller->updateStatus();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'index':
            default:
                $controller->index();
                break;
        }
        break;

    case 'laporan':
        $controller = new LaporanController();
        switch ($action) {
            case 'cetak':
                $controller->cetak();
                break;
            case 'index':
            default:
                $controller->index();
                break;
        }
        break;

    default:
        // Halaman 404 Tidak Ditemukan
        http_response_code(404);
        $pageTitle = 'Halaman Tidak Ditemukan';
        require_once __DIR__ . '/views/layout/header.php';
        echo '<div class="text-center py-5">
                <i class="bi bi-exclamation-triangle text-warning display-1"></i>
                <h2 class="mt-3 fw-bold">404 - Halaman Tidak Ditemukan</h2>
                <p class="text-muted">Halaman atau fitur yang Anda cari tidak tersedia di sistem ini.</p>
                <a href="' . baseUrl('index.php?page=dashboard') . '" class="btn btn-primary mt-2">
                    <i class="bi bi-house-door me-1"></i> Kembali ke Dashboard
                </a>
              </div>';
        require_once __DIR__ . '/views/layout/footer.php';
        break;
}
