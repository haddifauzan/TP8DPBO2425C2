<?php

require_once __DIR__ . '/Database.php';

// MODEL MATA KULIAH (COURSE)
// Menangani operasi data mata kuliah dan relasi berjenjang ke dosen dan program studi.
class Course {
    // Properti koneksi database PDO.
    private $db;

    // KONSTRUKTOR MODEL
    // Menginisialisasi koneksi database saat instance model dibuat.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // MENGAMBIL SELURUH DATA MATA KULIAH
    // Menggunakan relasi bertingkat (courses -> lecturers -> study_programs) dengan INNER JOIN
    // untuk menarik nama dosen pengampu dan program studi terkait dalam satu kali eksekusi query.
    // Diurutkan secara descending (terbaru di atas) berdasarkan ID.
    public function getAll()
    {
        $query = "SELECT
                    c.*,
                    l.name AS lecturer_name,
                    l.nidn,
                    sp.program_name,
                    sp.program_code
                  FROM courses c
                  INNER JOIN lecturers l
                    ON c.lecturer_id = l.id
                  INNER JOIN study_programs sp
                    ON l.study_program_id = sp.id
                  ORDER BY c.id DESC";

        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    // MENGAMBIL DETAIL MATA KULIAH BERDASARKAN ID
    // Menarik data satu mata kuliah spesifik beserta identitas dosen dan prodi untuk form edit atau detail.
    public function getById($id)
    {
        $query = "SELECT
                    c.*,
                    l.name AS lecturer_name,
                    sp.program_name
                  FROM courses c
                  INNER JOIN lecturers l
                    ON c.lecturer_id = l.id
                  INNER JOIN study_programs sp
                    ON l.study_program_id = sp.id
                  WHERE c.id = :id";

        $statement = $this->db->prepare($query);
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    // MENYIMPAN DATA MATA KULIAH BARU
    // Menyimpan atribut mata kuliah (kode, nama, sks, semester, tahun akademik, dan id dosen).
    public function create($data)
    {
        $query = "INSERT INTO courses
                    (lecturer_id, course_code, course_name, credits, semester, academic_year)
                  VALUES
                    (:lecturer_id, :course_code, :course_name, :credits, :semester, :academic_year)";

        $statement = $this->db->prepare($query);

        $statement->execute([
            'lecturer_id'   => $data['lecturer_id'],
            'course_code'   => $data['course_code'],
            'course_name'   => $data['course_name'],
            'credits'       => $data['credits'],
            'semester'      => $data['semester'],
            'academic_year' => $data['academic_year']
        ]);

        return $this->db->lastInsertId();
    }

    // MEMPERBARUI DATA MATA KULIAH
    // Mengubah informasi mata kuliah berdasarkan ID target. Pola parameter binding konsisten dengan method create.
    public function update($id, $data)
    {
        $query = "UPDATE courses
                  SET
                    lecturer_id = :lecturer_id,
                    course_code = :course_code,
                    course_name = :course_name,
                    credits = :credits,
                    semester = :semester,
                    academic_year = :academic_year
                  WHERE id = :id";

        $statement = $this->db->prepare($query);

        return $statement->execute([
            'id'            => $id,
            'lecturer_id'   => $data['lecturer_id'],
            'course_code'   => $data['course_code'],
            'course_name'   => $data['course_name'],
            'credits'       => $data['credits'],
            'semester'      => $data['semester'],
            'academic_year' => $data['academic_year']
        ]);
    }

    // MENGHAPUS DATA MATA KULIAH
    // Menghapus data mata kuliah berdasarkan ID kunci utama.
    public function delete($id)
    {
        $query = "DELETE FROM courses WHERE id = :id";

        $statement = $this->db->prepare($query);
        return $statement->execute(['id' => $id]);
    }
}
