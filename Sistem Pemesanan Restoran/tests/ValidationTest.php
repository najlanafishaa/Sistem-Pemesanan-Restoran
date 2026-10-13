<?php
/**
 * Unit Test: Validasi Data
 * Menguji logika validasi input pelanggan, meja, dan menu.
 */

require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Menu.php';

class ValidationTest {

    public static function testValidCustomerData(): bool {
        $data = [
            'nama_pelanggan' => 'Andi Wijaya',
            'no_telepon'     => '08123456789',
            'email'          => 'andi@gmail.com'
        ];
        $errors = Pelanggan::validate($data);
        return empty($errors);
    }

    public static function testEmptyCustomerName(): bool {
        $data = [
            'nama_pelanggan' => '',
            'no_telepon'     => '08123456789'
        ];
        $errors = Pelanggan::validate($data);
        return isset($errors['nama_pelanggan']);
    }

    public static function testInvalidCustomerPhone(): bool {
        $data = [
            'nama_pelanggan' => 'Budi Santoso',
            'no_telepon'     => 'abc-bukan-angka'
        ];
        $errors = Pelanggan::validate($data);
        return isset($errors['no_telepon']);
    }

    public static function testInvalidCustomerEmail(): bool {
        $data = [
            'nama_pelanggan' => 'Budi Santoso',
            'no_telepon'     => '08123456789',
            'email'          => 'bukan-email-valid'
        ];
        $errors = Pelanggan::validate($data);
        return isset($errors['email']);
    }

    public static function testValidMenuData(): bool {
        $data = [
            'nama_menu' => 'Ayam Bakar',
            'kategori'  => 'Makanan',
            'harga'     => 45000,
        ];
        $errors = Menu::validate($data);
        return empty($errors);
    }

    public static function testNegativeMenuPrice(): bool {
        $data = [
            'nama_menu' => 'Ayam Bakar',
            'kategori'  => 'Makanan',
            'harga'     => -10000,
        ];
        $errors = Menu::validate($data);
        return isset($errors['harga']);
    }

    public static function testInvalidMenuCategory(): bool {
        $data = [
            'nama_menu' => 'Ayam Bakar',
            'kategori'  => 'KategoriPalsu',
            'harga'     => 45000,
        ];
        $errors = Menu::validate($data);
        return isset($errors['kategori']);
    }
}
