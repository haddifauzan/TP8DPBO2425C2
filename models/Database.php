<?php

require_once __DIR__ . '/../config.php';

// KELAS PENGELOLA KONEKSI BASIS DATA (DATABASE SINGLETON)
// Menerapkan pola Singleton agar hanya satu instance koneksi PDO yang aktif di seluruh aplikasi.
class Database {
    // Properti statis penyimpan instance koneksi PDO tunggal.
    private static $connection = null;

    // METODE KONEKSI DATABASE
    // Mengembalikan objek PDO yang siap digunakan untuk mengeksekusi query.
    public static function connect()
    {
        // Menggunakan kembali koneksi yang sudah terbentuk sebelumnya jika tersedia (efisiensi memori).
        if (self::$connection !== null) {
            return self::$connection;
        }

        // Data Source Name (DSN) yang memuat driver, host, nama database, dan set karakter utf8mb4.
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        try {
            // INSTANSIASI KONEKSI PDO DENGAN OPSI KEAMANAN TINGGI
            self::$connection = new PDO($dsn, DB_USER, DB_PASS, [
                // Melempar PDOException otomatis saat query gagal agar mudah ditangkap blok try-catch.
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Format hasil fetch default berupa array asosiatif (nama_kolom => nilai).
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Menonaktifkan emulasi prepared statement untuk mencegah celah SQL Injection di sisi database engine.
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            return self::$connection;
        } catch (PDOException $e) {
            // Pencatatan pesan error teknis ke log server untuk keperluan debugging pengembang.
            error_log('Database Connection Error: ' . $e->getMessage());

            // Tampilan pesan ramah pengguna jika server MySQL tidak dapat dihubungi.
            die('Koneksi database gagal. Periksa konfigurasi database dan pastikan server MySQL aktif.');
        }
    }
}
