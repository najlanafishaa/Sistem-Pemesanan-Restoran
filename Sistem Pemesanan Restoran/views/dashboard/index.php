<?php
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--color-text);">
            Dashboard
        </h4>
        <p class="text-muted mb-0">
            Ringkasan jumlah pelanggan, meja, menu, dan pemesanan restoran.
        </p>
    </div>
    <div>
        <a href="<?= baseUrl('index.php?page=pemesanan&action=create') ?>" class="btn btn-primary btn-sm">
            Tambah Pemesanan
        </a>
    </div>
</div>

<!-- ── Kartu Ringkasan (Pelanggan, Meja, Menu, Pemesanan) ── -->
<div class="row g-3 mb-4">

    <!-- Ringkasan Pelanggan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 p-3">
            <div class="text-muted small fw-bold text-uppercase mb-1">
                Pelanggan
            </div>
            <div class="fw-bold mb-1 fs-3" style="color:var(--color-text);">
                <?= $totalPelanggan ?>
            </div>
            <a href="<?= baseUrl('index.php?page=pelanggan') ?>" class="text-decoration-none small text-primary">
                Lihat Data &rarr;
            </a>
        </div>
    </div>

    <!-- Ringkasan Meja -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 p-3">
            <div class="text-muted small fw-bold text-uppercase mb-1">
                Meja
            </div>
            <div class="fw-bold mb-1 fs-3" style="color:var(--color-text);">
                <?= $totalMeja ?>
            </div>
            <a href="<?= baseUrl('index.php?page=meja') ?>" class="text-decoration-none small text-primary">
                Lihat Data &rarr;
            </a>
        </div>
    </div>

    <!-- Ringkasan Menu -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 p-3">
            <div class="text-muted small fw-bold text-uppercase mb-1">
                Menu
            </div>
            <div class="fw-bold mb-1 fs-3" style="color:var(--color-text);">
                <?= $totalMenu ?>
            </div>
            <a href="<?= baseUrl('index.php?page=menu') ?>" class="text-decoration-none small text-primary">
                Lihat Data &rarr;
            </a>
        </div>
    </div>

    <!-- Ringkasan Pemesanan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 p-3">
            <div class="text-muted small fw-bold text-uppercase mb-1">
                Pemesanan
            </div>
            <div class="fw-bold mb-1 fs-3" style="color:var(--color-text);">
                <?= $totalPemesanan ?>
            </div>
            <a href="<?= baseUrl('index.php?page=pemesanan') ?>" class="text-decoration-none small text-primary">
                Lihat Data &rarr;
            </a>
        </div>
    </div>

</div>

<!-- ── Baris 2: Pemantauan Status Pemesanan ── -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold small" style="color:var(--color-text);">
                    Status Pemesanan
                </span>
                <a href="<?= baseUrl('index.php?page=pemesanan') ?>"
                   class="btn btn-sm btn-light border py-1 small">
                    Daftar Pemesanan
                </a>
            </div>
            <div class="card-body p-3">
                <div class="row g-2 text-center">
                    <div class="col-12 col-md-4">
                        <div class="card p-2 shadow-none border-warning h-100">
                            <span class="text-muted small fw-semibold text-uppercase">Diproses</span>
                            <div class="fw-bold fs-4 text-dark mt-1">
                                <?= $statusDiproses ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card p-2 shadow-none border-success h-100">
                            <span class="text-muted small fw-semibold text-uppercase">Selesai</span>
                            <div class="fw-bold fs-4 text-success mt-1">
                                <?= $statusSelesai ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card p-2 shadow-none border-danger h-100">
                            <span class="text-muted small fw-semibold text-uppercase">Dibatalkan</span>
                            <div class="fw-bold fs-4 text-danger mt-1">
                                <?= $statusDibatalkan ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Tabel Pemesanan Terbaru ── -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold small" style="color:var(--color-text);">
            Daftar Pemesanan Terbaru
        </span>
        <a href="<?= baseUrl('index.php?page=pemesanan') ?>"
           class="btn btn-sm btn-light border small">
            Semua Pemesanan
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentPemesanan)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Belum ada data pemesanan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentPemesanan as $row): ?>
                        <tr>
                            <td>
                                <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $row['id_pemesanan']) ?>"
                                   class="fw-bold text-decoration-none" style="color:var(--color-primary);">
                                    <?= e($row['kode_pemesanan']) ?>
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color:var(--color-text);">
                                    <?= e($row['nama_pelanggan']) ?>
                                </div>
                                <small style="color:var(--color-muted);"><?= e($row['no_telepon']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= e($row['nomor_meja']) ?>
                                </span>
                            </td>
                            <td>
                                <div><?= formatTanggalIndonesia($row['tanggal_pemesanan']) ?></div>
                            </td>
                            <td class="fw-bold text-success">
                                <?= formatRupiah($row['total_harga']) ?>
                            </td>
                            <td><?= getStatusBadge($row['status_pemesanan']) ?></td>
                            <td class="text-center">
                                <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $row['id_pemesanan']) ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
