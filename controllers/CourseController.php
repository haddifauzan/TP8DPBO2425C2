<?php

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Lecturer.php';
require_once __DIR__ . '/../views/CourseView.php';

// CONTROLLER MATA KULIAH (COURSE CONTROLLER)
// Menangani alur logika bisnis, navigasi, validasi data kurikulum, dan penetapan dosen pengampu mata kuliah.
class CourseController {
    // Properti model mata kuliah dan model dosen untuk relasi pengampu.
    private $model;
    private $lecturerModel;

    // KONSTRUKTOR CONTROLLER
    // Menginisialisasi model mata kuliah dan dosen.
    public function __construct()
    {
        $this->model = new Course();
        $this->lecturerModel = new Lecturer();
    }

    // HELPER REDIRECT
    // Mengarahkan alur navigasi browser ke daftar mata kuliah.
    private function redirect()
    {
        header('Location: ' . BASE_URL . '/?page=courses');
        exit;
    }

    // HELPER FLASH MESSAGE
    // Menyimpan notifikasi sementara pada session untuk ditampilkan satu kali ke pengguna.
    private function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    // MENAMPILKAN DAFTAR MATA KULIAH (INDEX)
    public function index()
    {
        // Mengambil seluruh data mata kuliah beserta detail dosen pengampu dan nama program studi.
        $courses = $this->model->getAll();

        CourseView::index([
            'courses'   => $courses,
            'pageTitle' => 'Data Mata Kuliah'
        ]);
    }

    // MENAMPILKAN FORMULIR TAMBAH MATA KULIAH (CREATE)
    public function create()
    {
        // Mengambil daftar ringkas dosen (id, name, nidn) untuk mengisi elemen select option pengampu.
        $lecturers = $this->lecturerModel->getAllForDropdown();

        CourseView::form([
            'pageTitle'  => 'Tambah Mata Kuliah',
            'course'     => null,
            'lecturers'  => $lecturers,
            'formAction' => BASE_URL . '/?page=courses&action=store'
        ]);
    }

    // MEMPROSES PENYIMPANAN MATA KULIAH BARU (STORE)
    public function store()
    {
        // SANITASI DATA INPUT
        // Membersihkan spasi, memvalidasi tipe numerik pada ID dosen & SKS, serta kapitalisasi kode MK.
        $data = [
            'lecturer_id'   => filter_var($_POST['lecturer_id'] ?? '', FILTER_VALIDATE_INT),
            'course_code'   => strtoupper(trim($_POST['course_code'] ?? '')),
            'course_name'   => trim($_POST['course_name'] ?? ''),
            'credits'       => filter_var($_POST['credits'] ?? '', FILTER_VALIDATE_INT),
            'semester'      => trim($_POST['semester'] ?? ''),
            'academic_year' => trim($_POST['academic_year'] ?? '')
        ];

        // Validasi kelayakan input data mata kuliah.
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect();
        }

        // PENYIMPANAN KE BASIS DATA
        try {
            $this->model->create($data);

            $this->setFlash('success', 'Mata kuliah berhasil ditambahkan.');
        } catch (PDOException $e) {
            error_log($e->getMessage());

            // Kode 23000 menandakan duplikasi kode mata kuliah (UNIQUE) atau ID dosen tidak valid.
            $message = $e->getCode() === '23000'
                ? 'Kode mata kuliah sudah digunakan atau dosen tidak valid.'
                : 'Terjadi kesalahan saat menyimpan mata kuliah.';

            $this->setFlash('danger', $message);
        }

        $this->redirect();
    }

    // MENAMPILKAN FORMULIR EDIT MATA KULIAH
    public function edit($id)
    {
        // Mencari data mata kuliah berdasarkan ID target.
        $course = $this->model->getById($id);

        if (!$course) {
            $this->setFlash('danger', 'Mata kuliah tidak ditemukan.');
            $this->redirect();
        }

        // Mengambil daftar dosen untuk pilihan dropdown pada form edit.
        $lecturers = $this->lecturerModel->getAllForDropdown();

        CourseView::form([
            'pageTitle'  => 'Edit Mata Kuliah',
            'course'     => $course,
            'lecturers'  => $lecturers,
            'formAction' => BASE_URL . '/?page=courses&action=update&id=' . $id
        ]);
    }

    // MEMPROSES PEMBARUAN DATA MATA KULIAH (UPDATE)
    public function update($id)
    {
        // Memastikan record mata kuliah ada sebelum diubah.
        $course = $this->model->getById($id);

        if (!$course) {
            $this->setFlash('danger', 'Mata kuliah tidak ditemukan.');
            $this->redirect();
        }

        // Sanitasi dan validasi data input (mengikuti pola yang sama dengan store).
        $data = [
            'lecturer_id'   => filter_var($_POST['lecturer_id'] ?? '', FILTER_VALIDATE_INT),
            'course_code'   => strtoupper(trim($_POST['course_code'] ?? '')),
            'course_name'   => trim($_POST['course_name'] ?? ''),
            'credits'       => filter_var($_POST['credits'] ?? '', FILTER_VALIDATE_INT),
            'semester'      => trim($_POST['semester'] ?? ''),
            'academic_year' => trim($_POST['academic_year'] ?? '')
        ];

        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect();
        }

        try {
            $this->model->update($id, $data);

            $this->setFlash('success', 'Mata kuliah berhasil diperbarui.');
        } catch (PDOException $e) {
            error_log($e->getMessage());

            $message = $e->getCode() === '23000'
                ? 'Kode mata kuliah sudah digunakan atau dosen tidak valid.'
                : 'Terjadi kesalahan saat memperbarui mata kuliah.';

            $this->setFlash('danger', $message);
        }

        $this->redirect();
    }

    // MENGHAPUS DATA MATA KULIAH (DELETE)
    public function delete($id)
    {
        try {
            $this->model->delete($id);

            $this->setFlash('success', 'Mata kuliah berhasil dihapus.');
        } catch (PDOException $e) {
            error_log($e->getMessage());

            $this->setFlash('danger', 'Gagal menghapus mata kuliah.');
        }

        $this->redirect();
    }

    // VALIDASI INPUT FORMULIR MATA KULIAH
    // Memeriksa keabsahan dosen pengampu, kode unik, nama mata kuliah, rentang SKS, semester, dan format tahun akademik.
    private function validate($data)
    {
        $errors = [];

        // Verifikasi dosen pengampu: wajib dipilih dan harus ada di tabel lecturers.
        if (!$data['lecturer_id'] || $data['lecturer_id'] < 1) {
            $errors[] = 'Dosen pengampu wajib dipilih.';
        } elseif (!$this->lecturerModel->getById($data['lecturer_id'])) {
            $errors[] = 'Dosen yang dipilih tidak ditemukan.';
        }

        // Validasi kode mata kuliah (wajib diisi, maks 20 karakter).
        if ($data['course_code'] === '' || strlen($data['course_code']) > 20) {
            $errors[] = 'Kode mata kuliah wajib diisi maksimal 20 karakter.';
        }

        // Validasi nama mata kuliah (wajib diisi, maks 100 karakter).
        if ($data['course_name'] === '' || strlen($data['course_name']) > 100) {
            $errors[] = 'Nama mata kuliah wajib diisi maksimal 100 karakter.';
        }

        // Validasi batas beban SKS (harus berupa integer 1 hingga 6).
        if ($data['credits'] === false || $data['credits'] < 1 || $data['credits'] > 6) {
            $errors[] = 'Jumlah SKS harus berupa angka antara 1 sampai 6.';
        }

        // Validasi pilihan semester (hanya menerima opsi Ganjil atau Genap).
        if (!in_array($data['semester'], ['Ganjil', 'Genap'], true)) {
            $errors[] = 'Semester harus Ganjil atau Genap.';
        }

        // Validasi format tahun akademik (pola YYYY/YYYY dan tahun kedua harus selisih tepat 1 tahun dari tahun pertama).
        if (!preg_match('/^\d{4}\/\d{4}$/', $data['academic_year'])) {
            $errors[] = 'Tahun akademik harus menggunakan format YYYY/YYYY (contoh: 2025/2026).';
        } elseif (
            (int) substr($data['academic_year'], 5, 4) !==
            (int) substr($data['academic_year'], 0, 4) + 1
        ) {
            $errors[] = 'Rentang tahun akademik harus berurutan (contoh: 2025/2026).';
        }

        return $errors;
    }
}
