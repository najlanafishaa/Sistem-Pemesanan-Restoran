<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Meja</h4>
        <p class="text-muted mb-0">Kelola nomor meja, kapasitas, dan status ketersediaan meja.</p>
    </div>
    <div>
        <a href="<?= baseUrl('index.php?page=meja&action=create') ?>" class="btn btn-primary">
            Tambah Meja
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Daftar Meja</h6>
        <span class="badge bg-primary"><?= count($mejaList) ?> Meja</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nomor Meja</th>
                    <th>Kapasitas</th>
                    <th>Status Meja</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mejaList)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data meja.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($mejaList as $m): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?= e($m['nomor_meja']) ?></div>
                            </td>
                            <td>
                                <?= e($m['kapasitas']) ?> Orang
                            </td>
                            <td><?= getStatusBadge($m['status_meja']) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= baseUrl('index.php?page=meja&action=edit&id=' . $m['id_meja']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?= baseUrl('index.php?page=meja&action=delete') ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $m['id_meja'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-name="meja '<?= e($m['nomor_meja']) ?>'" title="Hapus">
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
