<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Tambah Meja Baru</h5>
                    <a href="<?= baseUrl('index.php?page=meja') ?>" class="btn btn-sm btn-outline-secondary">
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= baseUrl('index.php?page=meja&action=create') ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                    <!-- Nomor Meja -->
                    <div class="mb-3">
                        <label for="nomor_meja" class="form-label fw-semibold">Nomor / Nama Meja <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= isset($errors['nomor_meja']) ? 'is-invalid' : '' ?>" 
                               id="nomor_meja" 
                               name="nomor_meja" 
                               placeholder="Contoh: Meja 05 atau VIP 01"
                               value="<?= e($data['nomor_meja'] ?? '') ?>" 
                               required>
                        <?php if (isset($errors['nomor_meja'])): ?>
                            <div class="invalid-feedback"><?= e($errors['nomor_meja']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Kapasitas -->
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label fw-semibold">Kapasitas Kursi (Orang) <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control <?= isset($errors['kapasitas']) ? 'is-invalid' : '' ?>" 
                               id="kapasitas" 
                               name="kapasitas" 
                               min="1" 
                               max="100"
                               placeholder="Contoh: 4"
                               value="<?= e($data['kapasitas'] ?? 4) ?>" 
                               required>
                        <?php if (isset($errors['kapasitas'])): ?>
                            <div class="invalid-feedback"><?= e($errors['kapasitas']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Lokasi / Area -->
                    <div class="mb-3">
                        <label for="lokasi" class="form-label fw-semibold">Area / Lokasi Meja</label>
                        <input type="text" 
                               class="form-control" 
                               id="lokasi" 
                               name="lokasi" 
                               placeholder="Contoh: Indoor Lantai 1, Outdoor Taman, VIP Room"
                               value="<?= e($data['lokasi'] ?? 'Indoor') ?>">
                    </div>

                    <!-- Status Meja -->
                    <div class="mb-4">
                        <label for="status_meja" class="form-label fw-semibold">Status Awal Meja</label>
                        <select class="form-select <?= isset($errors['status_meja']) ? 'is-invalid' : '' ?>" id="status_meja" name="status_meja">
                            <option value="Tersedia" <?= ($data['status_meja'] ?? '') === 'Tersedia' ? 'selected' : '' ?>>Tersedia (Siap Digunakan)</option>
                            <option value="Terisi" <?= ($data['status_meja'] ?? '') === 'Terisi' ? 'selected' : '' ?>>Terisi (Sedang Digunakan)</option>
                            <option value="Perbaikan" <?= ($data['status_meja'] ?? '') === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan / Maintenance</option>
                        </select>
                        <?php if (isset($errors['status_meja'])): ?>
                            <div class="invalid-feedback"><?= e($errors['status_meja']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= baseUrl('index.php?page=meja') ?>" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
