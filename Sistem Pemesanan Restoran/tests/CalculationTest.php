<?php
/**
 * Unit Test: Perhitungan Total & Pemformatan
 * Menguji kalkulasi subtotal item menu, total pesanan, dan format mata uang Rupiah.
 */

require_once __DIR__ . '/../models/DetailPemesanan.php';
require_once __DIR__ . '/../config/helpers.php';

class CalculationTest {

    public static function testCalculateSubtotalNormal(): bool {
        $harga = 35000.00;
        $qty = 3;
        $expected = 115000.00;
        return DetailPemesanan::calculateSubtotal($harga, $qty) === $expected;
    }

    public static function testCalculateSubtotalZeroQty(): bool {
        $harga = 35000.00;
        $qty = 0;
        $expected = 0.00;
        return DetailPemesanan::calculateSubtotal($harga, $qty) === $expected;
    }

    public static function testCalculateSubtotalNegativePrice(): bool {
        $harga = -20000.00;
        $qty = 2;
        $expected = 0.00;
        return DetailPemesanan::calculateSubtotal($harga, $qty) === $expected;
    }

    public static function testFormatRupiahNoDecimal(): bool {
        $angka = 75000;
        $hasil = formatRupiah($angka);
        return $hasil === 'Rp 75.000';
    }

    public static function testFormatRupiahWithDecimal(): bool {
        $angka = 75000;
        $hasil = formatRupiah($angka, true);
        return $hasil === 'Rp 75.000,00';
    }
}
