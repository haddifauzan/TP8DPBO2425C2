<!-- HEADER HALAMAN DAN TOMBOL AKSI UTAMA -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Data Program Studi</h3>
        <p class="text-muted mb-0">Kelola data program studi FPMIPA.</p>
    </div>

    <!-- Tombol navigasi menuju halaman formulir tambah data baru -->
    <a href="<?= BASE_URL ?>/?page=study_programs&action=create"
       class="btn btn-primary">
        Tambah Program Studi
    </a>
</div>

<!-- KARTU TABEL DATA PROGRAM STUDI -->
<div class="card">
    <div class="card-body">
        <!-- table-responsive memastikan tabel dapat digeser mendatar pada layar kecil/mobile -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Program Studi</th>
                        <th>Fakultas</th>
                        <th>Jenjang</th>
                        <th>Jumlah Dosen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Mengecek apakah terdapat data program studi yang dikirim controller -->
                    <?php if (!empty($studyPrograms)): ?>
                        <?php foreach ($studyPrograms as $index => $studyProgram): ?>
                            <tr>
                                <!-- Penomoran urut otomatis berbasis indeks array -->
                                <td><?= $index + 1 ?></td>

                                <!-- Sanitasi output teks dengan htmlspecialchars untuk menangkal XSS -->
                                <td><?= htmlspecialchars($studyProgram['program_code']) ?></td>
                                <td><?= htmlspecialchars($studyProgram['program_name']) ?></td>
                                <td><?= htmlspecialchars($studyProgram['faculty']) ?></td>
                                <td><?= htmlspecialchars($studyProgram['degree']) ?></td>

                                <!-- Jumlah dosen hasil agregasi COUNT dari model -->
                                <td><?= (int) $studyProgram['lecturer_count'] ?></td>

                                <!-- KOLOM AKSI (EDIT & HAPUS) -->
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Tombol edit mengarahkan ke form dengan ID target via query string -->
                                        <a
                                            href="<?= BASE_URL ?>/?page=study_programs&action=edit&id=<?= (int) $studyProgram['id'] ?>"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Edit
                                        </a>

                                        <!-- Form hapus menggunakan metode POST untuk melindungi dari modifikasi data via GET -->
                                        <form
                                            action="<?= BASE_URL ?>/?page=study_programs&action=delete&id=<?= (int) $studyProgram['id'] ?>"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus program studi ini?')"
                                        >
                                            <!-- Token keamanan CSRF tersembunyi -->
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
                        <!-- TAMPILAN JIKA BELUM ADA DATA (EMPTY STATE) -->
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Belum ada data program studi.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
