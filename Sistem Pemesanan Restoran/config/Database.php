<?php
/**
 * Class Database
 * Mengelola koneksi basis data MySQL menggunakan MySQLi.
 * Menerapkan prinsip OOP dan Singleton Pattern untuk efisiensi koneksi.
 */
class Database {
    private static ?mysqli $instance = null;

    private string $host     = '127.0.0.1';
    private int    $port     = 3306;
    private string $db_name  = 'db_pemesanan_restoran';
    private string $username = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';

    /** Private constructor — cegah instansiasi langsung (Singleton) */
    private function __construct() {}

    /**
     * Mendapatkan instance koneksi MySQLi.
     *
     * @return mysqli
     */
    public static function getConnection(): mysqli {
        if (self::$instance === null) {
            $db = new self();

            // Nonaktifkan laporan error bawaan MySQLi agar bisa ditangani sendiri
            mysqli_report(MYSQLI_REPORT_OFF);

            $conn = new mysqli(
                $db->host,
                $db->username,
                $db->password,
                $db->db_name,
                $db->port
            );

            if ($conn->connect_errno) {
                die(
                    "<div style='font-family:sans-serif;padding:20px;margin:20px;border:1px solid #dc3545;"
                    . "background:#f8d7da;color:#721c24;border-radius:8px;'>"
                    . "<h3>Koneksi Database Gagal!</h3>"
                    . "<p>Pastikan MySQL/MariaDB di Laragon / XAMPP sudah aktif dan database "
                    . "<strong>db_pemesanan_restoran</strong> sudah dibuat.</p>"
                    . "<small>Detail error: " . htmlspecialchars($conn->connect_error) . "</small>"
                    . "</div>"
                );
            }

            // Set charset UTF-8 untuk mendukung karakter Indonesia
            $conn->set_charset($db->charset);

            self::$instance = $conn;
        }

        return self::$instance;
    }
}
