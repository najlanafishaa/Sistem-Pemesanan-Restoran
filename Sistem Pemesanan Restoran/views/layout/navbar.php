<?php
/**
 * Layout Navbar — White + Dark Green Text
 */
?>
<div class="top-navbar">
    <!-- Kiri: Toggle (mobile) + Judul Halaman -->
    <div class="d-flex align-items-center gap-3">
        <button type="button" id="sidebarCollapse"
                class="btn btn-sm d-md-none"
                style="background:transparent; border:1px solid var(--color-border); color:var(--color-text);">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="d-none d-sm-block">
            <span class="fw-bold" style="color:var(--color-text); font-size:1.05rem;"><?= e($pageTitle ?? 'Sistem Pemesanan Restoran') ?></span>
            <small class="text-muted ms-2">&bull; <?= formatTanggalIndonesia(date('Y-m-d'), true) ?></small>
        </div>
    </div>

    <!-- Kanan: Profil Admin Sederhana -->
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-2 py-1" style="font-weight:600;">
            Admin Resto
        </span>
    </div>
</div>
