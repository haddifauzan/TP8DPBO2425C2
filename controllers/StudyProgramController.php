<?php

require_once __DIR__ . '/../models/StudyProgram.php';
require_once __DIR__ . '/../views/StudyProgramView.php';

// CONTROLLER PROGRAM STUDI
// Menangani alur logika bisnis, navigasi, validasi, dan komunikasi antara Model dan View untuk Program Studi.
class StudyProgramController {
    // Properti penyimpan instance model StudyProgram.
    private $model;

    // KONSTRUKTOR CONTROLLER
    // Menginisialisasi objek model untuk interaksi basis data.
    public function __construct()
    {
        $this->model = new StudyProgram();
    }

    // HELPER REDIRECT
    // Mengarahkan alur browser kembali ke halaman index program studi dan menghentikan eksekusi script.
    private function redirect()
    {
        header('Location: ' . BASE_URL . '/?page=study_programs');
        exit;
    }

    // HELPER FLASH MESSAGE
    // Menyimpan pesan status (tipe: success/danger/warning/info) ke dalam sesi pengguna
    // agar dapat ditampilkan satu kali pada request berikutnya.
    private function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    // MENAMPILKAN DAFTAR PROGRAM STUDI (INDEX)
    public function index()
    {
        // Mengambil seluruh data program studi dari basis data.
        $studyPrograms = $this->model->getAll();

        // Mengirimkan data dan judul halaman ke layer View untuk dirender ke layar pengguna.
        StudyProgramView::index([
            'studyPrograms' => $studyPrograms,
            'pageTitle'     => 'Data Program Studi'
        ]);
    }

    // MENAMPILKAN FORMULIR TAMBAH PROGRAM STUDI (CREATE)
    public function create()
    {
        // Menyiapkan data kosong untuk form penambahan baru beserta rute target aksi penyimpanan (store).
        StudyProgramView::form([
            'pageTitle'    => 'Tambah Program Studi',
            'studyProgram' => null,
            'formAction'   => BASE_URL . '/?page=study_programs&action=store'
        ]);
    }

    // MEMPROSES PENYIMPANAN DATA PROGRAM STUDI BARU (STORE)
    public function store()
    {
        // SANITASI DAN PENYIAPAN DATA INPUT
        // Membersihkan spasi di awal/akhir dan memaksa format kode/jenjang menjadi huruf kapital.
        $data = [
            'program_code' => strtoupper(trim($_POST['program_code'] ?? '')),
            'program_name' => trim($_POST['program_name'] ?? ''),
            'faculty'      => trim($_POST['faculty'] ?? ''),
            'degree'       => strtoupper(trim($_POST['degree'] ?? ''))
        ];

        // VALIDASI DATA
        $errors = $this->validate($data);

        // Jika terdapat galat validasi, kirimkan kembali notifikasi pesan kesalahan.
        if (!empty($errors)) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect();
        }

        // PENYIMPANAN KE BASIS DATA
        try {
            $this->model->create($data);

            $this->setFlash('success', 'Program studi berhasil ditambahkan.');
        } catch (PDOException $e) {
            // Pencatatan error internal untuk penelusuran bug.
            error_log($e->getMessage());

            // Kode error 23000 menandakan duplikasi pada kolom UNIQUE (program_code).
            $message = $e->getCode() === '23000'
                ? 'Kode program studi sudah digunakan.'
                : 'Terjadi kesalahan saat menyimpan program studi.';

            $this->setFlash('danger', $message);
        }

        $this->redirect();
    }

    // MENAMPILKAN FORMULIR EDIT PROGRAM STUDI
    public function edit($id)
    {
        // Mengambil data prodi eksisting berdasarkan ID.
        $studyProgram = $this->model->getById($id);

        // Validasi keberadaan data.
        if (!$studyProgram) {
            $this->setFlash('danger', 'Program studi tidak ditemukan.');
            $this->redirect();
        }

        // Menampilkan form edit yang telah terisi data lama dan mengarah ke aksi update.
        StudyProgramView::form([
            'pageTitle'    => 'Edit Program Studi',
            'studyProgram' => $studyProgram,
            'formAction'   => BASE_URL . '/?page=study_programs&action=update&id=' . $id
        ]);
    }

    // MEMPROSES PEMBARUAN DATA PROGRAM STUDI (UPDATE)
    public function update($id)
    {
        // Memastikan data yang hendak diubah memang ada di basis data.
        $studyProgram = $this->model->getById($id);

        if (!$studyProgram) {
            $this->setFlash('danger', 'Program studi tidak ditemukan.');
            $this->redirect();
        }

        // Sanitasi dan validasi input (pola sama dengan method store).
        $data = [
            'program_code' => strtoupper(trim($_POST['program_code'] ?? '')),
            'program_name' => trim($_POST['program_name'] ?? ''),
            'faculty'      => trim($_POST['faculty'] ?? ''),
            'degree'       => strtoupper(trim($_POST['degree'] ?? ''))
        ];

        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect();
        }

        // Eksekusi pembaruan ke database dengan penanganan duplikasi kode unik.
        try {
            $this->model->update($id, $data);

            $this->setFlash('success', 'Program studi berhasil diperbarui.');
        } catch (PDOException $e) {
            error_log($e->getMessage());

            $message = $e->getCode() === '23000'
                ? 'Kode program studi sudah digunakan.'
                : 'Terjadi kesalahan saat memperbarui program studi.';

            $this->setFlash('danger', $message);
        }

        $this->redirect();
    }

    // MENGHAPUS DATA PROGRAM STUDI (DELETE)
    public function delete($id)
    {
        try {
            $this->model->delete($id);

            $this->setFlash('success', 'Program studi berhasil dihapus.');
        } catch (PDOException $e) {
            // Jika terdapat foreign key constraint (masih memiliki dosen terhubung), proses hapus digagalkan.
            error_log($e->getMessage());

            $this->setFlash(
                'danger',
                'Program studi tidak dapat dihapus karena masih memiliki dosen.'
            );
        }

        $this->redirect();
    }

    // VALIDASI INPUT FORMULIR PROGRAM STUDI
    // Memeriksa keberadaan nilai wajib, batas maksimal karakter, dan kecocokan jenjang pendidikan.
    private function validate($data)
    {
        $errors = [];

        // Validasi kode program studi (wajib, maks 20 karakter).
        if ($data['program_code'] === '' || strlen($data['program_code']) > 20) {
            $errors[] = 'Kode program studi wajib diisi maksimal 20 karakter.';
        }

        // Validasi nama program studi (wajib, maks 100 karakter).
        if ($data['program_name'] === '' || strlen($data['program_name']) > 100) {
            $errors[] = 'Nama program studi wajib diisi maksimal 100 karakter.';
        }

        // Validasi fakultas (wajib, maks 100 karakter).
        if ($data['faculty'] === '' || strlen($data['faculty']) > 100) {
            $errors[] = 'Nama fakultas wajib diisi maksimal 100 karakter.';
        }

        // Validasi jenjang (harus salah satu dari pilihan yang diizinkan).
        if (!in_array($data['degree'], ['D3', 'S1', 'S2', 'S3'], true)) {
            $errors[] = 'Jenjang pendidikan tidak valid.';
        }

        return $errors;
    }
}
