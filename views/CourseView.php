<?php

require_once __DIR__ . '/Template.php';

// VIEW WRAPPER MATA KULIAH
// Menjadi penghubung antara CourseController dengan template visual di direktori templates/courses.
class CourseView {
    // Menampilkan halaman tabel daftar seluruh mata kuliah.
    public static function index($data)
    {
        Template::render('courses/index', $data);
    }

    // Menampilkan halaman formulir tambah atau edit data mata kuliah.
    public static function form($data)
    {
        Template::render('courses/form', $data);
    }
}
