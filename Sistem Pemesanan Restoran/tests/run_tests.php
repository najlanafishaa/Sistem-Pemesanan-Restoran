<?php
/**
 * Test Runner CLI & Web
 * Menjalankan seluruh pengujian unit program (Unit Testing)
 * Unit Kompetensi: Melaksanakan Pengujian Unit Program (J.620100.033.02)
 */

require_once __DIR__ . '/ValidationTest.php';
require_once __DIR__ . '/CalculationTest.php';
require_once __DIR__ . '/ReservationRulesTest.php';
require_once __DIR__ . '/OopTest.php';

$isCli = (php_sapi_name() === 'cli');

function printResult(string $testName, bool $status, bool $isCli): void {
    if ($isCli) {
        $badge = $status ? "\033[32m[PASS]\033[0m" : "\033[31m[FAIL]\033[0m";
        echo "{$badge} {$testName}\n";
    } else {
        $badge = $status ? "<span style='color:green;font-weight:bold;'>[PASS]</span>" : "<span style='color:red;font-weight:bold;'>[FAIL]</span>";
        echo "<div>{$badge} {$testName}</div>";
    }
}

$tests = [
    // Validation Tests
    'ValidationTest: Pelanggan Valid'            => fn() => ValidationTest::testValidCustomerData(),
    'ValidationTest: Nama Pelanggan Kosong'       => fn() => ValidationTest::testEmptyCustomerName(),
    'ValidationTest: No Telepon Format Salah'    => fn() => ValidationTest::testInvalidCustomerPhone(),
    'ValidationTest: Email Format Salah'         => fn() => ValidationTest::testInvalidCustomerEmail(),
    'ValidationTest: Menu Valid'                 => fn() => ValidationTest::testValidMenuData(),
    'ValidationTest: Harga Menu Negatif'         => fn() => ValidationTest::testNegativeMenuPrice(),
    'ValidationTest: Kategori Menu Tidak Valid'  => fn() => ValidationTest::testInvalidMenuCategory(),

    // Calculation Tests
    'CalculationTest: Subtotal Normal'           => fn() => CalculationTest::testCalculateSubtotalNormal(),
    'CalculationTest: Subtotal Qty Nol'          => fn() => CalculationTest::testCalculateSubtotalZeroQty(),
    'CalculationTest: Subtotal Harga Negatif'    => fn() => CalculationTest::testCalculateSubtotalNegativePrice(),
    'CalculationTest: Format Rupiah Standar'     => fn() => CalculationTest::testFormatRupiahNoDecimal(),
    'CalculationTest: Format Rupiah Desimal'     => fn() => CalculationTest::testFormatRupiahWithDecimal(),

    // Reservation Rules Tests
    'ReservationTest: Kapasitas Pas'             => fn() => ReservationRulesTest::testCapacityWithinLimit(),
    'ReservationTest: Kapasitas Kurang dari Max' => fn() => ReservationRulesTest::testCapacityBelowLimit(),
    'ReservationTest: Kapasitas Melebihi Batas'  => fn() => ReservationRulesTest::testCapacityExceeded(),
    'ReservationTest: Tamu Nol'                  => fn() => ReservationRulesTest::testZeroGuestCapacity(),
    'ReservationTest: Pencegahan Konflik Jadwal' => fn() => ReservationRulesTest::testScheduleOverlapLogic(),

    // OOP Architecture Tests (Inheritance & Polymorphism)
    'OopTest: Hirarki Pewarisan (Inheritance)'    => fn() => OopTest::testInheritanceHierarchy(),
    'OopTest: Method Warisan (Inherited Methods)' => fn() => OopTest::testInheritedMethods(),
    'OopTest: Perilaku Polimorfisme (Polymorphism)' => fn() => OopTest::testPolymorphismBehavior(),
];

if (!$isCli) {
    echo "<!DOCTYPE html><html><head><title>Hasil Pengujian Unit Program</title>";
    echo "<style>body{font-family:sans-serif;padding:30px;line-height:1.6;background:#f8fafc;}</style></head><body>";
    echo "<h2>Laporan Pengujian Unit Program (Unit Testing)</h2><hr>";
} else {
    echo "\n=======================================================\n";
    echo "  PENGUJIAN UNIT PROGRAM - SISTEM PEMESANAN RESTORAN   \n";
    echo "=======================================================\n\n";
}

$passed = 0;
$failed = 0;

foreach ($tests as $name => $fn) {
    try {
        $result = $fn();
        if ($result) {
            $passed++;
            printResult($name, true, $isCli);
        } else {
            $failed++;
            printResult($name, false, $isCli);
        }
    } catch (Throwable $e) {
        $failed++;
        printResult($name . ' (Error: ' . $e->getMessage() . ')', false, $isCli);
    }
}

$total = $passed + $failed;

if ($isCli) {
    echo "\n-------------------------------------------------------\n";
    echo "Hasil Akhir: Total: {$total} | Lolos: \033[32m{$passed}\033[0m | Gagal: \033[31m{$failed}\033[0m\n";
    echo "=======================================================\n\n";
    exit($failed > 0 ? 1 : 0);
} else {
    echo "<hr><h4>Ringkasan: Total: {$total} | Lolos: <span style='color:green;'>{$passed}</span> | Gagal: <span style='color:red;'>{$failed}</span></h4>";
    echo "<a href='../index.php'>&larr; Kembali ke Aplikasi</a>";
    echo "</body></html>";
}
