<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Tambah Pemesanan</h5>
                    <a href="<?= baseUrl('index.php?page=pemesanan') ?>" class="btn btn-sm btn-outline-secondary">
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

                <form method="POST" action="<?= baseUrl('index.php?page=pemesanan&action=create') ?>" id="formPemesanan">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                    <!-- Bagian 1: Data Pelanggan, Meja & Tanggal -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="fw-bold text-primary mb-3">Data Pelanggan & Meja</h6>

                        <!-- Pilihan Tipe Pelanggan (Terdaftar atau Pelanggan Baru Langsung) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Pilihan Pelanggan <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipe_pelanggan" id="tipe_existing" value="existing" checked autocomplete="off">
                                <label class="btn btn-outline-primary" for="tipe_existing">Pilih Pelanggan Terdaftar</label>

                                <input type="radio" class="btn-check" name="tipe_pelanggan" id="tipe_new" value="new" autocomplete="off">
                                <label class="btn btn-outline-primary" for="tipe_new">+ Tambah Pelanggan Baru Langsung</label>
                            </div>
                        </div>

                        <!-- Form 1: Pilih Pelanggan Terdaftar -->
                        <div id="section_pelanggan_existing" class="mb-3">
                            <label for="id_pelanggan" class="form-label fw-semibold">Pelanggan Terdaftar <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['id_pelanggan']) ? 'is-invalid' : '' ?>" id="id_pelanggan" name="id_pelanggan">
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

                        <!-- Form 2: Tambah Pelanggan Baru Langsung (Inline) -->
                        <div id="section_pelanggan_new" class="mb-3 p-3 bg-light rounded border d-none">
                            <h6 class="fw-bold text-dark mb-2">Formulir Pelanggan Baru</h6>
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_pelanggan_baru" id="nama_pelanggan_baru" class="form-control form-control-sm <?= isset($errors['nama_pelanggan']) ? 'is-invalid' : '' ?>" placeholder="Contoh: Budi Santoso">
                                    <?php if (isset($errors['nama_pelanggan'])): ?>
                                        <div class="text-danger small"><?= e($errors['nama_pelanggan']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                    <input type="tel" name="no_telepon_baru" id="no_telepon_baru" class="form-control form-control-sm <?= isset($errors['no_telepon']) ? 'is-invalid' : '' ?>" placeholder="Contoh: 081234567890">
                                    <?php if (isset($errors['no_telepon'])): ?>
                                        <div class="text-danger small"><?= e($errors['no_telepon']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Email (Opsional)</label>
                                    <input type="email" name="email_baru" id="email_baru" class="form-control form-control-sm" placeholder="Contoh: budi@gmail.com">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Alamat (Opsional)</label>
                                    <input type="text" name="alamat_baru" id="alamat_baru" class="form-control form-control-sm" placeholder="Contoh: Jl. Kartini No. 42, Tanjung Karang, Bandar Lampung">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Meja -->
                            <div class="col-12 col-md-6">
                                <label for="id_meja" class="form-label fw-semibold">Pilih Meja <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['id_meja']) ? 'is-invalid' : '' ?>" id="id_meja" name="id_meja" required>
                                    <option value="">-- Pilih Meja --</option>
                                    <?php foreach ($mejaList as $m): ?>
                                        <?php 
                                            $isTerisi = ($m['status_meja'] === 'Terisi');
                                            $isPerbaikan = ($m['status_meja'] === 'Perbaikan');
                                            $isDisabled = $isTerisi || $isPerbaikan;
                                        ?>
                                        <option value="<?= $m['id_meja'] ?>" 
                                                data-kapasitas="<?= $m['kapasitas'] ?>"
                                                <?= (string)($data['id_meja'] ?? '') === (string)$m['id_meja'] ? 'selected' : '' ?>
                                                <?= $isDisabled ? 'disabled' : '' ?>>
                                            <?= e($m['nomor_meja']) ?> (Kapasitas: <?= $m['kapasitas'] ?> org - Status: <?= e($m['status_meja']) ?>) <?= $isTerisi ? '[TERISI]' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_meja'])): ?>
                                    <div class="text-danger small mt-1"><?= e($errors['id_meja']) ?></div>
                                <?php endif; ?>
                                <small class="text-muted d-block mt-1">Meja yang sedang 'Terisi' tidak dapat dipilih untuk pemesanan baru.</small>
                            </div>

                            <!-- Tanggal Pemesanan -->
                            <div class="col-12 col-md-6">
                                <label for="tanggal_pemesanan" class="form-label fw-semibold">Tanggal Pemesanan <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control <?= isset($errors['tanggal_pemesanan']) ? 'is-invalid' : '' ?>" 
                                       id="tanggal_pemesanan" 
                                       name="tanggal_pemesanan" 
                                       value="<?= e($data['tanggal_pemesanan'] ?? date('Y-m-d')) ?>" 
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
                                <option value="Diproses" <?= ($data['status_pemesanan'] ?? 'Diproses') === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="Selesai" <?= ($data['status_pemesanan'] ?? '') === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= ($data['status_pemesanan'] ?? '') === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="catatan" class="form-label fw-semibold">Catatan Pemesanan (Opsional)</label>
                            <input type="text" class="form-control" id="catatan" name="catatan" placeholder="Contoh: Tanpa es, saus dipisah" value="<?= e($data['catatan'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= baseUrl('index.php?page=pemesanan') ?>" class="btn btn-light border">Batal</a>
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
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radioExisting = document.getElementById('tipe_existing');
    const radioNew = document.getElementById('tipe_new');
    const sectionExisting = document.getElementById('section_pelanggan_existing');
    const sectionNew = document.getElementById('section_pelanggan_new');
    const selectPelanggan = document.getElementById('id_pelanggan');
    const inputNamaBaru = document.getElementById('nama_pelanggan_baru');
    const inputTelpBaru = document.getElementById('no_telepon_baru');

    function updateCustomerSection() {
        if (!radioExisting || !radioNew) return;
        if (radioNew.checked) {
            sectionExisting.classList.add('d-none');
            sectionNew.classList.remove('d-none');
            if (selectPelanggan) selectPelanggan.removeAttribute('required');
            if (inputNamaBaru) inputNamaBaru.setAttribute('required', 'required');
            if (inputTelpBaru) inputTelpBaru.setAttribute('required', 'required');
        } else {
            sectionExisting.classList.remove('d-none');
            sectionNew.classList.add('d-none');
            if (selectPelanggan) selectPelanggan.setAttribute('required', 'required');
            if (inputNamaBaru) inputNamaBaru.removeAttribute('required');
            if (inputTelpBaru) inputTelpBaru.removeAttribute('required');
        }
    }

    if (radioExisting && radioNew) {
        radioExisting.addEventListener('change', updateCustomerSection);
        radioNew.addEventListener('change', updateCustomerSection);
        updateCustomerSection();
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
