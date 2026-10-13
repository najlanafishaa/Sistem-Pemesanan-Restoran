<?php
/**
 * Helper Functions (Pemrograman Terstruktur)
 * Berisi sekumpulan fungsi pembantu umum untuk sanitasi data, pemformatan,
 * flash message, dan keamanan.
 */

// Set zona waktu Indonesia (WIB) agar tanggal selalu terkini (up to date)
date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Menghasilkan URL dasar (Base URL) secara dinamis
 * 
 * @param string $path
 * @return string
 */
function baseUrl(string $path = ''): string {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = rtrim($scriptDir, '/');
    if ($base === '' || $base === '.') {
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * Mengamankan output teks untuk mencegah Cross-Site Scripting (XSS)
 * 
 * @param mixed $value
 * @return string
 */
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Membersihkan input data pengguna
 * 
 * @param string|null $data
 * @return string
 */
function sanitize(?string $data): string {
    if ($data === null) {
        return '';
    }
    return trim(strip_tags($data));
}

/**
 * Memformat angka menjadi format mata uang Rupiah (IDR)
 * 
 * @param float|int|string $angka
 * @param bool $denganDesimal
 * @return string
 */
function formatRupiah($angka, bool $denganDesimal = false): string {
    $num = (float)$angka;
    if ($denganDesimal) {
        return 'Rp ' . number_format($num, 2, ',', '.');
    }
    return 'Rp ' . number_format($num, 0, ',', '.');
}

/**
 * Menghitung rincian keuangan pemesanan berdasarkan rumus:
 * - Subtotal = jumlah seluruh (harga menu x kuantitas)
 * - Biaya layanan = subtotal x 5% (0.05)
 * - Pajak = subtotal x 10% (0.10)
 * - Total pembayaran = subtotal + biaya layanan + pajak
 * 
 * @param float|int $subtotal
 * @return array ['subtotal', 'biaya_layanan', 'pajak', 'total_pembayaran']
 */
function calculateOrderFinancials(float $subtotal): array {
    $subtotal = max(0.0, (float)$subtotal);
    $biayaLayanan = round($subtotal * 0.05, 2);
    $pajak = round($subtotal * 0.10, 2);
    $totalPembayaran = round($subtotal + $biayaLayanan + $pajak, 2);

    return [
        'subtotal'         => $subtotal,
        'biaya_layanan'    => $biayaLayanan,
        'pajak'            => $pajak,
        'total_pembayaran' => $totalPembayaran,
    ];
}

/**
 * Memformat tanggal ke format standar Indonesia
 * 
 * @param string|null $tanggal
 * @param bool $denganHari
 * @return string
 */
function formatTanggalIndonesia(?string $tanggal, bool $denganHari = false): string {
    if (!$tanggal) return '-';
    $timestamp = strtotime($tanggal);
    if (!$timestamp) return '-';

    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hariIndo = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $d = date('j', $timestamp);
    $m = (int)date('n', $timestamp);
    $y = date('Y', $timestamp);

    $hasil = "$d " . ($bulanIndo[$m] ?? '') . " $y";

    if ($denganHari) {
        $hariInggris = date('l', $timestamp);
        $hari = $hariIndo[$hariInggris] ?? '';
        return "$hari, $hasil";
    }

    return $hasil;
}

/**
 * Memformat waktu (jam:menit)
 * 
 * @param string|null $waktu
 * @return string
 */
function formatWaktu(?string $waktu): string {
    if (!$waktu) return '-';
    return date('H:i', strtotime($waktu));
}

/**
 * Menyimpan notifikasi pesan flash (Session)
 * 
 * @param string $type ('success', 'danger', 'warning', 'info')
 * @param string $message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Mengambil dan menghapus notifikasi pesan flash
 * 
 * @return array|null
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect ke halaman tertentu
 * 
 * @param string $url
 */
function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

/**
 * Menghasilkan token CSRF untuk keamanan form
 * 
 * @return string
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Memvalidasi token CSRF
 * 
 * @param string|null $token
 * @return bool
 */
function verifyCsrfToken(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Menghasilkan badge status HTML dengan warna semantik yang mudah dibedakan.
 * Setiap status memiliki warna unik agar tidak membingungkan pengguna.
 *
 * @param string $status
 * @return string
 */
function getStatusBadge(string $status): string {
    // Style inline agar tidak bergantung pada override Bootstrap primary.
    // Status reservasi: kuning=Menunggu, teal=Dikonfirmasi, hijau=Selesai, merah=Dibatalkan
    $styles = [
        // Status Pemesanan (Sederhana & Konsisten)
        'Diproses'     => 'background:#d97706; color:#fff;',               // Amber/Kuning
        'Selesai'      => 'background:#557A59; color:#fff;',               // Hijau
        'Dibatalkan'   => 'background:#dc2626; color:#fff;',               // Merah

        // Status Meja
        'Tersedia'     => 'background:#557A59; color:#fff;',               // Hijau
        'Terisi'       => 'background:#dc2626; color:#fff;',               // Merah
        'Perbaikan'    => 'background:#667085; color:#fff;',               // Abu-abu

        // Status Menu
        'Habis'        => 'background:#dc2626; color:#fff;',               // Merah
    ];

    $style = $styles[$status] ?? 'background:#667085; color:#fff;';
    return '<span class="badge px-2 py-1" style="' . $style . '">' . e($status) . '</span>';
}
