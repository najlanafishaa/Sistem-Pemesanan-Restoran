<?php
/**
 * Script Generator Dokumentasi API / Kode Otomatis
 * Menghasilkan file dokumentasi HTML & Markdown dari docblock PHPDoc kelas-kelas sistem.
 * 
 * Unit Kompetensi: Membuat Dokumentasi Kode Program (J.620100.021.02)
 */

$modelsDir = __DIR__ . '/../models';
$outputDir = __DIR__ . '/../docs';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Meja.php';
require_once __DIR__ . '/../models/Menu.php';
require_once __DIR__ . '/../models/Pemesanan.php';
require_once __DIR__ . '/../models/DetailPemesanan.php';

$classes = ['BaseModel', 'Pelanggan', 'Meja', 'Menu', 'Pemesanan', 'DetailPemesanan'];

$mdOutput = "# DOKUMENTASI KODE SUMBER — RESTO NUSANTARA\n\n";
$mdOutput .= "*Dihasilkan secara otomatis pada: " . date('Y-m-d H:i:s') . "*\n\n";
$mdOutput .= "Dokumentasi teknis arsitektur kelas, method, parameter, dan nilai balik sistem pemesanan restoran.\n\n---\n\n";

$htmlCards = '';

foreach ($classes as $className) {
    $ref = new ReflectionClass($className);
    $parent = $ref->getParentClass() ? $ref->getParentClass()->getName() : '-';
    $docComment = trim(str_replace(['/**', '*/', '*'], '', $ref->getDocComment() ?: 'Tidak ada deskripsi class.'));

    $mdOutput .= "## Kelas `{$className}`\n";
    $mdOutput .= "- **Induk (Inheritance)**: `{$parent}`\n";
    $mdOutput .= "- **Deskripsi**: " . trim($docComment) . "\n\n";
    $mdOutput .= "### Daftar Method:\n\n";
    $mdOutput .= "| Nama Method | Visibilitas | Parameter | Return Type | Deskripsi |\n";
    $mdOutput .= "| :--- | :--- | :--- | :--- | :--- |\n";

    $htmlMethods = '';

    foreach ($ref->getMethods() as $method) {
        $mName = $method->getName();
        $visibility = $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private');
        if ($method->isStatic()) $visibility .= ' static';

        // Parameters
        $params = [];
        foreach ($method->getParameters() as $p) {
            $pType = $p->hasType() ? $p->getType()->getName() : 'mixed';
            $pName = '$' . $p->getName();
            if ($p->isDefaultValueAvailable()) {
                $pName .= ' = ' . var_export($p->getDefaultValue(), true);
            }
            $params[] = "{$pType} {$pName}";
        }
        $paramStr = implode(', ', $params);

        // Return type
        $retType = $method->hasReturnType() ? $method->getReturnType()->getName() : 'void';

        // Doc comment
        $mDoc = trim(str_replace(['/**', '*/', '*'], '', $method->getDocComment() ?: '-'));
        $mDocClean = explode("\n", $mDoc)[0] ?? '-';

        $mdOutput .= "| `{$mName}()` | `{$visibility}` | `{$paramStr}` | `{$retType}` | " . trim($mDocClean) . " |\n";

        $htmlMethods .= "<tr>
            <td><code>{$mName}()</code></td>
            <td><span class='badge bg-secondary'>{$visibility}</span></td>
            <td><code>" . htmlspecialchars($paramStr ?: '-') . "</code></td>
            <td><span class='badge bg-info text-dark'>{$retType}</span></td>
            <td>" . htmlspecialchars(trim($mDocClean)) . "</td>
        </tr>";
    }

    $mdOutput .= "\n---\n\n";

    $htmlCards .= "
    <div class='card mb-4 shadow-sm border-0'>
        <div class='card-header text-white' style='background:#19352B;'>
            <div class='d-flex justify-content-between align-items-center'>
                <h4 class='mb-0 fw-bold'>class {$className}</h4>
                <span class='badge' style='background:#557A59;'>Extends: {$parent}</span>
            </div>
            <small class='text-light d-block mt-1'>" . htmlspecialchars(trim($docComment)) . "</small>
        </div>
        <div class='card-body p-0'>
            <div class='table-responsive'>
                <table class='table table-hover table-striped mb-0'>
                    <thead style='background:#F0F4EB;'>
                        <tr>
                            <th>Method</th>
                            <th>Visibility</th>
                            <th>Parameter</th>
                            <th>Return</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$htmlMethods}
                    </tbody>
                </table>
            </div>
        </div>
    </div>";
}

// Simpan file Markdown
file_put_contents($outputDir . '/API_DOCUMENTATION.md', $mdOutput);

// Simpan file HTML Interaktif
$htmlTemplate = "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Dokumentasi Kode Program (PHPDoc) — Resto Nusantara</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: #F7F6F0; color: #26352B; font-family: 'Segoe UI', sans-serif; padding-bottom: 50px; }
        .hero { background: #19352B; color: #fff; padding: 40px 0 30px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class='hero text-center'>
        <div class='container'>
            <h2 class='fw-bold mb-1'>DOKUMENTASI KODE SUMBER RESMI</h2>
            <p class='mb-0 text-light' style='opacity:0.85;'>Generated by Automatic Reflection API Tool &bull; Unit Kompetensi J.620100.021.02</p>
        </div>
    </div>
    <div class='container'>
        <div class='alert alert-success d-flex justify-content-between align-items-center mb-4'>
            <div><i class='bi bi-check-circle-fill me-2'></i>Dokumentasi otomatis berhasil diekstrak dari reflection metadata kelas PHP Native.</div>
            <a href='../index.php?page=dashboard' class='btn btn-sm btn-outline-dark'>&larr; Dashboard</a>
        </div>
        {$htmlCards}
    </div>
</body>
</html>";

file_put_contents($outputDir . '/index.html', $htmlTemplate);

echo "[OK] Dokumentasi otomatis berhasil dibuat di:\n";
echo " - docs/API_DOCUMENTATION.md (Markdown)\n";
echo " - docs/index.html (HTML Viewer)\n";
