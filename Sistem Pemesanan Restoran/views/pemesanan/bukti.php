<?php
/**
 * Bukti Pemesanan Kasir (Print-Friendly)
 * Menampilkan:
 * - Nomor pemesanan
 * - Tanggal
 * - Nama pelanggan
 * - Nomor meja
 * - Daftar menu, jumlah, harga satuan, subtotal
 * - Biaya layanan (5%)
 * - Pajak (10%)
 * - Total pembayaran
 * Dilengkapi dengan tombol cetak.
 */

// Hitung subtotal seluruh item pesanan
$subtotalMurni = 0.00;
if (!empty($pemesanan['items'])) {
    foreach ($pemesanan['items'] as $it) {
        $subtotalMurni += (float)$it['subtotal'];
    }
}
$financials = calculateOrderFinancials($subtotalMurni);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pemesanan Kasir - <?= e($pemesanan['kode_pemesanan']) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f6f0;
            color: #2b2f38;
            padding: 20px 0;
        }
        .receipt-card {
            max-width: 580px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e3e8df;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 28px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #d0d7cd;
            padding-bottom: 16px;
            margin-bottom: 18px;
        }
        .receipt-header h4 {
            font-weight: 700;
            color: #19352B;
            margin-bottom: 4px;
        }
        .receipt-section {
            border-bottom: 1px dashed #e0e0e0;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }
        .receipt-table th {
            background-color: #f0f4eb;
            color: #495057;
            font-size: 0.82rem;
            padding: 6px 8px;
        }
        .receipt-table td {
            font-size: 0.88rem;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 0.9rem;
        }
        .grand-total-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-top: 2px solid #19352B;
            border-bottom: 2px solid #19352B;
            margin-top: 8px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #19352B;
        }
        .btn-action-bar {
            max-width: 580px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-card {
                box-shadow: none;
                border: none;
                padding: 10px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi Aksi (Disembunyikan saat dicetak) -->
    <div class="btn-action-bar no-print">
        <a href="<?= baseUrl('index.php?page=pemesanan&action=detail&id=' . $pemesanan['id_pemesanan']) ?>" class="btn btn-outline-secondary btn-sm">
            Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
            Cetak Bukti Pemesanan
        </button>
    </div>

    <!-- Kertas Bukti Pemesanan Kasir -->
    <div class="receipt-card">
        <!-- Metadata Transaksi Langsung -->
        <div class="receipt-section">
            <div class="row g-1 small">
                <div class="col-6">
                    <span class="text-muted">No. Pemesanan:</span><br>
                    <strong class="text-dark"><?= e($pemesanan['kode_pemesanan']) ?></strong>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted">Tanggal:</span><br>
                    <strong class="text-dark"><?= formatTanggalIndonesia($pemesanan['tanggal_pemesanan']) ?></strong>
                </div>
                <div class="col-6 mt-2">
                    <span class="text-muted">Pelanggan:</span><br>
                    <strong class="text-dark"><?= e($pemesanan['nama_pelanggan']) ?></strong>
                    <?php if (!empty($pemesanan['no_telepon'])): ?>
                        <div class="text-muted" style="font-size:0.78rem;"><?= e($pemesanan['no_telepon']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-6 mt-2 text-end">
                    <span class="text-muted">Nomor Meja:</span><br>
                    <strong class="text-dark fs-6">Meja <?= e($pemesanan['nomor_meja']) ?></strong>
                    <div class="text-muted" style="font-size:0.78rem;">Status: <?= e($pemesanan['status_pemesanan']) ?></div>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Menu, Qty, Harga, Subtotal -->
        <div class="mb-3">
            <div class="table-responsive">
                <table class="table table-sm table-bordered receipt-table mb-0">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-center" style="width: 50px;">Qty</th>
                            <th class="text-end" style="width: 100px;">Harga</th>
                            <th class="text-end" style="width: 110px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pemesanan['items'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Belum ada item menu yang dicatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pemesanan['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= e($item['nama_menu']) ?></div>
                                        <?php if (!empty($item['catatan_khusus'])): ?>
                                            <small class="text-muted fst-italic"><?= e($item['catatan_khusus']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center fw-bold"><?= (int)$item['jumlah'] ?></td>
                                    <td class="text-end"><?= formatRupiah($item['harga_satuan']) ?></td>
                                    <td class="text-end fw-semibold"><?= formatRupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rincian Perhitungan Keuangan Otomatis (Subtotal, Biaya Layanan 5%, Pajak 10%, Total Pembayaran) -->
        <div class="pt-2">
            <div class="total-line">
                <span class="text-muted">Subtotal Menu:</span>
                <span class="fw-semibold text-dark"><?= formatRupiah($financials['subtotal']) ?></span>
            </div>
            <div class="total-line">
                <span class="text-muted">Biaya Layanan (5%):</span>
                <span class="fw-semibold text-dark"><?= formatRupiah($financials['biaya_layanan']) ?></span>
            </div>
            <div class="total-line">
                <span class="text-muted">Pajak (10%):</span>
                <span class="fw-semibold text-dark"><?= formatRupiah($financials['pajak']) ?></span>
            </div>
            <div class="grand-total-line">
                <span>TOTAL PEMBAYARAN:</span>
                <span><?= formatRupiah($financials['total_pembayaran']) ?></span>
            </div>
        </div>

        <?php if (!empty($pemesanan['catatan'])): ?>
            <div class="mt-3 p-2 bg-light rounded small border">
                <strong>Catatan Kasir:</strong> <?= e($pemesanan['catatan']) ?>
            </div>
        <?php endif; ?>

        <!-- Footer Nota -->
        <div class="text-center text-muted small mt-4 pt-3 border-top">
            <p class="mb-1">Terima kasih atas kunjungan Anda di Resto Nusantara!</p>
            <p class="mb-0" style="font-size:0.75rem;">Simpan bukti pemesanan ini sebagai bukti pembayaran yang sah.</p>
        </div>
    </div>

</body>
</html>
