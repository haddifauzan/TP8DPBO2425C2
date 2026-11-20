<?php

require_once __DIR__ . '/Database.php';

// MODEL PROGRAM STUDI
// Menangani seluruh operasi CRUD pada tabel 'study_programs'.
class StudyProgram {
    // Properti penyimpan koneksi PDO aktif.
    private $db;

    // KONSTRUKTOR MODEL
    // Mendapatkan instance koneksi database saat objek model diinstansiasi.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // MENGAMBIL SELURUH DATA PROGRAM STUDI
    // Menggabungkan tabel study_programs dengan lecturers menggunakan LEFT JOIN
    // untuk menghitung total dosen aktif per program studi secara otomatis.
    // Diurutkan secara descending (terbaru di atas) berdasarkan ID.
    public function getAll()
    {
        $query = "SELECT
                    sp.*,
                    COUNT(l.id) AS lecturer_count
                  FROM study_programs sp
                  LEFT JOIN lecturers l
                    ON sp.id = l.study_program_id
                  GROUP BY sp.id
                  ORDER BY sp.id DESC";

        // Menyiapkan dan mengeksekusi query.
        $statement = $this->db->prepare($query);
        $statement->execute();

        // Mengembalikan seluruh baris data sebagai array dua dimensi.
        return $statement->fetchAll();
    }

    // MENGAMBIL DATA PROGRAM STUDI BERDASARKAN ID
    // Menggunakan prepared statement bertarget parameter :id untuk keamanan dari SQL injection.
    public function getById($id)
    {
        $query = "SELECT *
                  FROM study_programs
                  WHERE id = :id";

        $statement = $this->db->prepare($query);
        $statement->execute(['id' => $id]);

        // Mengembalikan satu baris data atau false jika tidak ditemukan.
        return $statement->fetch();
    }

    // MENYIMPAN DATA PROGRAM STUDI BARU
    // Menerima array asosiatif data dan menyimpannya menggunakan parameter binding.
    public function create($data)
    {
        $query = "INSERT INTO study_programs
                    (program_code, program_name, faculty, degree)
                  VALUES
                    (:program_code, :program_name, :faculty, :degree)";

        $statement = $this->db->prepare($query);

        // Binding nilai input ke parameter placeholder.
        $statement->execute([
            'program_code' => $data['program_code'],
            'program_name' => $data['program_name'],
            'faculty'      => $data['faculty'],
            'degree'       => $data['degree']
        ]);

        // Mengembalikan auto-increment ID baris yang baru ditambahkan.
        return $this->db->lastInsertId();
    }

    // MEMPERBARUI DATA PROGRAM STUDI
    // Mengubah nilai atribut berdasarkan kunci utama ID. Pola binding sama dengan method create.
    public function update($id, $data)
    {
        $query = "UPDATE study_programs
                  SET
                    program_code = :program_code,
                    program_name = :program_name,
                    faculty = :faculty,
                    degree = :degree
                  WHERE id = :id";

        $statement = $this->db->prepare($query);

        return $statement->execute([
            'id'           => $id,
            'program_code' => $data['program_code'],
            'program_name' => $data['program_name'],
            'faculty'      => $data['faculty'],
            'degree'       => $data['degree']
        ]);
    }

    // MENGHAPUS DATA PROGRAM STUDI BERDASARKAN ID
    public function delete($id)
    {
        $query = "DELETE FROM study_programs WHERE id = :id";

        $statement = $this->db->prepare($query);
        return $statement->execute(['id' => $id]);
    }
}
