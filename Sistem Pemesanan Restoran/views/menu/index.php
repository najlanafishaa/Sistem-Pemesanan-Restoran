<?php
require_once __DIR__ . '/../layout/header.php';
$currentKat = $_GET['kategori'] ?? '';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Menu</h4>
        <p class="text-muted mb-0">Kelola daftar hidangan makanan, minuman, dan status ketersediaan.</p>
    </div>
    <div>
        <a href="<?= baseUrl('index.php?page=menu&action=create') ?>" class="btn btn-primary">
            Tambah Menu
        </a>
    </div>
</div>

<!-- Filter Kategori Tabs Sederhana -->
<div class="card mb-3 p-2 bg-white">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted small fw-bold ms-2 me-1">Kategori:</span>
        <a href="<?= baseUrl('index.php?page=menu') ?>" class="btn btn-sm <?= empty($currentKat) ? 'btn-primary' : 'btn-outline-secondary' ?>">
            Semua
        </a>
        <a href="<?= baseUrl('index.php?page=menu&kategori=Makanan') ?>" class="btn btn-sm <?= $currentKat === 'Makanan' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            Makanan
        </a>
        <a href="<?= baseUrl('index.php?page=menu&kategori=Minuman') ?>" class="btn btn-sm <?= $currentKat === 'Minuman' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            Minuman
        </a>
        <a href="<?= baseUrl('index.php?page=menu&kategori=Dessert') ?>" class="btn btn-sm <?= $currentKat === 'Dessert' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            Dessert
        </a>
        <a href="<?= baseUrl('index.php?page=menu&kategori=Paket') ?>" class="btn btn-sm <?= $currentKat === 'Paket' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            Paket
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Daftar Menu</h6>
        <span class="badge bg-primary"><?= count($menuList) ?> Menu</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($menuList)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada menu yang ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($menuList as $m): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?= e($m['nama_menu']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= e($m['kategori']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6"><?= formatRupiah($m['harga']) ?></span>
                            </td>
                            <td><?= getStatusBadge($m['status_menu']) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= baseUrl('index.php?page=menu&action=edit&id=' . $m['id_menu']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?= baseUrl('index.php?page=menu&action=delete') ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $m['id_menu'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-name="menu '<?= e($m['nama_menu']) ?>'" title="Hapus">
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
