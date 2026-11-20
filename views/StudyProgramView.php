<?php

require_once __DIR__ . '/Template.php';

// VIEW WRAPPER PROGRAM STUDI
// Menjadi penghubung antara StudyProgramController dengan template visual di direktori templates/study_programs.
class StudyProgramView {
    // Menampilkan halaman tabel daftar seluruh program studi.
    public static function index($data)
    {
        Template::render('study_programs/index', $data);
    }

    // Menampilkan halaman formulir tambah atau edit data program studi.
    public static function form($data)
    {
        Template::render('study_programs/form', $data);
    }
}
