<?php
require_once __DIR__ . '/../layout/header.php';
$selectedStatus = $_GET['status'] ?? '';
$tglDari = $_GET['tanggal_dari'] ?? date('Y-m-01');
$tglSampai = $_GET['tanggal_sampai'] ?? date('Y-m-d');
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Laporan Pemesanan</h4>
        <p class="text-muted mb-0">Rekapitulasi data transaksi pemesanan dan total nilai pesanan restoran.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= baseUrl('index.php?page=laporan&action=cetak&status=' . urlencode($selectedStatus) . '&tanggal_dari=' . urlencode($tglDari) . '&tanggal_sampai=' . urlencode($tglSampai)) ?>" target="_blank" class="btn btn-outline-primary">
            Cetak Laporan
        </a>
    </div>
</div>

<!-- Form Filter Laporan Sederhana -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Filter Rentang Tanggal & Status</h6>
    </div>
    <div class="card-body p-3">
        <form method="GET" action="<?= baseUrl('index.php') ?>" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="laporan">

            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control" value="<?= e($tglDari) ?>">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="form-control" value="<?= e($tglSampai) ?>">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Status Pemesanan</label>
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="Diproses" <?= $selectedStatus === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="Selesai" <?= $selectedStatus === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="Dibatalkan" <?= $selectedStatus === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>

            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    Tampilkan
                </button>
                <a href="<?= baseUrl('index.php?page=laporan') ?>" class="btn btn-outline-secondary" title="Reset Filter">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Kartu Ringkasan Laporan Sesuai Spesifikasi -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card card-stat h-100 p-3">
            <span class="text-muted small fw-semibold text-uppercase">Jumlah Pemesanan</span>
            <h3 class="fw-bold mb-0 mt-1"><?= $totalPemesanan ?> Transaksi</h3>
            <small class="text-muted">Total transaksi pada rentang tanggal yang dipilih</small>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card card-stat h-100 p-3">
            <span class="text-muted small fw-semibold text-uppercase">Total Pendapatan (Selesai)</span>
            <h3 class="fw-bold text-success mb-0 mt-1"><?= formatRupiah($totalPendapatan) ?></h3>
            <small class="text-muted">Total nilai transaksi pesanan yang telah selesai</small>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Rincian Data Laporan</h6>
        <span class="badge bg-secondary">Periode: <?= formatTanggalIndonesia($tglDari) ?> s.d. <?= formatTanggalIndonesia($tglSampai) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Status</th>
                    <th class="text-end">Total Harga</th>
                    <th class="text-center" style="width: 90px;">Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporanList)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data pemesanan pada rentang filter ini.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($laporanList as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-bold text-primary"><?= e($row['kode_pemesanan']) ?></td>
                            <td><?= formatTanggalIndonesia($row['tanggal_pemesanan']) ?></td>
                            <td>
                                <div class="fw-semibold"><?= e($row['nama_pelanggan']) ?></div>
                                <small class="text-muted d-block"><?= e($row['no_telepon']) ?></small>
                                <?php if (!empty($row['items'])): ?>
                                    <div class="mt-1" style="font-size: 0.78rem;">
                                        <?php 
                                            $itemNames = array_map(function($it) {
                                                return e($it['nama_menu']) . ' (' . $it['jumlah'] . 'x)';
                                            }, $row['items']);
                                            echo '<span class="text-secondary">' . implode(', ', $itemNames) . '</span>';
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= e($row['nomor_meja']) ?></span>
                            </td>
                            <td><?= getStatusBadge($row['status_pemesanan']) ?></td>
                            <td class="text-end fw-bold text-success"><?= formatRupiah($row['total_harga']) ?></td>
                            <td class="text-center">
                                <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $row['id_pemesanan']) ?>" class="btn btn-sm btn-outline-info" title="Detail">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="8" class="py-3 px-3">
                        <div class="d-flex justify-content-end align-items-center flex-nowrap gap-3">
                            <span class="fs-6 fw-bold text-dark text-nowrap">TOTAL KESELURUHAN:</span>
                            <span class="fs-5 fw-bold text-success text-nowrap"><?= formatRupiah($totalPendapatan) ?></span>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
