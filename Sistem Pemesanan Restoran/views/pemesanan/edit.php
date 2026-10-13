<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Edit Pemesanan <?= e($pemesanan['kode_pemesanan']) ?></h5>
                        <small class="text-muted">Perbarui data pelanggan, meja, menu pesanan, atau status transaksi.</small>
                    </div>
                    <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $id) ?>" class="btn btn-sm btn-outline-secondary">
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <?= e($errors['general']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors['menu'])): ?>
                    <div class="alert alert-warning">
                        <?= e($errors['menu']) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= baseUrl('index.php?page=pemesanan&action=edit&id=' . $id) ?>" id="formPemesanan">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                    <!-- Bagian 1: Data Pelanggan, Meja & Tanggal -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="fw-bold text-primary mb-3">Data Pelanggan & Meja</h6>
                        <div class="row g-3">
                            <!-- Pelanggan -->
                            <div class="col-12 col-md-5">
                                <label for="id_pelanggan" class="form-label fw-semibold">Pelanggan <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['id_pelanggan']) ? 'is-invalid' : '' ?>" id="id_pelanggan" name="id_pelanggan" required>
                                    <option value="">-- Pilih Pelanggan --</option>
                                    <?php foreach ($pelangganList as $pel): ?>
                                        <option value="<?= $pel['id_pelanggan'] ?>" <?= (string)($data['id_pelanggan'] ?? '') === (string)$pel['id_pelanggan'] ? 'selected' : '' ?>>
                                            <?= e($pel['nama_pelanggan']) ?> (<?= e($pel['no_telepon']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_pelanggan'])): ?>
                                    <div class="text-danger small mt-1"><?= e($errors['id_pelanggan']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Meja -->
                            <div class="col-12 col-md-4">
                                <label for="id_meja" class="form-label fw-semibold">Pilih Meja <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['id_meja']) ? 'is-invalid' : '' ?>" id="id_meja" name="id_meja" required>
                                    <option value="">-- Pilih Meja --</option>
                                    <?php foreach ($mejaList as $m): ?>
                                        <?php 
                                            $isCurrentMeja = ((string)($data['id_meja'] ?? '') === (string)$m['id_meja']);
                                            $isTerisi = ($m['status_meja'] === 'Terisi' && !$isCurrentMeja);
                                            $isPerbaikan = ($m['status_meja'] === 'Perbaikan' && !$isCurrentMeja);
                                            $isDisabled = $isTerisi || $isPerbaikan;
                                        ?>
                                        <option value="<?= $m['id_meja'] ?>" 
                                                data-kapasitas="<?= $m['kapasitas'] ?>"
                                                <?= $isCurrentMeja ? 'selected' : '' ?>
                                                <?= $isDisabled ? 'disabled' : '' ?>>
                                            <?= e($m['nomor_meja']) ?> (Kapasitas: <?= $m['kapasitas'] ?> org - <?= e($m['status_meja']) ?>) <?= $isTerisi ? '[TERISI]' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_meja'])): ?>
                                    <div class="text-danger small mt-1"><?= e($errors['id_meja']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Tanggal Pemesanan -->
                            <div class="col-12 col-md-3">
                                <label for="tanggal_pemesanan" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control <?= isset($errors['tanggal_pemesanan']) ? 'is-invalid' : '' ?>" 
                                       id="tanggal_pemesanan" 
                                       name="tanggal_pemesanan" 
                                       value="<?= e($data['tanggal_pemesanan'] ?? '') ?>" 
                                       required>
                                <?php if (isset($errors['tanggal_pemesanan'])): ?>
                                    <div class="invalid-feedback"><?= e($errors['tanggal_pemesanan']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 2: Rincian Menu Pesanan -->
                    <div class="border-bottom pb-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold text-primary mb-0">Pilih Menu & Jumlah Pesanan <span class="text-danger">*</span></h6>
                                <small class="text-muted">Pilih menu dan tentukan jumlah (harus bilangan bulat positif).</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddMenuItem">
                                Tambah Menu
                            </button>
                        </div>

                        <!-- Container Baris Menu -->
                        <div id="menuItemsContainer">
                            <?php if (!empty($selectedItems)): ?>
                                <?php foreach ($selectedItems as $sItem): ?>
                                    <div class="order-item-row menu-item-row mb-2">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-12 col-md-5">
                                                <select name="menu_id[]" class="form-select form-select-sm select-menu-item" required>
                                                    <option value="" data-price="0">-- Pilih Menu --</option>
                                                    <?php foreach ($menuList as $m): ?>
                                                        <option value="<?= $m['id_menu'] ?>" data-price="<?= $m['harga'] ?>" <?= (string)$sItem['id_menu'] === (string)$m['id_menu'] ? 'selected' : '' ?>>
                                                            <?= e($m['nama_menu']) ?> (<?= e($m['kategori']) ?>) - <?= formatRupiah($m['harga']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-4 col-md-2">
                                                <input type="number" name="menu_qty[]" class="form-control form-control-sm input-menu-qty" min="1" max="100" value="<?= e($sItem['jumlah']) ?>" placeholder="Qty" required>
                                            </div>
                                            <div class="col-5 col-md-3">
                                                <input type="text" name="menu_notes[]" class="form-control form-control-sm" placeholder="Catatan (opsional)" value="<?= e($sItem['catatan'] ?? '') ?>">
                                            </div>
                                            <div class="col-3 col-md-2 text-end">
                                                <span class="display-subtotal fw-bold text-success d-inline-block me-2">Rp 0</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="order-item-row menu-item-row mb-2">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 col-md-5">
                                            <select name="menu_id[]" class="form-select form-select-sm select-menu-item" required>
                                                <option value="" data-price="0">-- Pilih Menu --</option>
                                                <?php foreach ($menuList as $m): ?>
                                                    <option value="<?= $m['id_menu'] ?>" data-price="<?= $m['harga'] ?>">
                                                        <?= e($m['nama_menu']) ?> (<?= e($m['kategori']) ?>) - <?= formatRupiah($m['harga']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <input type="number" name="menu_qty[]" class="form-control form-control-sm input-menu-qty" min="1" max="100" value="1" placeholder="Qty" required>
                                        </div>
                                        <div class="col-5 col-md-3">
                                            <input type="text" name="menu_notes[]" class="form-control form-control-sm" placeholder="Catatan (opsional)">
                                        </div>
                                        <div class="col-3 col-md-2 text-end">
                                            <span class="display-subtotal fw-bold text-success d-inline-block me-2">Rp 0</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Ringkasan Total Pesanan Realtime (Subtotal, Layanan 5%, Pajak 10%, Total Pembayaran) -->
                        <div class="p-3 bg-light rounded mt-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-secondary small">Subtotal Seluruh Menu:</span>
                                <span class="fw-semibold text-dark" id="displaySubtotalOrder">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-secondary small">Biaya Layanan (5%):</span>
                                <span class="fw-semibold text-dark" id="displayServiceFee">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary small">Pajak Restoran (10%):</span>
                                <span class="fw-semibold text-dark" id="displayTaxFee">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="fw-bold text-dark fs-6">TOTAL PEMBAYARAN:</span>
                                <span class="fs-4 fw-bold text-success" id="displayGrandTotal">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 3: Status & Catatan -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label for="status_pemesanan" class="form-label fw-semibold">Status Pemesanan</label>
                            <select class="form-select" id="status_pemesanan" name="status_pemesanan">
                                <option value="Diproses" <?= ($data['status_pemesanan'] ?? '') === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="Selesai" <?= ($data['status_pemesanan'] ?? '') === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= ($data['status_pemesanan'] ?? '') === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="catatan" class="form-label fw-semibold">Catatan Pemesanan (Opsional)</label>
                            <input type="text" class="form-control" id="catatan" name="catatan" value="<?= e($data['catatan'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $id) ?>" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template Baris Menu untuk JavaScript Clone -->
<template id="menuItemTemplate">
    <div class="order-item-row menu-item-row mb-2">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <select name="menu_id[]" class="form-select form-select-sm select-menu-item" required>
                    <option value="" data-price="0">-- Pilih Menu --</option>
                    <?php foreach ($menuList as $m): ?>
                        <option value="<?= $m['id_menu'] ?>" data-price="<?= $m['harga'] ?>">
                            <?= e($m['nama_menu']) ?> (<?= e($m['kategori']) ?>) - <?= formatRupiah($m['harga']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-4 col-md-2">
                <input type="number" name="menu_qty[]" class="form-control form-control-sm input-menu-qty" min="1" max="100" value="1" placeholder="Qty" required>
            </div>
            <div class="col-5 col-md-3">
                <input type="text" name="menu_notes[]" class="form-control form-control-sm" placeholder="Catatan (opsional)">
            </div>
            <div class="col-3 col-md-2 text-end">
                <span class="display-subtotal fw-bold text-success d-inline-block me-2">Rp 0</span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</template>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
