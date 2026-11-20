-- Database: `tp_mvc25`
CREATE DATABASE IF NOT EXISTS `tp_mvc25` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tp_mvc25`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `lecturers`;
DROP TABLE IF EXISTS `study_programs`;
SET FOREIGN_KEY_CHECKS = 1;

-- Struktur tabel `study_programs`
CREATE TABLE `study_programs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_code` varchar(20) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `faculty` varchar(100) NOT NULL DEFAULT 'FPMIPA',
  `degree` varchar(5) NOT NULL DEFAULT 'S1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `program_code` (`program_code`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data untuk tabel `study_programs`
INSERT INTO `study_programs` (`id`, `program_code`, `program_name`, `faculty`, `degree`) VALUES
('1', 'D0151', 'Pendidikan Matematika', 'FPMIPA', 'S1'),
('2', 'D0251', 'Pendidikan Fisika', 'FPMIPA', 'S1'),
('3', 'D0351', 'Pendidikan Biologi', 'FPMIPA', 'S1'),
('4', 'D0451', 'Pendidikan Kimia', 'FPMIPA', 'S1'),
('5', 'D0551', 'Pendidikan Ilmu Komputer', 'FPMIPA', 'S1'),
('6', 'D5051', 'Matematika', 'FPMIPA', 'S1'),
('7', 'D5151', 'Fisika', 'FPMIPA', 'S1'),
('8', 'D5251', 'Biologi', 'FPMIPA', 'S1'),
('9', 'D5351', 'Kimia', 'FPMIPA', 'S1'),
('10', 'D5451', 'Ilmu Komputer', 'FPMIPA', 'S1'),
('11', 'D4051', 'Pendidikan IPA', 'FPMIPA', 'S1');

-- Struktur tabel `lecturers`
CREATE TABLE `lecturers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `nidn` varchar(30) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `join_date` date NOT NULL,
  `study_program_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nidn` (`nidn`),
  KEY `fk_lecturers_study_program` (`study_program_id`),
  CONSTRAINT `fk_lecturers_study_program` FOREIGN KEY (`study_program_id`) REFERENCES `study_programs` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data untuk tabel `lecturers`
INSERT INTO `lecturers` (`id`, `name`, `nidn`, `phone`, `join_date`, `study_program_id`) VALUES
('1', 'Dr. Rosa Ariani Sukamto, M.T.', '0013018201', '081221122334', '2012-08-01', '10'),
('2', 'Dr. Lala Septem Riza, M.T.', '0017098002', '081223344556', '2010-03-15', '10'),
('3', 'Yudi Wibisono, S.T., M.T.', '0025067803', '081334455667', '2014-02-01', '5'),
('4', 'Dr. Rani Megasari, M.T.', '0008118504', '081445566778', '2015-09-01', '10'),
('5', 'Prof. Dr. Munir, M.IT.', '0025106605', '081556677889', '2005-01-10', '5'),
('6', 'Dr. Rizky Rahman, M.Kom.', '0019048806', '081667788990', '2018-07-01', '6');

-- Struktur tabel `courses`
CREATE TABLE `courses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lecturer_id` int(10) unsigned NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `credits` int(11) NOT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  `academic_year` varchar(9) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_code` (`course_code`),
  KEY `fk_courses_lecturer` (`lecturer_id`),
  CONSTRAINT `fk_courses_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_courses_credits` CHECK (`credits` between 1 and 6)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data untuk tabel `courses`
INSERT INTO `courses` (`id`, `lecturer_id`, `course_code`, `course_name`, `credits`, `semester`, `academic_year`) VALUES
('1', '1', 'IK101', 'Algoritma dan Pemrograman', '3', 'Ganjil', '2025/2026'),
('2', '1', 'IK102', 'Rekayasa Perangkat Lunak', '3', 'Genap', '2025/2026'),
('3', '2', 'IK201', 'Kecerdasan Buatan', '3', 'Ganjil', '2025/2026'),
('4', '2', 'IK202', 'Machine Learning dan Penambangan Data', '3', 'Genap', '2025/2026'),
('5', '2', 'IK203', 'Desain dan Analisis Algoritma', '4', 'Ganjil', '2025/2026'),
('6', '3', 'PK101', 'Pemrograman Berorientasi Objek', '3', 'Genap', '2025/2026'),
('7', '3', 'PK102', 'Sistem Basis Data', '3', 'Ganjil', '2025/2026'),
('8', '4', 'IK301', 'Interaksi Manusia dan Komputer', '3', 'Ganjil', '2025/2026'),
('9', '4', 'IK302', 'Pengolahan Citra Digital', '3', 'Genap', '2025/2026'),
('10', '5', 'PK201', 'Multimedia Pembelajaran', '3', 'Ganjil', '2025/2026'),
('11', '5', 'PK202', 'Jaringan Komputer dan Keamanan Sistem', '3', 'Genap', '2025/2026'),
('12', '6', 'MT101', 'Matematika Diskrit', '3', 'Ganjil', '2025/2026');

