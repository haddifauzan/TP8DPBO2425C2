<?php

require_once __DIR__ . '/Template.php';

// VIEW WRAPPER DOSEN
// Menjadi penghubung antara LecturerController dengan template visual di direktori templates/lecturers.
class LecturerView {
    // Menampilkan halaman tabel daftar seluruh dosen.
    public static function index($data)
    {
        Template::render('lecturers/index', $data);
    }

    // Menampilkan halaman formulir tambah atau edit data dosen.
    public static function form($data)
    {
        Template::render('lecturers/form', $data);
    }
}
