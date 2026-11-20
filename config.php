<?php

// PENGATURAN KONEKSI DATABASE
// Menentukan parameter kredensial untuk menghubungkan aplikasi ke server database MySQL.
define('DB_HOST', 'localhost'); // Host server database (default lokal XAMPP)
define('DB_NAME', 'tp_mvc25');   // Nama skema database yang digunakan
define('DB_USER', 'root');       // Username akses basis data
define('DB_PASS', '');           // Password akses basis data (kosong secara default pada XAMPP)

// IDENTITAS DAN KONFIGURASI APLIKASI
// Nama aplikasi yang ditampilkan pada judul halaman tab browser dan layout.
define('APP_NAME', 'Lecturer Management FPMIPA UPI');

// Lokasi path direktori root proyek di sistem berkas (filesystem) server.
define('ROOT_PATH', __DIR__);

// PENGATURAN BASE URL DINAMIS
// Menyesuaikan alamat URL dasar aplikasi secara otomatis mengikuti letak folder pada localhost.
// Mengubah pemisah direktori Windows (\) menjadi slash URL (/), lalu memotong trailing slash.
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', $basePath === '/' ? '' : rtrim($basePath, '/'));
