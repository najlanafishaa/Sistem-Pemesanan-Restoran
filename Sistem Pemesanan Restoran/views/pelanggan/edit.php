<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Edit Data Pelanggan</h5>
                    <a href="<?= baseUrl('index.php?page=pelanggan') ?>" class="btn btn-sm btn-outline-secondary">
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= baseUrl('index.php?page=pelanggan&action=edit&id=' . $id) ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                    <!-- Nama Pelanggan -->
                    <div class="mb-3">
                        <label for="nama_pelanggan" class="form-label fw-semibold">Nama Lengkap Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= isset($errors['nama_pelanggan']) ? 'is-invalid' : '' ?>" 
                               id="nama_pelanggan" 
                               name="nama_pelanggan" 
                               value="<?= e($data['nama_pelanggan'] ?? '') ?>" 
                               required>
                        <?php if (isset($errors['nama_pelanggan'])): ?>
                            <div class="invalid-feedback"><?= e($errors['nama_pelanggan']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="mb-3">
                        <label for="no_telepon" class="form-label fw-semibold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" 
                               class="form-control <?= isset($errors['no_telepon']) ? 'is-invalid' : '' ?>" 
                               id="no_telepon" 
                               name="no_telepon" 
                               value="<?= e($data['no_telepon'] ?? '') ?>" 
                               required>
                        <?php if (isset($errors['no_telepon'])): ?>
                            <div class="invalid-feedback"><?= e($errors['no_telepon']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Alamat Email (Opsional)</label>
                        <input type="email" 
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= e($data['email'] ?? '') ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-4">
                        <label for="alamat" class="form-label fw-semibold">Alamat (Opsional)</label>
                        <textarea class="form-control" 
                                  id="alamat" 
                                  name="alamat" 
                                  rows="3"><?= e($data['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= baseUrl('index.php?page=pelanggan') ?>" class="btn btn-light border">Batal</a>
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
