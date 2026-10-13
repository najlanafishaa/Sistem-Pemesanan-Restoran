<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0">Detail Pemesanan #<?= e($pemesanan['kode_pemesanan']) ?></h4>
            <?= getStatusBadge($pemesanan['status_pemesanan']) ?>
        </div>
        <p class="text-muted mb-0">Tanggal Pemesanan: <?= formatTanggalIndonesia($pemesanan['tanggal_pemesanan']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= baseUrl('index.php?page=pemesanan&action=cetakBukti&id=' . $pemesanan['id_pemesanan']) ?>" target="_blank" class="btn btn-primary">
            Cetak Bukti Pemesanan
        </a>
        <a href="<?= baseUrl('index.php?page=pemesanan&action=edit&id=' . $pemesanan['id_pemesanan']) ?>" class="btn btn-outline-warning">
            Edit
        </a>
        <a href="<?= baseUrl('index.php?page=pemesanan') ?>" class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>
</div>

<!-- Pembaruan Status Pemesanan -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3 bg-light rounded d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold text-secondary">Ubah Status Pemesanan:</span>
        </div>
        <form method="POST" action="<?= baseUrl('index.php?page=pemesanan&action=updateStatus') ?>" class="d-flex align-items-center gap-2">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="id" value="<?= $pemesanan['id_pemesanan'] ?>">
            <input type="hidden" name="redirect_to" value="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $pemesanan['id_pemesanan']) ?>">
            
            <select name="status" class="form-select form-select-sm" style="min-width: 160px;">
                <option value="Diproses" <?= $pemesanan['status_pemesanan'] === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="Selesai" <?= $pemesanan['status_pemesanan'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="Dibatalkan" <?= $pemesanan['status_pemesanan'] === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                Simpan
            </button>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Kartu Informasi Pelanggan -->
    <div class="col-12 col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-primary">Data Pelanggan</h6>
            </div>
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-1"><?= e($pemesanan['nama_pelanggan']) ?></h5>
                <p class="text-muted small mb-3">ID Pelanggan: #<?= $pemesanan['id_pelanggan'] ?></p>

                <div class="mb-2">
                    <span class="text-muted small d-block">Nomor Telepon:</span>
                    <span class="fw-semibold"><?= e($pemesanan['no_telepon']) ?></span>
                </div>

                <div class="mb-2">
                    <span class="text-muted small d-block">Email:</span>
                    <span class="fw-semibold"><?= !empty($pemesanan['email']) ? e($pemesanan['email']) : '-' ?></span>
                </div>

                <div>
                    <span class="text-muted small d-block">Alamat:</span>
                    <span><?= !empty($pemesanan['alamat']) ? e($pemesanan['alamat']) : '-' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Informasi Meja & Catatan -->
    <div class="col-12 col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-primary">Data Meja & Catatan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="fw-bold text-dark mb-0">Meja <?= e($pemesanan['nomor_meja']) ?></h5>
                    <?= getStatusBadge($pemesanan['status_meja']) ?>
                </div>
                <p class="text-muted small mb-3">Kapasitas Meja: <?= e($pemesanan['kapasitas']) ?> Orang</p>

                <div class="mb-2">
                    <span class="text-muted small d-block">Tanggal Transaksi:</span>
                    <span class="fw-bold text-dark"><?= formatTanggalIndonesia($pemesanan['tanggal_pemesanan']) ?></span>
                </div>

                <div class="mb-2">
                    <span class="text-muted small d-block">Catatan Pemesanan:</span>
                    <span class="fst-italic text-secondary"><?= !empty($pemesanan['catatan']) ? e($pemesanan['catatan']) : 'Tidak ada catatan.' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rincian Pesanan Menu Makanan & Minuman -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-primary">Daftar Menu yang Dipesan</h6>
        <span class="badge bg-secondary"><?= count($pemesanan['items']) ?> Menu</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-center" style="width: 100px;">Jumlah</th>
                    <th class="text-end">Subtotal</th>
                    <th>Catatan Khusus</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pemesanan['items'])): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Belum ada rincian menu untuk pemesanan ini. Menu dapat ditambahkan melalui tombol <strong>Edit</strong>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($pemesanan['items'] as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-bold text-dark"><?= e($item['nama_menu']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= e($item['kategori']) ?></span></td>
                            <td class="text-end"><?= formatRupiah($item['harga_satuan']) ?></td>
                            <td class="text-center fw-bold"><?= e($item['jumlah']) ?></td>
                            <td class="text-end fw-bold text-success"><?= formatRupiah($item['subtotal']) ?></td>
                            <td class="small text-muted"><?= !empty($item['catatan_khusus']) ? e($item['catatan_khusus']) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot class="table-light">
                <?php
                    $subtotalSemua = 0.00;
                    if (!empty($pemesanan['items'])) {
                        foreach ($pemesanan['items'] as $it) {
                            $subtotalSemua += (float)$it['subtotal'];
                        }
                    }
                    $financials = calculateOrderFinancials($subtotalSemua);
                ?>
                <tr>
                    <th colspan="5" class="text-end text-muted">Subtotal Seluruh Menu:</th>
                    <th class="text-end fw-bold text-dark"><?= formatRupiah($financials['subtotal']) ?></th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end text-muted">Biaya Layanan (5%):</th>
                    <th class="text-end fw-semibold text-dark"><?= formatRupiah($financials['biaya_layanan']) ?></th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end text-muted">Pajak Restoran (10%):</th>
                    <th class="text-end fw-semibold text-dark"><?= formatRupiah($financials['pajak']) ?></th>
                    <th></th>
                </tr>
                <tr class="table-success border-top border-dark">
                    <th colspan="5" class="text-end fs-6 text-dark">TOTAL PEMBAYARAN:</th>
                    <th class="text-end fs-5 text-success fw-bold"><?= formatRupiah($financials['total_pembayaran']) ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
