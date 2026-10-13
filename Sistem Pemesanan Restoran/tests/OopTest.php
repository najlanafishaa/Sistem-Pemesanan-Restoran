<?php
/**
 * Test Kasus OOP: Inheritance & Polymorphism
 * Menguji pewarisan BaseModel dan perilaku polymorphic dari method getSummaryInfo()
 */

require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Meja.php';
require_once __DIR__ . '/../models/Pemesanan.php';
require_once __DIR__ . '/../models/Menu.php';

class OopTest {
    /**
     * Uji Inheritance: Memastikan semua model adalah turunan dari BaseModel
     */
    public static function testInheritanceHierarchy(): bool {
        $pelanggan = new Pelanggan();
        $meja      = new Meja();
        $pemesanan = new Pemesanan();
        $menu      = new Menu();

        return ($pelanggan instanceof BaseModel)
            && ($meja instanceof BaseModel)
            && ($pemesanan instanceof BaseModel)
            && ($menu instanceof BaseModel);
    }

    /**
     * Uji Inheritance: Memastikan property getTable() diwariskan dengan benar
     */
    public static function testInheritedMethods(): bool {
        $pelanggan = new Pelanggan();
        $meja      = new Meja();

        return ($pelanggan->getTable() === 'pelanggan')
            && ($meja->getTable() === 'meja');
    }

    /**
     * Uji Polymorphism: Memanggil method getSummaryInfo() secara dinamis pada berbagai objek turunan BaseModel
     */
    public static function testPolymorphismBehavior(): bool {
        $objects = [
            new Pelanggan(),
            new Meja(),
            new Pemesanan(),
            new Menu()
        ];

        // Seluruh objek turunan BaseModel harus dapat memanggil getSummaryInfo() dan mengembalikan string
        foreach ($objects as $obj) {
            if (!method_exists($obj, 'getSummaryInfo')) {
                return false;
            }
            $summary = $obj->getSummaryInfo(999999); // ID dummy
            if (!is_string($summary) || empty($summary)) {
                return false;
            }
        }

        return true;
    }
}
