<!DOCTYPE html>
<html lang="id">
<head>
    <!-- PENGATURAN META DAN RESPONSIVITAS DOKUMEN -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- JUDUL HALAMAN DINAMIS -->
    <!-- Mengamankan output judul dari serangan XSS menggunakan htmlspecialchars -->
    <title>
        <?= htmlspecialchars($pageTitle ?? APP_NAME) ?> | <?= APP_NAME ?>
    </title>

    <!-- PEMUATAN ASSET CSS BOOTSTRAP LOKAL -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">

    <!-- STYLING KHUSUS UNTUK MENINGKATKAN KONTORAS DAN KETERBACAAN MENU HEADER -->
    <style>
        .navbar-custom {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .navbar-custom .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff !important;
            letter-spacing: 0.3px;
        }
        .navbar-custom .navbar-nav {
            gap: 0.5rem;
        }
        .navbar-custom .nav-link {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 1.05rem;
            padding: 0.5rem 1.15rem !important;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
            opacity: 0.92;
        }
        .navbar-custom .nav-link:hover {
            opacity: 1;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff !important;
        }
        .navbar-custom .nav-link.active {
            opacity: 1;
            background-color: #ffffff !important;
            color: #0d6efd !important;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<!-- STRUKTUR BODY FLEXBOX UNTUK STICKY FOOTER -->
<!-- d-flex flex-column min-vh-100 memastikan tinggi layar minimal 100vh agar footer tetap di bawah -->
<body class="d-flex flex-column min-vh-100">

<?php
// MENDETEKSI HALAMAN AKTIF UNTUK PENANDA MENU (ACTIVE STATE)
$activePage = $_GET['page'] ?? 'lecturers';
?>

<!-- NAVIGASI UTAMA (NAVBAR) DENGAN KELAS KHUSUS KETERBACAAN TINGGI -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary navbar-custom py-2">
    <div class="container">

        <!-- LOGO / NAMA APLIKASI TEBAL DAN JELAS -->
        <a class="navbar-brand" href="<?= BASE_URL ?>/">
            Lecturer Management FPMIPA UPI
        </a>

        <!-- TOMBOL HAMBURGER UNTUK TAMPILAN MOBILE -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENU TAUTAN HALAMAN DENGAN TEKS KONTRAS DAN STATUS AKTIF JELAS -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a
                        class="nav-link <?= $activePage === 'study_programs' ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>/?page=study_programs"
                    >
                        Study Programs
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link <?= $activePage === 'lecturers' ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>/?page=lecturers"
                    >
                        Lecturers
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link <?= $activePage === 'courses' ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>/?page=courses"
                    >
                        Courses
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>

<!-- CONTAINER UTAMA KONTEN -->
<!-- flex-grow-1 memperluas area konten utama untuk mendorong footer ke batas terbawah viewport -->
<main class="container py-4 flex-grow-1">

    <!-- KOMPONEN NOTIFIKASI KILAT (FLASH MESSAGE) -->
    <!-- Menampilkan pesan sukses atau galat dari aksi controller sebelumnya jika tersedia -->
    <?php if (!empty($flash)): ?>
        <div
            class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show"
            role="alert"
        >
            <?= htmlspecialchars($flash['message']) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    <?php endif; ?>
