<?php
/**
 * Layout Sidebar — Forest Green (#19352B) Theme
 */
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<nav id="sidebar">
    <!-- Brand / Logo Sederhana -->
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div style="
                width:36px; height:36px; border-radius:6px;
                background:#557A59; color:#fff;
                display:flex; align-items:center; justify-content:center;
                font-weight:700; font-size:0.9rem;">
                RN
            </div>
            <div>
                <h6 class="mb-0 text-white fw-bold" style="letter-spacing:0.5px;">
                    Resto Nusantara
                </h6>
                <small style="color:#A5B58A; font-size:0.75rem;">Sistem Pemesanan</small>
            </div>
        </div>
    </div>

    <!-- Menu Navigasi Normal & Alami -->
    <ul class="components">
        <li class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= baseUrl('index.php?page=dashboard') ?>">Dashboard</a>
        </li>
        <li class="<?= $currentPage === 'pelanggan' ? 'active' : '' ?>">
            <a href="<?= baseUrl('index.php?page=pelanggan') ?>">Pelanggan</a>
        </li>
        <li class="<?= $currentPage === 'meja' ? 'active' : '' ?>">
            <a href="<?= baseUrl('index.php?page=meja') ?>">Meja</a>
        </li>
        <li class="<?= $currentPage === 'menu' ? 'active' : '' ?>">
            <a href="<?= baseUrl('index.php?page=menu') ?>">Menu</a>
        </li>
        <li class="<?= $currentPage === 'pemesanan' ? 'active' : '' ?>">
            <a href="<?= baseUrl('index.php?page=pemesanan') ?>">Pemesanan</a>
        </li>
        <li class="<?= $currentPage === 'laporan' ? 'active' : '' ?>">
            <a href="<?= baseUrl('index.php?page=laporan') ?>">Laporan</a>
        </li>
    </ul>
</nav>
