<?php

require_once __DIR__ . '/../models/Lecturer.php';
require_once __DIR__ . '/../models/StudyProgram.php';
require_once __DIR__ . '/../views/LecturerView.php';

// CONTROLLER DOSEN (LECTURER CONTROLLER)
// Mengelola data pengajar, validasi input identitas akademik, dan interaksi relasional ke Program Studi.
class LecturerController {
    // Properti model dosen dan model program studi untuk kebutuhan dropdown relasi.
    private $model;
    private $studyProgramModel;

    // KONSTRUKTOR CONTROLLER
    // Menginisialisasi kedua model saat objek controller dibuat.
    public function __construct()
    {
        $this->model = new Lecturer();
        $this->studyProgramModel = new StudyProgram();
    }

    // HELPER REDIRECT
    // Mengarahkan alur navigasi browser ke daftar dosen.
    private function redirect()
    {
        header('Location: ' . BASE_URL . '/?page=lecturers');
        exit;
    }

    // HELPER FLASH MESSAGE
    // Menyimpan pesan feedback sementara pada session untuk ditampilkan ke pengguna.
    private function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    // MENAMPILKAN DAFTAR DOSEN (INDEX)
    public function index()
    {
        // Mengambil seluruh rekaman dosen lengkap beserta nama dan kode program studi.
        $lecturers = $this->model->getAll();

        LecturerView::index([
            'lecturers' => $lecturers,
            'pageTitle' => 'Data Dosen'
        ]);
    }

    // MENAMPILKAN FORMULIR TAMBAH DOSEN (CREATE)
    public function create()
    {
        // Mengambil seluruh data program studi untuk mengisi pilihan opsi dropdown prodi.
        $studyPrograms = $this->studyProgramModel->getAll();

        LecturerView::form([
            'pageTitle'     => 'Tambah Dosen',
            'lecturer'      => null,
            'studyPrograms' => $studyPrograms,
            'formAction'    => BASE_URL . '/?page=lecturers&action=store'
        ]);
    }

    // MEMPROSES PENYIMPANAN DOSEN BARU (STORE)
    public function store()
    {
        // SANITASI DATA INPUT
        // Membersihkan string input dan memastikan ID prodi dikonversi ke tipe data integer.
        $data = [
            'name'             => trim($_POST['name'] ?? ''),
            'nidn'             => trim($_POST['nidn'] ?? ''),
            'phone'            => trim($_POST['phone'] ?? ''),
            'join_date'        => trim($_POST['join_date'] ?? ''),
            'study_program_id' => filter_var(
                $_POST['study_program_id'] ?? '',
                FILTER_VALIDATE_INT
            )
        ];

        // Validasi kelayakan data input.
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect();
        }

        // PENYIMPANAN KE BASIS DATA
        try {
            $this->model->create($data);

            $this->setFlash('success', 'Data dosen berhasil ditambahkan.');
        } catch (PDOException $e) {
            error_log($e->getMessage());

            // Kode 23000 menandakan duplikasi NIDN (kunci unik) atau foreign key prodi tidak valid.
            $message = $e->getCode() === '23000'
                ? 'NIDN sudah digunakan atau program studi tidak valid.'
                : 'Terjadi kesalahan saat menyimpan data dosen.';

            $this->setFlash('danger', $message);
        }

        $this->redirect();
    }

    // MENAMPILKAN FORMULIR EDIT DOSEN
    public function edit($id)
    {
        // Mencari dosen berdasarkan ID yang diminta.
        $lecturer = $this->model->getById($id);

        if (!$lecturer) {
            $this->setFlash('danger', 'Data dosen tidak ditemukan.');
            $this->redirect();
        }

        // Mengambil daftar program studi untuk pilihan dropdown pada form edit.
        $studyPrograms = $this->studyProgramModel->getAll();

        LecturerView::form([
            'pageTitle'     => 'Edit Dosen',
            'lecturer'      => $lecturer,
            'studyPrograms' => $studyPrograms,
            'formAction'    => BASE_URL . '/?page=lecturers&action=update&id=' . $id
        ]);
    }

    // MEMPROSES PEMBARUAN DATA DOSEN (UPDATE)
    public function update($id)
    {
        // Memastikan record dosen ada sebelum diperbarui.
        $lecturer = $this->model->getById($id);

        if (!$lecturer) {
            $this->setFlash('danger', 'Data dosen tidak ditemukan.');
            $this->redirect();
        }

        // Sanitasi dan validasi data (mengikuti pola yang sama dengan store).
        $data = [
            'name'             => trim($_POST['name'] ?? ''),
            'nidn'             => trim($_POST['nidn'] ?? ''),
            'phone'            => trim($_POST['phone'] ?? ''),
            'join_date'        => trim($_POST['join_date'] ?? ''),
            'study_program_id' => filter_var(
                $_POST['study_program_id'] ?? '',
                FILTER_VALIDATE_INT
            )
        ];

        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect();
        }

        try {
            $this->model->update($id, $data);

            $this->setFlash('success', 'Data dosen berhasil diperbarui.');
        } catch (PDOException $e) {
            error_log($e->getMessage());

            $message = $e->getCode() === '23000'
                ? 'NIDN sudah digunakan atau program studi tidak valid.'
                : 'Terjadi kesalahan saat memperbarui data dosen.';

            $this->setFlash('danger', $message);
        }

        $this->redirect();
    }

    // MENGHAPUS DATA DOSEN (DELETE)
    public function delete($id)
    {
        try {
            $this->model->delete($id);

            $this->setFlash('success', 'Data dosen berhasil dihapus.');
        } catch (PDOException $e) {
            // Mencegah penghapusan jika dosen masih terikat sebagai pengampu mata kuliah.
            error_log($e->getMessage());

            $this->setFlash(
                'danger',
                'Data dosen tidak dapat dihapus karena masih memiliki mata kuliah.'
            );
        }

        $this->redirect();
    }

    // VALIDASI INPUT FORMULIR DOSEN
    // Memeriksa keabsahan format nama, NIDN, nomor telepon, tanggal bergabung, dan keberadaan prodi.
    private function validate($data)
    {
        $errors = [];

        // Validasi nama dosen.
        if ($data['name'] === '' || strlen($data['name']) > 100) {
            $errors[] = 'Nama dosen wajib diisi maksimal 100 karakter.';
        }

        // Validasi nomor induk dosen nasional (NIDN).
        if ($data['nidn'] === '' || strlen($data['nidn']) > 30) {
            $errors[] = 'NIDN wajib diisi maksimal 30 karakter.';
        }

        // Validasi nomor telepon dan format karakter yang diizinkan (angka, tanda plus, spasi, kurung).
        if ($data['phone'] === '' || strlen($data['phone']) > 20) {
            $errors[] = 'Nomor telepon wajib diisi maksimal 20 karakter.';
        } elseif (!preg_match('/^[0-9+\-\s()]+$/', $data['phone'])) {
            $errors[] = 'Format nomor telepon tidak valid.';
        }

        // Validasi format tanggal YYYY-MM-DD menggunakan kelas DateTime bawaan PHP.
        $date = DateTime::createFromFormat('!Y-m-d', $data['join_date']);
        if (!$date || $date->format('Y-m-d') !== $data['join_date']) {
            $errors[] = 'Tanggal bergabung tidak valid.';
        }

        // Verifikasi keabsahan program studi pilihan: harus berupa integer dan ada di basis data.
        if (!$data['study_program_id'] || $data['study_program_id'] < 1) {
            $errors[] = 'Program studi wajib dipilih.';
        } elseif (!$this->studyProgramModel->getById($data['study_program_id'])) {
            $errors[] = 'Program studi yang dipilih tidak ditemukan.';
        }

        return $errors;
    }
}
