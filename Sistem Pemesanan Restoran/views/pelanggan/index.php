<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Pelanggan</h4>
        <p class="text-muted mb-0">Kelola data pelanggan restoran dan kontak pemesan.</p>
    </div>
    <div>
        <a href="<?= baseUrl('index.php?page=pelanggan&action=create') ?>" class="btn btn-primary">
            Tambah Pelanggan
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Daftar Pelanggan</h6>
        <span class="badge bg-primary"><?= count($pelangganList) ?> Pelanggan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Pelanggan</th>
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pelangganList)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data pelanggan yang tersimpan.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($pelangganList as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($p['nama_pelanggan']) ?></div>
                            </td>
                            <td>
                                <?= e($p['no_telepon']) ?>
                            </td>
                            <td>
                                <?= !empty($p['email']) ? e($p['email']) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <?= !empty($p['alamat']) ? e($p['alamat']) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= baseUrl('index.php?page=pelanggan&action=edit&id=' . $p['id_pelanggan']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?= baseUrl('index.php?page=pelanggan&action=delete') ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $p['id_pelanggan'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-name="pelanggan '<?= e($p['nama_pelanggan']) ?>'" title="Hapus">
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
