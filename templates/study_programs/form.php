<?php

// PENENTUAN MODE FORMULIR
// Menentukan apakah formulir berada dalam mode 'Edit' (jika variabel data terisi) atau 'Tambah' (jika null).
$isEdit = !empty($studyProgram);
?>

<!-- HEADER FORMULIR -->
<div class="mb-4">
    <!-- Judul dan deskripsi menyesuaikan mode formulir secara dinamis -->
    <h3><?= $isEdit ? 'Edit Program Studi' : 'Tambah Program Studi' ?></h3>
    <p class="text-muted mb-0">
        <?= $isEdit ? 'Perbarui informasi program studi.' : 'Masukkan informasi program studi baru.' ?>
    </p>
</div>

<!-- KARTU CONTAINER FORMULIR -->
<div class="card">
    <div class="card-body">
        <!-- Form mengirim data ke URL formAction yang ditentukan controller via metode POST -->
        <form action="<?= htmlspecialchars($formAction) ?>" method="POST">

            <!-- TOKEN KEAMANAN CSRF -->
            <!-- Mencegah serangan pemalsuan permintaan lintas situs (Cross-Site Request Forgery) -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken) ?>"
            >

            <!-- INPUT KODE PROGRAM STUDI -->
            <div class="mb-3">
                <label class="form-label">Kode Program Studi</label>
                <input
                    type="text"
                    name="program_code"
                    class="form-control"
                    maxlength="20"
                    placeholder="Contoh: D0151"
                    value="<?= htmlspecialchars($studyProgram['program_code'] ?? '') ?>"
                    required
                >
            </div>

            <!-- INPUT NAMA PROGRAM STUDI -->
            <div class="mb-3">
                <label class="form-label">Nama Program Studi</label>
                <input
                    type="text"
                    name="program_name"
                    class="form-control"
                    maxlength="100"
                    placeholder="Contoh: Pendidikan Matematika"
                    value="<?= htmlspecialchars($studyProgram['program_name'] ?? '') ?>"
                    required
                >
            </div>

            <!-- INPUT NAMA FAKULTAS -->
            <div class="mb-3">
                <label class="form-label">Fakultas</label>
                <input
                    type="text"
                    name="faculty"
                    class="form-control"
                    maxlength="100"
                    value="<?= htmlspecialchars($studyProgram['faculty'] ?? 'FPMIPA') ?>"
                    required
                >
            </div>

            <!-- INPUT PILIHAN JENJANG PENDIDIKAN (DROPDOWN) -->
            <div class="mb-3">
                <label class="form-label">Jenjang Pendidikan</label>
                <select name="degree" class="form-select" required>
                    <option value="">Pilih Jenjang</option>

                    <!-- Perulangan opsi jenjang dengan seleksi otomatis untuk data yang sedang diedit -->
                    <?php foreach (['D3', 'S1', 'S2', 'S3'] as $degree): ?>
                        <option
                            value="<?= $degree ?>"
                            <?= ($studyProgram['degree'] ?? 'S1') === $degree ? 'selected' : '' ?>
                        >
                            <?= $degree ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- TOMBOL AKSI SIMPAN DAN KEMBALI -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Program Studi' ?>
                </button>

                <a href="<?= BASE_URL ?>/?page=study_programs"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>
