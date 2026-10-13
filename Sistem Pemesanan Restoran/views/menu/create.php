<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Tambah Menu Baru</h5>
                    <a href="<?= baseUrl('index.php?page=menu') ?>" class="btn btn-sm btn-outline-secondary">
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= baseUrl('index.php?page=menu&action=create') ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label for="nama_menu" class="form-label fw-semibold">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= isset($errors['nama_menu']) ? 'is-invalid' : '' ?>" 
                               id="nama_menu" 
                               name="nama_menu" 
                               placeholder="Contoh: Nasi Goreng Spesial Resto"
                               value="<?= e($data['nama_menu'] ?? '') ?>" 
                               required>
                        <?php if (isset($errors['nama_menu'])): ?>
                            <div class="invalid-feedback"><?= e($errors['nama_menu']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label for="kategori" class="form-label fw-semibold">Kategori Hidangan <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['kategori']) ? 'is-invalid' : '' ?>" id="kategori" name="kategori" required>
                            <option value="Makanan" <?= ($data['kategori'] ?? '') === 'Makanan' ? 'selected' : '' ?>>Makanan (Main Course)</option>
                            <option value="Minuman" <?= ($data['kategori'] ?? '') === 'Minuman' ? 'selected' : '' ?>>Minuman (Beverage)</option>
                            <option value="Dessert" <?= ($data['kategori'] ?? '') === 'Dessert' ? 'selected' : '' ?>>Dessert (Hidangan Penutup)</option>
                            <option value="Paket" <?= ($data['kategori'] ?? '') === 'Paket' ? 'selected' : '' ?>>Paket Hemat / Keluarga</option>
                        </select>
                        <?php if (isset($errors['kategori'])): ?>
                            <div class="invalid-feedback"><?= e($errors['kategori']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label for="harga" class="form-label fw-semibold">Harga Satuan (Rupiah) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>" 
                                   id="harga" 
                                   name="harga" 
                                   placeholder="35000"
                                   min="0"
                                   step="500"
                                   value="<?= e($data['harga'] ?? '') ?>" 
                                   required>
                        </div>
                        <?php if (isset($errors['harga'])): ?>
                            <div class="text-danger small mt-1"><?= e($errors['harga']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Status Ketersediaan -->
                    <div class="mb-3">
                        <label for="status_menu" class="form-label fw-semibold">Status Ketersediaan</label>
                        <select class="form-select <?= isset($errors['status_menu']) ? 'is-invalid' : '' ?>" id="status_menu" name="status_menu">
                            <option value="Tersedia" <?= ($data['status_menu'] ?? '') === 'Tersedia' ? 'selected' : '' ?>>Tersedia (Ready Stock)</option>
                            <option value="Habis" <?= ($data['status_menu'] ?? '') === 'Habis' ? 'selected' : '' ?>>Habis (Sold Out)</option>
                        </select>
                        <?php if (isset($errors['status_menu'])): ?>
                            <div class="invalid-feedback"><?= e($errors['status_menu']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi / Komposisi (Opsional)</label>
                        <textarea class="form-control" 
                                  id="deskripsi" 
                                  name="deskripsi" 
                                  rows="3" 
                                  placeholder="Contoh: Dilengkapi dengan telur, kerupuk, dan bumbu rempah pilihan"><?= e($data['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= baseUrl('index.php?page=menu') ?>" class="btn btn-light border">Batal</a>
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
