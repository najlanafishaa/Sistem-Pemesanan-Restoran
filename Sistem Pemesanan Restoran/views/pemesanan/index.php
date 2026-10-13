<?php
require_once __DIR__ . '/../layout/header.php';
$selectedStatus = $_GET['status'] ?? '';
$tglDari = $_GET['tanggal_dari'] ?? '';
$tglSampai = $_GET['tanggal_sampai'] ?? '';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Pemesanan</h4>
        <p class="text-muted mb-0">Kelola pemesanan kasir, meja yang digunakan, menu, dan status transaksi.</p>
    </div>
    <div>
        <a href="<?= baseUrl('index.php?page=pemesanan&action=create') ?>" class="btn btn-primary">
            Tambah Pemesanan
        </a>
    </div>
</div>

<!-- 3 Kotak Statistik Status Sederhana (Diproses, Selesai, Dibatalkan) -->
<div class="row g-3 mb-4 text-center">
    <div class="col-12 col-md-4">
        <div class="card p-3 shadow-none h-100 border-warning">
            <span class="text-muted small fw-semibold text-uppercase">Diproses</span>
            <h3 class="fw-bold mb-0 mt-1 text-dark"><?= (int)($statusDiproses ?? 0) ?></h3>
            <small class="text-muted">Pesanan sedang aktif</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3 shadow-none h-100 border-success">
            <span class="text-muted small fw-semibold text-uppercase">Selesai</span>
            <h3 class="fw-bold mb-0 mt-1 text-success"><?= (int)($statusSelesai ?? 0) ?></h3>
            <small class="text-muted">Transaksi selesai</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3 shadow-none h-100 border-danger">
            <span class="text-muted small fw-semibold text-uppercase">Dibatalkan</span>
            <h3 class="fw-bold mb-0 mt-1 text-danger"><?= (int)($statusDibatalkan ?? 0) ?></h3>
            <small class="text-muted">Pesanan dibatalkan</small>
        </div>
    </div>
</div>

<!-- Filter Bar Sederhana -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= baseUrl('index.php') ?>" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="pemesanan">

            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Status Pemesanan</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="Diproses" <?= $selectedStatus === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="Selesai" <?= $selectedStatus === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="Dibatalkan" <?= $selectedStatus === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="<?= e($tglDari) ?>">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="<?= e($tglSampai) ?>">
            </div>

            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                    Filter
                </button>
                <a href="<?= baseUrl('index.php?page=pemesanan') ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Pemesanan -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Daftar Pemesanan</h6>
        <span class="badge bg-primary"><?= count($pemesananList) ?> Pemesanan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 190px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pemesananList)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data pemesanan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pemesananList as $p): ?>
                        <tr>
                            <td>
                                <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $p['id_pemesanan']) ?>" class="fw-bold text-primary text-decoration-none">
                                    <?= e($p['kode_pemesanan']) ?>
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= e($p['nama_pelanggan']) ?></div>
                                <small class="text-muted"><?= e($p['no_telepon']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= e($p['nomor_meja']) ?>
                                </span>
                            </td>
                            <td>
                                <div><?= formatTanggalIndonesia($p['tanggal_pemesanan']) ?></div>
                            </td>
                            <td>
                                <span class="fw-bold text-success"><?= formatRupiah($p['total_harga']) ?></span>
                            </td>
                            <td><?= getStatusBadge($p['status_pemesanan']) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $p['id_pemesanan']) ?>" class="btn btn-sm btn-outline-info" title="Detail">
                                        Detail
                                    </a>
                                    <a href="<?= baseUrl('index.php?page=pemesanan&action=edit&id=' . $p['id_pemesanan']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?= baseUrl('index.php?page=pemesanan&action=delete') ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $p['id_pemesanan'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-name="pemesanan '<?= e($p['kode_pemesanan']) ?>'" title="Hapus">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
