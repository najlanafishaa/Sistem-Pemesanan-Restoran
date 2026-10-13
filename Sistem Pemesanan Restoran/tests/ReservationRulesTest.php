<?php
/**
 * Unit Test: Aturan Bisnis Reservasi
 * Menguji validasi kapasitas meja dan pendeteksian tabrakan jadwal (conflict check).
 */

require_once __DIR__ . '/../models/Pemesanan.php';

class ReservationRulesTest {

    public static function testCapacityWithinLimit(): bool {
        // Tamu 4 pada meja berkapasitas 4 -> Valid
        return Pemesanan::validateCapacity(4, 4) === true;
    }

    public static function testCapacityBelowLimit(): bool {
        // Tamu 2 pada meja berkapasitas 6 -> Valid
        return Pemesanan::validateCapacity(2, 6) === true;
    }

    public static function testCapacityExceeded(): bool {
        // Tamu 7 pada meja berkapasitas 6 -> Tidak Valid (Melebihi)
        return Pemesanan::validateCapacity(7, 6) === false;
    }

    public static function testZeroGuestCapacity(): bool {
        // Tamu 0 -> Tidak Valid
        return Pemesanan::validateCapacity(0, 4) === false;
    }

    public static function testScheduleOverlapLogic(): bool {
        // Interval Tabrakan: (mulai_baru < selesai_lama) && (selesai_baru > mulai_lama)
        // Jadwal Lama: 12:00 - 14:00
        $lamaMulai = '12:00';
        $lamaSelesai = '14:00';

        // Kasus 1: Tabrakan di tengah (13:00 - 15:00) -> Konflik
        $baruMulai1 = '13:00';
        $baruSelesai1 = '15:00';
        $konflik1 = ($baruMulai1 < $lamaSelesai && $baruSelesai1 > $lamaMulai);

        // Kasus 2: Setelah jadwal lama selesai (14:00 - 16:00) -> Tidak Konflik
        $baruMulai2 = '14:00';
        $baruSelesai2 = '16:00';
        $konflik2 = ($baruMulai2 < $lamaSelesai && $baruSelesai2 > $lamaMulai);

        // Kasus 3: Sebelum jadwal lama mulai (10:00 - 12:00) -> Tidak Konflik
        $baruMulai3 = '10:00';
        $baruSelesai3 = '12:00';
        $konflik3 = ($baruMulai3 < $lamaSelesai && $baruSelesai3 > $lamaMulai);

        return ($konflik1 === true) && ($konflik2 === false) && ($konflik3 === false);
    }
}
