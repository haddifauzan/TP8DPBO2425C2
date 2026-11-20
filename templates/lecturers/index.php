<!-- HEADER HALAMAN DAN NAVIGASI TAMBAH DOSEN -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Data Dosen</h3>
        <p class="text-muted mb-0">Kelola informasi dosen dan program studi.</p>
    </div>

    <!-- Tombol navigasi menuju halaman formulir tambah dosen baru -->
    <a href="<?= BASE_URL ?>/?page=lecturers&action=create"
       class="btn btn-primary">
        Tambah Dosen
    </a>
</div>

<!-- KARTU TABEL DATA DOSEN -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Dosen</th>
                        <th>NIDN</th>
                        <th>Nomor Telepon</th>
                        <th>Tanggal Bergabung</th>
                        <th>Program Studi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Memeriksa ketersediaan koleksi data dosen -->
                    <?php if (!empty($lecturers)): ?>
                        <?php foreach ($lecturers as $index => $lecturer): ?>
                            <tr>
                                <!-- Penomoran baris -->
                                <td><?= $index + 1 ?></td>

                                <!-- Informasi profil akademik dosen -->
                                <td><?= htmlspecialchars($lecturer['name']) ?></td>
                                <td><?= htmlspecialchars($lecturer['nidn']) ?></td>
                                <td><?= htmlspecialchars($lecturer['phone']) ?></td>
                                <td><?= htmlspecialchars($lecturer['join_date']) ?></td>

                                <!-- Informasi program studi asal dari hasil INNER JOIN -->
                                <td>
                                    <?= htmlspecialchars($lecturer['program_name']) ?>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($lecturer['program_code']) ?>
                                    </small>
                                </td>

                                <!-- KOLOM TOMBOL AKSI -->
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Tombol edit data dosen -->
                                        <a
                                            href="<?= BASE_URL ?>/?page=lecturers&action=edit&id=<?= (int) $lecturer['id'] ?>"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Edit
                                        </a>

                                        <!-- Formulir hapus dosen dengan proteksi POST dan token CSRF -->
                                        <form
                                            action="<?= BASE_URL ?>/?page=lecturers&action=delete&id=<?= (int) $lecturer['id'] ?>"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data dosen ini?')"
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
                            <td colspan="7" class="text-center text-muted">
                                Belum ada data dosen.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
