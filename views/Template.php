<?php

require_once __DIR__ . '/../config.php';

// PENGELOLA TEMPLATE DAN TAMPILAN (VIEW ENGINE)
// Bertanggung jawab merender layout utama (header, konten view, footer),
// mengamankan jalur berkas template, dan mengelola token CSRF serta flash message.
class Template {
    // MERENDER TAMPILAN
    // Menerima nama berkas view (misal: 'courses/index') dan data asosiatif yang akan diekstrak ke view.
    public static function render($view, $data = [])
    {
        // EKSTRAKSI DATA KE VARIABEL LOKAL
        // Mengubah kunci array menjadi variabel lokal ($courses, $lecturers, dsb).
        // Parameter EXTR_SKIP mencegah penimpaan variabel internal yang sudah ada di scope fungsi.
        extract($data, EXTR_SKIP);

        // RESOLUSI PATH BERKAS TEMPLATE
        $viewPath = ROOT_PATH . '/templates/' . $view . '.php';

        // PENCEGAHAN SERANGAN PATH TRAVERSAL (KEAMANAN LFI)
        // Memastikan path berkas nyata berada di dalam direktori sah /templates/.
        $realViewPath = realpath($viewPath);
        $templatesPath = realpath(ROOT_PATH . '/templates');

        if (
            $realViewPath === false ||
            $templatesPath === false ||
            strpos($realViewPath, $templatesPath . DIRECTORY_SEPARATOR) !== 0
        ) {
            http_response_code(404);
            die('Halaman tidak ditemukan.');
        }

        // JUDUL HALAMAN DEFAULT
        // Menggunakan nama aplikasi jika controller tidak menyertakan judul khusus.
        $pageTitle = $pageTitle ?? APP_NAME;

        // PEMBUATAN TOKEN CSRF
        // Menghasilkan token acak 32-byte (64 karakter hex) yang aman secara kriptografi untuk proteksi form.
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $csrfToken = $_SESSION['csrf_token'];

        // KONSUMSI PESAN KILAT (FLASH MESSAGE)
        // Mengambil pesan dari session lalu langsung menghapusnya agar hanya tampil satu kali (flash).
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        // PEMUATAN TATA LETAK UTAMA SECARA BERURUTAN
        // 1. Header (doctype, CSS, navigasi bar atas, container pembuka)
        require ROOT_PATH . '/templates/layouts/header.php';
        // 2. Konten spesifik halaman
        require $realViewPath;
        // 3. Footer (container penutup, copyright bawah, script JS)
        require ROOT_PATH . '/templates/layouts/footer.php';
    }
}
