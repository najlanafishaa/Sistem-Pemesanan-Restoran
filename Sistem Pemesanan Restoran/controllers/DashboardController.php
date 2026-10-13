<?php
/**
 * Controller Dashboard
 * Menampilkan ringkasan data statistik dan pemesanan terbaru.
 */

require_once __DIR__ . '/../models/Meja.php';
require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Menu.php';
require_once __DIR__ . '/../models/Pemesanan.php';

class DashboardController {
    private Meja $mejaModel;
    private Pelanggan $pelangganModel;
    private Menu $menuModel;
    private Pemesanan $pemesananModel;

    public function __construct() {
        $this->mejaModel = new Meja();
        $this->pelangganModel = new Pelanggan();
        $this->menuModel = new Menu();
        $this->pemesananModel = new Pemesanan();
    }

    /**
     * Menampilkan halaman dashboard utama
     */
    public function index(): void {
        // Data statistik
        $totalMeja = $this->mejaModel->count();
        $totalPelanggan = $this->pelangganModel->count();
        $totalMenu = $this->menuModel->count();
        $totalPemesanan = $this->pemesananModel->count();
        $totalPendapatan = $this->pemesananModel->getTotalPendapatan();

        // Data status pemesanan
        $statusDiproses = $this->pemesananModel->countByStatus('Diproses');
        $statusSelesai = $this->pemesananModel->countByStatus('Selesai');
        $statusDibatalkan = $this->pemesananModel->countByStatus('Dibatalkan');

        // Pemesanan terbaru
        $recentPemesanan = $this->pemesananModel->getRecent(6);

        // Render tampilan
        $pageTitle = 'Dashboard Restoran';
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
