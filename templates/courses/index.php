<!-- HEADER HALAMAN DAN NAVIGASI TAMBAH MATA KULIAH -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Data Mata Kuliah</h3>
        <p class="text-muted mb-0">Kelola mata kuliah dan dosen pengampu.</p>
    </div>

    <!-- Tombol navigasi menuju halaman formulir tambah mata kuliah -->
    <a href="<?= BASE_URL ?>/?page=courses&action=create"
       class="btn btn-primary">
        Tambah Mata Kuliah
    </a>
</div>

<!-- KARTU TABEL DATA MATA KULIAH -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Dosen Pengampu</th>
                        <th>Program Studi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Memeriksa ketersediaan koleksi data mata kuliah -->
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $index => $course): ?>
                            <tr>
                                <!-- Penomoran baris -->
                                <td><?= $index + 1 ?></td>

                                <!-- Informasi dasar kurikulum mata kuliah -->
                                <td><?= htmlspecialchars($course['course_code']) ?></td>
                                <td><?= htmlspecialchars($course['course_name']) ?></td>
                                <td><?= (int) $course['credits'] ?></td>
                                <td><?= htmlspecialchars($course['semester']) ?></td>
                                <td><?= htmlspecialchars($course['academic_year']) ?></td>

                                <!-- Data dosen pengampu hasil relasi INNER JOIN -->
                                <td>
                                    <?= htmlspecialchars($course['lecturer_name']) ?>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($course['nidn']) ?>
                                    </small>
                                </td>

                                <!-- Data program studi asal dosen pengampu -->
                                <td><?= htmlspecialchars($course['program_name']) ?></td>

                                <!-- KOLOM TOMBOL AKSI -->
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Tombol edit mata kuliah -->
                                        <a
                                            href="<?= BASE_URL ?>/?page=courses&action=edit&id=<?= (int) $course['id'] ?>"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Edit
                                        </a>

                                        <!-- Formulir hapus mata kuliah dengan proteksi POST dan token CSRF -->
                                        <form
                                            action="<?= BASE_URL ?>/?page=courses&action=delete&id=<?= (int) $course['id'] ?>"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?')"
                                        >
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars($csrfToken) ?>"
                                            >

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- TAMPILAN JIKA DATA KOSONG -->
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                Belum ada data mata kuliah.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
