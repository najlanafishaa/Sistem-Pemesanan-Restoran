<?php
/**
 * View Cetak Laporan Pemesanan (Print-Friendly)
 */
require_once __DIR__ . '/../../config/helpers.php';
$selectedStatus = $_GET['status'] ?? '';
$tglDari = $_GET['tanggal_dari'] ?? date('Y-m-01');
$tglSampai = $_GET['tanggal_sampai'] ?? date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pemesanan - Resto Rasa Nusantara</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #F7F6F0;
            color: #26352B;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .print-container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(25,53,43,.08);
            border: 1px solid #E3E8DF;
        }
        .letterhead {
            border-bottom: 3px double #19352B;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .letterhead h2 { color: #19352B; }
        .stat-box {
            background: #F0F4EB;
            border: 1px solid #E3E8DF;
            border-radius: 6px;
        }
        .table thead th {
            background-color: #F0F4EB;
            color: #667085;
        }
        .btn-back {
            background: #fff;
            border: 1px solid #E3E8DF;
            color: #26352B;
            padding: .4rem 1rem;
            border-radius: .45rem;
            text-decoration: none;
            font-size: .88rem;
        }
        .btn-print {
            background: #557A59;
            border: none;
            color: #fff;
            padding: .4rem 1.2rem;
            border-radius: .45rem;
            font-size: .88rem;
            cursor: pointer;
        }
        .btn-print:hover { background: #3e5e42; }
        .signature-box { margin-top: 50px; }
        @media print {
            body { background: #fff !important; }
            .print-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            .no-print { display: none !important; }
            .table thead th {
                background-color: #e8ecdf !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="container my-3 no-print">
        <div class="d-flex justify-content-between align-items-center">
            <a href="<?= baseUrl('index.php?page=laporan') ?>" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Sistem
            </a>
            <button onclick="window.print()" class="btn-print">
                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen / Export PDF
            </button>
        </div>
    </div>

    <div class="print-container">
        <!-- Kop Surat Restoran -->
        <div class="letterhead text-center">
            <h2 class="fw-bold mb-1 tracking-wide">RESTO RASA NUSANTARA</h2>
            <p class="mb-1 text-muted">Jl. Raden Intan No. 88, Enggal, Bandar Lampung &bull; Telp: (0721) 701234 / 0812-7890-1234</p>
            <p class="mb-0 text-muted">Email: info@restorasanusantara.com &bull; Website: www.restorasanusantara.com</p>
        </div>

        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">LAPORAN PEMESANAN RESTORAN</h4>
            <div class="text-secondary">
                Rentang Tanggal: <strong><?= formatTanggalIndonesia($tglDari) ?></strong> s.d. <strong><?= formatTanggalIndonesia($tglSampai) ?></strong>
                <?php if (!empty($selectedStatus)): ?>
                    &bull; Status: <strong><?= e($selectedStatus) ?></strong>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ringkasan Data Cetak -->
        <div class="row g-2 mb-4 p-3 bg-light rounded border text-center">
            <div class="col-6">
                <small class="text-muted d-block text-uppercase">Jumlah Pemesanan</small>
                <strong class="fs-5"><?= $totalPemesanan ?> Transaksi</strong>
            </div>
            <div class="col-6">
                <small class="text-muted d-block text-uppercase">Total Pendapatan (Selesai)</small>
                <strong class="fs-5 text-success"><?= formatRupiah($totalPendapatan) ?></strong>
            </div>
        </div>

        <!-- Tabel Rincian Data -->
        <table class="table table-bordered table-sm align-middle mb-4">
            <thead class="table-secondary text-center">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Kode Pemesanan</th>
                    <th>Tanggal</th>
                    <th>Nama Pelanggan</th>
                    <th>Meja</th>
                    <th>Status</th>
                    <th class="text-end">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporanList)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">Tidak ada data pemesanan pada periode ini.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($laporanList as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="fw-bold"><?= e($row['kode_pemesanan']) ?></td>
                            <td><?= formatTanggalIndonesia($row['tanggal_pemesanan']) ?></td>
                            <td>
                                <div class="fw-bold"><?= e($row['nama_pelanggan']) ?></div>
                                <small class="text-muted"><?= e($row['no_telepon']) ?></small>
                                <?php if (!empty($row['items'])): ?>
                                    <div class="mt-1" style="font-size: 0.75rem; color: #555;">
                                        Pesanan: 
                                        <?php 
                                            $itemNames = array_map(function($it) {
                                                return e($it['nama_menu']) . ' (' . $it['jumlah'] . 'x)';
                                            }, $row['items']);
                                            echo implode(', ', $itemNames);
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= e($row['nomor_meja']) ?></td>
                            <td class="text-center"><?= e($row['status_pemesanan']) ?></td>
                            <td class="text-end fw-bold"><?= formatRupiah($row['total_harga']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="7" class="py-2 px-3">
                        <div class="d-flex justify-content-end align-items-center flex-nowrap gap-3">
                            <span class="fs-6 fw-bold text-dark text-nowrap">TOTAL KESELURUHAN:</span>
                            <span class="fs-6 fw-bold text-success text-nowrap"><?= formatRupiah($totalPendapatan) ?></span>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan / Verifikasi -->
        <div class="row signature-box">
            <div class="col-6 text-center">
                <p class="mb-5">Mengetahui,<br><strong>Manager Restoran</strong></p>
                <p class="mt-5 mb-0"><u>( .................................................. )</u><br><small class="text-muted">NIP: 19850720202101</small></p>
            </div>
            <div class="col-6 text-center">
                <p class="mb-5">Bandar Lampung, <?= formatTanggalIndonesia(date('Y-m-d')) ?><br><strong>Petugas / Kasir</strong></p>
                <p class="mt-5 mb-0"><u>( .................................................. )</u><br><small class="text-muted">Admin Operasional</small></p>
            </div>
        </div>
    </div>

</body>
</html>
