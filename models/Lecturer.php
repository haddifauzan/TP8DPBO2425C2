<?php

require_once __DIR__ . '/Database.php';

// MODEL DOSEN (LECTURER)
// Mengelola data pengajar serta keterhubungannya dengan tabel program studi.
class Lecturer {
    // Properti koneksi database PDO.
    private $db;

    // KONSTRUKTOR MODEL
    // Menginisialisasi koneksi database saat instance model dibuat.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // MENGAMBIL SELURUH DATA DOSEN
    // Menggunakan relasi INNER JOIN dengan tabel study_programs agar nama dan kode prodi
    // dapat langsung ditampilkan pada tabel tanpa query tambahan (mencegah N+1 query problem).
    // Diurutkan secara descending (terbaru di atas) berdasarkan ID.
    public function getAll()
    {
        $query = "SELECT
                    l.*,
                    sp.program_code,
                    sp.program_name
                  FROM lecturers l
                  INNER JOIN study_programs sp
                    ON l.study_program_id = sp.id
                  ORDER BY l.id DESC";

        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    // MENGAMBIL DETAIL DOSEN BERDASARKAN ID
    // Mengambil satu data dosen spesifik beserta informasi program studinya untuk halaman edit/detail.
    public function getById($id)
    {
        $query = "SELECT
                    l.*,
                    sp.program_code,
                    sp.program_name
                  FROM lecturers l
                  INNER JOIN study_programs sp
                    ON l.study_program_id = sp.id
                  WHERE l.id = :id";

        $statement = $this->db->prepare($query);
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    // MENGAMBIL DAFTAR DOSEN UNTUK ELEMEN SELECT DROPDOWN
    // Query ringan yang hanya mengambil kolom id, name, dan nidn untuk efisiensi beban data form mata kuliah.
    // Diurutkan secara descending (terbaru di atas) agar dosen yang baru ditambahkan langsung berada di paling atas pilihan.
    public function getAllForDropdown()
    {
        $query = "SELECT id, name, nidn
                  FROM lecturers
                  ORDER BY id DESC";

        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    // MENYIMPAN DATA DOSEN BARU
    // Menjalankan insert dengan binding parameter data profil dosen dan foreign key program studi.
    public function create($data)
    {
        $query = "INSERT INTO lecturers
                    (name, nidn, phone, join_date, study_program_id)
                  VALUES
                    (:name, :nidn, :phone, :join_date, :study_program_id)";

        $statement = $this->db->prepare($query);

        $statement->execute([
            'name'             => $data['name'],
            'nidn'             => $data['nidn'],
            'phone'            => $data['phone'],
            'join_date'        => $data['join_date'],
            'study_program_id' => $data['study_program_id']
        ]);

        return $this->db->lastInsertId();
    }

    // MEMPERBARUI DATA DOSEN
    // Memperbarui atribut dosen berdasarkan ID kunci utama.
    public function update($id, $data)
    {
        $query = "UPDATE lecturers
                  SET
                    name = :name,
                    nidn = :nidn,
                    phone = :phone,
                    join_date = :join_date,
                    study_program_id = :study_program_id
                  WHERE id = :id";

        $statement = $this->db->prepare($query);

        return $statement->execute([
            'id'               => $id,
            'name'             => $data['name'],
            'nidn'             => $data['nidn'],
            'phone'            => $data['phone'],
            'join_date'        => $data['join_date'],
            'study_program_id' => $data['study_program_id']
        ]);
    }

    // MENGHAPUS DATA DOSEN
    // Menghapus rekaman dosen. Jika masih direferensikan oleh mata kuliah, operasi ini ditolak oleh foreign key database.
    public function delete($id)
    {
        $query = "DELETE FROM lecturers WHERE id = :id";

        $statement = $this->db->prepare($query);
        return $statement->execute(['id' => $id]);
    }
}
