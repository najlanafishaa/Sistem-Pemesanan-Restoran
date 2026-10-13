<?php
/**
 * Layout Header — Resto Nusantara
 */
require_once __DIR__ . '/../../config/helpers.php';
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Sistem Pemesanan Restoran') ?> — Resto Nusantara</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS (tema Forest Green + Ivory) -->
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay d-md-none"></div>

    <!-- Content -->
    <div id="content">
        <!-- Navbar -->
        <?php require_once __DIR__ . '/navbar.php'; ?>

        <!-- Main Content -->
        <div class="main-container">
            <!-- Flash Message -->
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show d-flex align-items-center gap-2 mb-4"
                     role="alert">
                    <?php if ($flash['type'] === 'success'): ?>
                        <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                    <?php elseif ($flash['type'] === 'danger'): ?>
                        <i class="bi bi-exclamation-octagon-fill fs-5 flex-shrink-0"></i>
                    <?php elseif ($flash['type'] === 'warning'): ?>
                        <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                    <?php else: ?>
                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                    <?php endif; ?>
                    <div class="flex-grow-1"><?= e($flash['message']) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>
