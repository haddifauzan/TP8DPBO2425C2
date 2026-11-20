<?php

// TITIK MASUK UTAMA APLIKASI (FRONT CONTROLLER)
// Seluruh permintaan HTTP dari klien diarahkan dan dikelola melalui file ini.

// INISIALISASI SESI
// Memulai sesi PHP jika belum aktif untuk mendukung penyimpanan flash message dan token CSRF.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PEMUATAN DEPENDENSI DASAR
// Memuat berkas konfigurasi, koneksi basis data, dan renderer template tampilan.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/views/Template.php';

// PEMUATAN CONTROLLER
// Memuat seluruh controller yang menangani logika bisnis setiap entitas.
require_once __DIR__ . '/controllers/StudyProgramController.php';
require_once __DIR__ . '/controllers/LecturerController.php';
require_once __DIR__ . '/controllers/CourseController.php';

// DAFTAR RUTE DAN CONTROLLER (ROUTING MAP)
// Memetakan parameter query 'page' dari URL ke kelas controller yang bertanggung jawab.
$routes = [
    'study_programs' => StudyProgramController::class,
    'lecturers'      => LecturerController::class,
    'courses'        => CourseController::class
];

// DAFTAR AKSI YANG DIIZINKAN (ACTION WHITELIST)
// Membatasi metode apa saja yang boleh dieksekusi secara publik via URL demi alasan keamanan.
$allowedActions = [
    'index',  // Menampilkan daftar data
    'create', // Menampilkan formulir penambahan
    'store',  // Memproses penyimpanan data baru
    'edit',   // Menampilkan formulir pengubahan data
    'update', // Memproses pembaruan data yang sudah ada
    'delete'  // Menghapus data dari basis data
];

// PARSING PARAMETER REQUEST
// Menentukan entitas halaman default ke 'lecturers' jika tidak disertakan dalam query string.
$page = $_GET['page'] ?? 'lecturers';

// Menentukan aksi default ke 'index' (daftar data).
$action = $_GET['action'] ?? 'index';

// Memvalidasi parameter 'id' harus berupa bilangan bulat positif.
$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

// VALIDASI KEBERADAAN RUTE
// Menghentikan proses dan mengirim respons 404 jika halaman tidak terdaftar pada whitelist rute.
if (!isset($routes[$page])) {
    http_response_code(404);
    die('Halaman tidak ditemukan.');
}

// VALIDASI AKSI
// Memastikan aksi yang dipanggil berada dalam daftar aksi yang diizinkan.
if (!in_array($action, $allowedActions, true)) {
    http_response_code(404);
    die('Aksi tidak ditemukan.');
}

// VALIDASI INTEGRITAS PARAMETER ID
// Jika parameter id dikirimkan di URL, nilainya harus valid (integer >= 1).
if (isset($_GET['id']) && (!$id || $id < 1)) {
    http_response_code(400);
    die('ID data tidak valid.');
}

// PROTEKSI METODE HTTP
// Operasi yang memanipulasi data (tulis/ubah/hapus) wajib dikirim via metode POST.
$writeActions = ['store', 'update', 'delete'];

if (
    in_array($action, $writeActions, true) &&
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {
    http_response_code(405);
    die('Metode request tidak diizinkan.');
}

// INSTANSIASI CONTROLLER
// Membuat instance objek controller dinamis berdasarkan rute yang dipilih.
$controllerClass = $routes[$page];
$controller = new $controllerClass();

// Memastikan metode aksi yang diminta benar-benar ada di dalam objek controller terkait.
if (!method_exists($controller, $action)) {
    http_response_code(404);
    die('Fungsi Controller tidak ditemukan.');
}

// EKSEKUSI AKSI CONTROLLER (DISPATCHER)
// Memanggil aksi dengan meneruskan parameter ID untuk aksi yang memerlukannya (edit, update, delete).
if ($action === 'edit' || $action === 'update' || $action === 'delete') {
    if ($id === null) {
        http_response_code(400);
        die('ID data harus disertakan.');
    }

    $controller->$action($id);
} else {
    // Memanggil aksi tanpa argumen untuk aksi umum (index, create, store).
    $controller->$action();
}
