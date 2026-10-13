<?php
/**
 * Abstract Class BaseModel
 * Base model yang mengimplementasikan dasar Inheritance & Polymorphism
 * untuk seluruh entitas sistem (Pelanggan, Meja, Pemesanan, Menu).
 */

require_once __DIR__ . '/../config/Database.php';

abstract class BaseModel {
    /**
     * Objek koneksi basis data (MySQLi)
     * Diturunkan ke seluruh kelas anak (Inheritance)
     */
    protected mysqli $db;

    /**
     * Nama tabel di database
     */
    protected string $table;

    /**
     * Primary key dari tabel
     */
    protected string $primaryKey;

    /**
     * Constructor BaseModel - Menginisialisasi koneksi database Singleton
     */
    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Mendapatkan nama tabel dari entitas
     * 
     * @return string
     */
    public function getTable(): string {
        return $this->table;
    }

    /**
     * Menghitung total data pada tabel
     * 
     * @return int
     */
    public function count(): int {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM {$this->table}");
        if ($result) {
            $row = $result->fetch_assoc();
            return (int)($row['total'] ?? 0);
        }
        return 0;
    }

    /**
     * Polymorphism: Setiap model wajib mengimplementasikan representasi
     * informasi ringkas objeknya masing-masing.
     * 
     * @param int $id
     * @return string
     */
    abstract public function getSummaryInfo(int $id): string;
}
