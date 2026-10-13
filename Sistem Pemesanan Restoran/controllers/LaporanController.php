<?php
/**
 * Controller Laporan
 * Mengelola laporan transaksi pemesanan restoran, filter tanggal & status,
 * serta tampilan cetak laporan (print view).
 */

require_once __DIR__ . '/../models/Pemesanan.php';
require_once __DIR__ . '/../config/helpers.php';

class LaporanController {
    private Pemesanan $pemesananModel;

    public function __construct() {
        $this->pemesananModel = new Pemesanan();
    }

    /**
     * Menampilkan halaman laporan dengan filter tanggal dan status
     */
    public function index(): void {
        $status = !empty($_GET['status']) ? sanitize($_GET['status']) : null;
        $tanggalDari = !empty($_GET['tanggal_dari']) ? sanitize($_GET['tanggal_dari']) : date('Y-m-01');
        $tanggalSampai = !empty($_GET['tanggal_sampai']) ? sanitize($_GET['tanggal_sampai']) : date('Y-m-d');

        $laporanList = $this->pemesananModel->getAll($status, $tanggalDari, $tanggalSampai);

        // Kalkulasi statistik laporan
        // Pendapatan HANYA dihitung dari pesanan yang sudah Selesai (pembayaran dikonfirmasi)
        $totalPemesanan = count($laporanList);
        $totalPendapatan = 0.00;
        $totalTamu = 0;
        $detailModel = new DetailPemesanan();
        foreach ($laporanList as &$row) {
            if ($row['status_pemesanan'] === 'Selesai') {
                $totalPendapatan += (float)$row['total_harga'];
            }
            $totalTamu += (int)$row['jumlah_orang'];
            $row['items'] = $detailModel->getByPemesananId((int)$row['id_pemesanan']);
        }
        unset($row);

        $rataRata = $totalPemesanan > 0 ? ($totalPendapatan / $totalPemesanan) : 0.00;

        $pageTitle = 'Laporan Pemesanan Restoran';
        require_once __DIR__ . '/../views/laporan/index.php';
    }

    /**
     * Menampilkan halaman cetak laporan yang bersih dan ramah cetak (print-friendly)
     */
    public function cetak(): void {
        $status = !empty($_GET['status']) ? sanitize($_GET['status']) : null;
        $tanggalDari = !empty($_GET['tanggal_dari']) ? sanitize($_GET['tanggal_dari']) : date('Y-m-01');
        $tanggalSampai = !empty($_GET['tanggal_sampai']) ? sanitize($_GET['tanggal_sampai']) : date('Y-m-d');

        $laporanList = $this->pemesananModel->getAll($status, $tanggalDari, $tanggalSampai);

        $totalPemesanan = count($laporanList);
        $totalPendapatan = 0.00;
        $totalTamu = 0;

        $detailModel = new DetailPemesanan();
        foreach ($laporanList as &$row) {
            if ($row['status_pemesanan'] === 'Selesai') {
                $totalPendapatan += (float)$row['total_harga'];
            }
            $totalTamu += (int)$row['jumlah_orang'];
            $row['items'] = $detailModel->getByPemesananId((int)$row['id_pemesanan']);
        }
        unset($row);

        $pageTitle = 'Cetak Laporan Pemesanan Restoran';
        require_once __DIR__ . '/../views/laporan/cetak.php';
    }
}
