<?php

// PENENTUAN MODE FORMULIR
// Memeriksa apakah form dibuka untuk edit mata kuliah atau penambahan baru.
$isEdit = !empty($course);
?>

<!-- HEADER FORMULIR -->
<div class="mb-4">
    <h3><?= $isEdit ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' ?></h3>
    <p class="text-muted mb-0">
        <?= $isEdit ? 'Perbarui informasi mata kuliah.' : 'Masukkan informasi mata kuliah baru.' ?>
    </p>
</div>

<!-- KARTU CONTAINER FORMULIR -->
<div class="card">
    <div class="card-body">
        <form action="<?= htmlspecialchars($formAction) ?>" method="POST">

            <!-- TOKEN KEAMANAN CSRF -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken) ?>"
            >

            <!-- INPUT KODE MATA KULIAH -->
            <div class="mb-3">
                <label class="form-label">Kode Mata Kuliah</label>
                <input
                    type="text"
                    name="course_code"
                    class="form-control"
                    maxlength="20"
                    placeholder="Contoh: IK101"
                    value="<?= htmlspecialchars($course['course_code'] ?? '') ?>"
                    required
                >
            </div>

            <!-- INPUT NAMA MATA KULIAH -->
            <div class="mb-3">
                <label class="form-label">Nama Mata Kuliah</label>
                <input
                    type="text"
                    name="course_name"
                    class="form-control"
                    maxlength="100"
                    placeholder="Contoh: Algoritma dan Pemrograman"
                    value="<?= htmlspecialchars($course['course_name'] ?? '') ?>"
                    required
                >
            </div>

            <!-- INPUT BOBOT SKS (BATAS 1 SAMPAI 6) -->
            <div class="mb-3">
                <label class="form-label">Jumlah SKS</label>
                <input
                    type="number"
                    name="credits"
                    class="form-control"
                    min="1"
                    max="6"
                    placeholder="Rentang 1 - 6"
                    value="<?= htmlspecialchars((string) ($course['credits'] ?? '')) ?>"
                    required
                >
            </div>

            <!-- PILIHAN SEMESTER -->
            <div class="mb-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select" required>
                    <option value="">Pilih Semester</option>

                    <option
                        value="Ganjil"
                        <?= ($course['semester'] ?? '') === 'Ganjil' ? 'selected' : '' ?>
                    >
                        Ganjil
                    </option>

                    <option
                        value="Genap"
                        <?= ($course['semester'] ?? '') === 'Genap' ? 'selected' : '' ?>
                    >
                        Genap
                    </option>
                </select>
            </div>

            <!-- INPUT TAHUN AKADEMIK (FORMAT YYYY/YYYY) -->
            <div class="mb-3">
                <label class="form-label">Tahun Akademik</label>
                <input
                    type="text"
                    name="academic_year"
                    class="form-control"
                    placeholder="Contoh: 2025/2026"
                    pattern="[0-9]{4}/[0-9]{4}"
                    value="<?= htmlspecialchars($course['academic_year'] ?? '') ?>"
                    required
                >
            </div>

            <!-- PILIHAN DOSEN PENGAMPU (FOREIGN KEY) -->
            <div class="mb-3">
                <label class="form-label">Dosen Pengampu</label>
                <select name="lecturer_id" class="form-select" required>
                    <option value="">Pilih Dosen</option>

                    <!-- Looping data dosen untuk opsi dropdown dengan nama dan NIDN pengampu -->
                    <?php foreach ($lecturers as $lecturer): ?>
                        <option
                            value="<?= (int) $lecturer['id'] ?>"
                            <?= (string) ($course['lecturer_id'] ?? '') === (string) $lecturer['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($lecturer['name']) ?>
                            (<?= htmlspecialchars($lecturer['nidn']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- TOMBOL AKSI FORMULIR -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Mata Kuliah' ?>
                </button>

                <a href="<?= BASE_URL ?>/?page=courses"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>
