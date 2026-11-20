<?php

// PENENTUAN MODE FORMULIR
// Memeriksa apakah form dibuka untuk edit data dosen atau penambahan baru.
$isEdit = !empty($lecturer);
?>

<!-- HEADER FORMULIR -->
<div class="mb-4">
    <h3><?= $isEdit ? 'Edit Data Dosen' : 'Tambah Dosen' ?></h3>
    <p class="text-muted mb-0">
        <?= $isEdit ? 'Perbarui informasi dosen.' : 'Masukkan informasi dosen baru.' ?>
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

            <!-- INPUT NAMA LENGKAP DAN GELAR DOSEN -->
            <div class="mb-3">
                <label class="form-label">Nama Dosen</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    maxlength="100"
                    placeholder="Contoh: Dr. Rosa Ariani Sukamto, M.T."
                    value="<?= htmlspecialchars($lecturer['name'] ?? '') ?>"
                    required
                >
            </div>

            <!-- INPUT NOMOR INDUK DOSEN NASIONAL (NIDN) -->
            <div class="mb-3">
                <label class="form-label">NIDN</label>
                <input
                    type="text"
                    name="nidn"
                    class="form-control"
                    maxlength="30"
                    placeholder="Contoh: 0013018201"
                    value="<?= htmlspecialchars($lecturer['nidn'] ?? '') ?>"
                    required
                >
            </div>

            <!-- INPUT NOMOR TELEPON -->
            <div class="mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    maxlength="20"
                    placeholder="Contoh: 081234567890"
                    value="<?= htmlspecialchars($lecturer['phone'] ?? '') ?>"
                    required
                >
            </div>

            <!-- INPUT TANGGAL BERGABUNG DI PERGURUAN TINGGI -->
            <div class="mb-3">
                <label class="form-label">Tanggal Bergabung</label>
                <input
                    type="date"
                    name="join_date"
                    class="form-control"
                    value="<?= htmlspecialchars($lecturer['join_date'] ?? '') ?>"
                    required
                >
            </div>

            <!-- PILIHAN PROGRAM STUDI (FOREIGN KEY) -->
            <div class="mb-3">
                <label class="form-label">Program Studi</label>
                <select name="study_program_id" class="form-select" required>
                    <option value="">Pilih Program Studi</option>

                    <!-- Looping data prodi untuk pilihan dropdown dengan seleksi otomatis pada mode edit -->
                    <?php foreach ($studyPrograms as $studyProgram): ?>
                        <option
                            value="<?= (int) $studyProgram['id'] ?>"
                            <?= (string) ($lecturer['study_program_id'] ?? '') === (string) $studyProgram['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($studyProgram['program_name']) ?>
                            (<?= htmlspecialchars($studyProgram['program_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- TOMBOL AKSI FORMULIR -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Dosen' ?>
                </button>

                <a href="<?= BASE_URL ?>/?page=lecturers"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>
