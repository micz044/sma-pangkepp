-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 04 Sep 2024 pada 02.13
-- Versi server: 8.0.30
-- Versi PHP: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aplikasi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `agama`
--

CREATE TABLE `agama` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `teacher_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `agama`
--

INSERT INTO `agama` (`id`, `name`, `description`, `teacher_id`, `subject_id`) VALUES
(6, 'Dasar Pendidikan Agama', '66d31c88e2f1e.pdf', 5, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahasa_indonesia`
--

CREATE TABLE `bahasa_indonesia` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `teacher_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bahasa_indonesia`
--

INSERT INTO `bahasa_indonesia` (`id`, `name`, `description`, `teacher_id`, `subject_id`) VALUES
(12, 'Menyusun Hasil Observasi', '66d30c3f190c9.pdf', 2, 2),
(17, 'Mengembangkan Pendapat', '66d30c6f29e70.pdf', 2, 2),
(19, 'Menyimpan ide melalui Anekdot', '66d30ca8881f8.pdf', 2, 2),
(20, 'Cerita Rakyat', '66d30d18e2e57.pdf', 2, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahasa_inggris`
--

CREATE TABLE `bahasa_inggris` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `teacher_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bahasa_inggris`
--

INSERT INTO `bahasa_inggris` (`id`, `name`, `description`, `teacher_id`, `subject_id`) VALUES
(7, 'Talking About Self', '66d30edee0115.pdf', 3, 3),
(8, 'Congratulating others', '66d30f989d60f.pdf', 3, 3),
(9, 'What Are you doing today', '66d30fcc5c631.pdf', 3, 3),
(10, 'Which One', '66d3100de2b91.pdf', 3, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `grades`
--

CREATE TABLE `grades` (
  `id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `nilai_tugas` decimal(5,2) DEFAULT NULL,
  `nilai_ulangan_harian` decimal(5,2) DEFAULT NULL,
  `kehadiran` decimal(5,2) DEFAULT NULL,
  `nilai_uts` decimal(5,2) DEFAULT NULL,
  `nilai_uas` decimal(5,2) DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `nilai_tugas`, `nilai_ulangan_harian`, `kehadiran`, `nilai_uts`, `nilai_uas`, `grade`) VALUES
(2, 2, 1, 85.00, 80.00, 90.00, 50.00, 30.00, 67.00),
(3, 4, 1, 78.00, 82.00, 95.00, 70.00, 85.00, 82.00),
(4, 5, 1, 92.00, 87.00, 85.00, 80.00, 90.00, 86.80),
(6, 4, 2, 65.00, 70.00, 85.00, 60.00, 75.00, 71.00),
(8, 1, 2, 88.00, 92.00, 100.00, 85.00, 93.00, 91.60),
(9, 2, 2, 80.00, 78.00, 88.00, 90.00, 46.00, 76.40),
(10, 5, 2, 78.00, 80.00, 90.00, 68.00, 74.00, 78.00),
(16, 6, 1, 80.00, 70.00, 78.00, 64.00, 80.00, 74.40),
(18, 6, 3, 82.00, 89.00, 74.00, 80.00, 90.00, 83.00),
(19, 6, 5, 80.00, 89.00, 90.00, 88.00, 70.00, 83.40),
(28, 4, 3, 89.00, 80.00, 78.00, 88.00, 78.00, 82.60),
(29, 5, 3, 86.00, 80.00, 74.00, 60.00, 68.00, 73.60),
(31, 2, 3, 80.00, 76.00, 88.00, 48.00, 63.00, 71.00),
(32, 1, 1, 86.00, 80.00, 68.00, 77.00, 90.00, 80.20),
(33, 2, 5, 88.00, 80.00, 88.00, 90.00, 86.00, 86.40),
(34, 4, 5, 80.00, 82.00, 84.00, 90.00, 88.00, 84.80),
(35, 5, 5, 88.00, 90.00, 98.00, 64.00, 79.00, 83.80),
(37, 1, 5, 80.00, 88.00, 78.00, 90.00, 80.00, 83.20),
(39, 6, 2, 88.00, 86.00, 90.00, 80.00, 80.00, 84.80),
(41, 1, 4, 80.00, 64.00, 72.00, 76.00, 60.00, 70.40),
(42, 2, 4, 88.00, 80.00, 90.00, 89.00, 82.00, 85.80),
(43, 4, 4, 70.00, 90.00, 88.00, 75.00, 86.00, 81.80),
(44, 5, 4, 80.00, 90.00, 96.00, 88.00, 76.00, 86.00),
(45, 6, 4, 80.00, 86.00, 84.00, 90.00, 90.00, 86.00),
(47, 1, 3, 88.00, 89.00, 80.00, 86.00, 86.00, 85.80);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kumpul_tugas`
--

CREATE TABLE `kumpul_tugas` (
  `id` int NOT NULL,
  `task_id` int NOT NULL,
  `student_class` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submission_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `siswa_id` int DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kumpul_tugas`
--

INSERT INTO `kumpul_tugas` (`id`, `task_id`, `student_class`, `file_path`, `submission_date`, `siswa_id`, `student_name`) VALUES
(5, 12, '10 IPA', 'uploads/feed.png', '2024-08-24 05:05:51', 2, 'Irsyad'),
(6, 17, '10 IPA', 'uploads/7054141-sengoku-basara-2-heroes-playstation-2-front-cover.jpg', '2024-08-24 05:08:23', 2, 'Fandi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `matematika`
--

CREATE TABLE `matematika` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `teacher_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `matematika`
--

INSERT INTO `matematika` (`id`, `name`, `description`, `teacher_id`, `subject_id`) VALUES
(9, 'Sistem Persamaan Linear', '66d30a0f069c7.pdf', 1, 1),
(10, 'Pertidaksamaan Linear', '66d309b40d375.pdf', 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_siswa`
--

CREATE TABLE `nilai_siswa` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `soal_id` int NOT NULL,
  `jawaban_siswa` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `tanggal` date NOT NULL,
  `rekomendasi_id` int DEFAULT NULL,
  `recommendation_score` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pkn`
--

CREATE TABLE `pkn` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `teacher_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pkn`
--

INSERT INTO `pkn` (`id`, `name`, `description`, `teacher_id`, `subject_id`) VALUES
(7, 'Materi PKN', '66d313c439083.pdf', 4, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int NOT NULL,
  `subject_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `tipe` varchar(25) NOT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `recommendations`
--

INSERT INTO `recommendations` (`id`, `subject_id`, `title`, `tipe`, `description`, `url`) VALUES
(5, 1, 'VIDEO PEMBELAJARAN MATEMATIKA', 'video', 'BELAJAR EKSPONEN', 'https://www.youtube.com/watch?v=AlrOq3W7IZ4'),
(7, 1, 'MODUL ONLINE MATEMATIKA', 'modul', 'BELAJAR TEORIMA PITAGORAS', '../rekomendasi/matematika/BAB VII TEOREMA PYTHAGORAS.pdf'),
(14, 1, 'PPT MATEMATIKA', 'ppt', 'BELAJAR TEOREMA PITAGORAS', '../rekomendasi/matematika/Matematika Kelas 8 BAB 6 -.pptx.pdf'),
(15, 2, 'VIDEO PEMBELAJARAN BAHASA INDONESIA', 'video', 'BELAJAR PUISI', 'https://www.youtube.com/watch?v=51AWWs6UahM'),
(16, 2, 'MODUL ONLINE BAHASA INDONESIA', 'modul', 'BELAJAR TATA BAHASA', '../rekomendasi/bahasa_indonesia/tata_bahasa.pdf'),
(17, 2, 'INFOGRAFIS BAHASA INDONESIA', 'infografis', 'BELAJAR TATA BAHASA', '../rekomendasi/bahasa_indonesia/eno.pdf'),
(18, 2, 'PPT BAHASA INDONESIA', 'ppt', 'BELAJAR SASTRA INDONESIA', '../rekomendasi/bahasa_indonesia/ppt_sastra.pdf'),
(23, 3, 'VIDEO REKOMENDASI BAHASA INGGRIS', 'video', 'BELAJAR VIDEO BAHASA INGGRIS', 'https://www.youtube.com/watch?v=owC80a8xHT4'),
(26, 3, 'MODUL REKOMENDASI BAHASA INGGRIS', 'modul', 'BELAJAR BAHASA INGGRIS', '../rekomendasi/bahasa_inggris/Unit 1 How are you (modulmerdeka.com).docx.pdf'),
(27, 3, 'PPT REKOMENDASI BAHASA INGGRIS', 'ppt', 'BELAJAR BAHASA INGGRIS', '../rekomendasi/bahasa_inggris/English on Sky 1 Chapter 1.pptx.pdf'),
(36, 3, 'INFOGRAFIS REKOMENDASI BAHASA INGGRIS', 'infografis', 'INFOGRAFIS BAHASA INGGRIS', '../rekomendasi/bahasa_inggris/en.pdf'),
(38, 1, 'INFOGRAFIS MATEMATIKA', 'infografis', 'INFOGRAFIS MATEMATIKA', '../rekomendasi/matematika/Doc1.pdf'),
(39, 5, 'VIDEO PEMBELAJARAN PKN', 'video', 'VIDEO PEMBELAJARAN PKN', 'https://www.youtube.com/watch?v=-dLHUlG_QuY'),
(40, 5, 'MODUL PKN', 'modul', 'MODUL PEMBELAJARAN PKN', '../rekomendasi/pkn/PPKn_Pembelajaran-1.pdf'),
(41, 5, 'PPT PEMBELAJARAN PKN', 'ppt', 'PPT PEMBELAJARAN PKN', '../rekomendasi/pkn/PPKN Kelas 7 BAB 1.pptx.pdf'),
(42, 5, 'INFOGRAFIS PEMBELAJARAN PKN', 'infografis', 'INFOGRAFIS PEMBELAJARAN PKN', '../rekomendasi/pkn/PKN.pdf'),
(43, 4, 'VIDEO PEMBELAJARAN AGAMA', 'video', 'VIDEO PEMBELAJARAN AGAMA', 'https://www.youtube.com/watch?v=nhaTgntD5wU'),
(44, 4, 'MODUL PEMBELAJARAN BAHASA INDONESIA', 'modul', 'MODUL PEMBELAJARAN BAHASA INDONESIA', '../rekomendasi/agama/BUKU MODUL IPI.pdf'),
(45, 4, 'PPT PEMBELAJARAN AGAMA', 'ppt', 'PPT PEMBELAJARAN AGAMA', '../rekomendasi/agama/STD PAI SMA kls X Bab 1 ok.pdf'),
(46, 4, 'INFOGRAFIS PEMBELAJARAN AGAMA', 'infografis', 'INFOGRAFIS PEMBELAJARAN AGAMA', '../rekomendasi/agama/ag.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal_rekomendasi`
--

CREATE TABLE `soal_rekomendasi` (
  `id` int NOT NULL,
  `soal` text,
  `jawaban_benar` varchar(255) DEFAULT NULL,
  `opsi1` varchar(255) DEFAULT NULL,
  `opsi2` varchar(255) DEFAULT NULL,
  `opsi3` varchar(255) DEFAULT NULL,
  `opsi4` varchar(255) DEFAULT NULL,
  `rekomendasi_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `soal_rekomendasi`
--

INSERT INTO `soal_rekomendasi` (`id`, `soal`, `jawaban_benar`, `opsi1`, `opsi2`, `opsi3`, `opsi4`, `rekomendasi_id`) VALUES
(6, 'Diketahui sebuah segitiga dengan panjang sisi 6 cm dan 8 cm. Hidung panjang sisi miring segitiga tersebut.', '10 cm', '8 cm', '10 cm', '12 cm', '14 cm', 7),
(7, 'Sebuah segitiga memiliki panjang sisi miring 15 cm dan salah satu sisi adalah 9 cm. Hitung panjang sisi yang lainnya.', '12 cm', '10 cm', '11 cm', '12 cm', '13 cm', 7),
(8, 'Diketahui sebuah segitiga dengan panjang sisi miring 20 cm dan salah satu sisi adalah 12 cm. Hitung panjang sisi yang lainnya.', '16 cm', '14 cm', '15 cm', '16 cm', '18 cm', 7),
(9, 'Sebuah segitiga dengan panjang sisi 9 cm dan 12 cm, hitung panjang sisi miringnya.', '15 cm', '14 cm', '15 cm', '16 cm', '17 cm', 7),
(10, 'Jika panjang sisi miring segitiga adalah 25 cm dan panjang salah satu sisi adalah 7 cm, berapakah panjang sisi yang lainnya?', '24 cm', '22 cm', '23 cm', '24 cm', '25 cm', 7),
(11, 'What is the synonym of \"happy\"?', 'joyful', 'sad', 'angry', 'joyful', NULL, 23),
(12, 'Which sentence is correct?', 'She can speak English.', 'She can speak English.', 'She can speaking English.', 'She can spoke English.', NULL, 23),
(13, 'What is the past tense of \"go\"?', 'went', 'goes', 'going', 'gone', NULL, 23),
(14, 'Choose the correct form of the verb: \"He _____ to the store yesterday.\"', 'went', 'go', 'going', 'gone', NULL, 23),
(15, 'Select the correct sentence.', 'I have a book.', 'I have a book.', 'I having a book.', 'I had a book.', NULL, 23),
(16, 'Apa yang dimaksud dengan struktur teks naratif?', 'Bagian cerita', 'Bagian cerita', 'Jenis teks', 'Format penulisan', 'Jenis cerita', 15),
(17, 'Sebutkan unsur-unsur utama dalam teks deskripsi!', 'Objek', 'Tujuan', 'Objek', 'Ciri-ciri', 'Pengenalan', 15),
(18, 'Apa perbedaan antara teks prosedur dan teks eksposisi?', 'Langkah-langkah', 'Jenis teks', 'Langkah-langkah', 'Pendapat', 'Keduanya', 15),
(19, 'Apa yang dimaksud dengan kalimat utama dalam teks?', 'Pokok bahasan', 'Pokok bahasan', 'Penjelasan', 'Kesimpulan', 'Pendapat', 15),
(20, 'Sebutkan ciri-ciri teks berita!', 'Subjektif', 'Subjektif', 'Akurat', 'Informasi', 'Pendapat', 15),
(22, 'Apakah Nanda Seorang Raja Iblis? Iyakah?', 'netral', 'Sangat Setuju', 'setuju', 'netral ', 'ragu-ragu', 7),
(28, 'Jika a^2 + b^2 = c^2, berapakah nilai c jika a = 3 dan b = 4?', '5', '5', '6', '7', '8', 5),
(29, 'Pada segitiga siku-siku, jika salah satu sudutnya 30 derajat, berapakah besar sudut lainnya?', '60 derajat', '45 derajat', '60 derajat', '75 derajat', '90 derajat', 5),
(30, 'Sebuah segitiga sama kaki memiliki panjang alas 10 cm dan tinggi 8 cm. Berapakah panjang sisi miringnya?', '10 cm', '8 cm', '10 cm', '12 cm', '14 cm', 5),
(31, 'Hitung luas segitiga dengan alas 6 cm dan tinggi 4 cm.', '12 cm^2', '10 cm^2', '12 cm^2', '14 cm^2', '16 cm^2', 5),
(32, 'Jika panjang alas segitiga adalah 8 cm dan tingginya adalah 6 cm, berapakah luas segitiga tersebut?', '24 cm^2', '20 cm^2', '24 cm^2', '28 cm^2', '32 cm^2', 5),
(33, 'Sebuah segitiga memiliki sisi-sisi dengan panjang 3 cm, 4 cm, dan 5 cm. Jenis segitiga apakah ini?', 'Segitiga siku-siku', 'Segitiga sama sisi', 'Segitiga sama kaki', 'Segitiga siku-siku', 'Segitiga sembarang', 7),
(34, 'Jika panjang alas segitiga adalah 12 cm dan tingginya 5 cm, berapakah luas segitiga tersebut?', '30 cm^2', '20 cm^2', '30 cm^2', '40 cm^2', '50 cm^2', 7),
(35, 'Pada segitiga sama kaki, jika salah satu sudutnya 70 derajat, berapakah besar dua sudut lainnya?', '55 derajat', '70 derajat', '55 derajat', '60 derajat', '45 derajat', 7),
(36, 'Hitung keliling segitiga dengan sisi-sisi 7 cm, 24 cm, dan 25 cm.', '56 cm', '55 cm', '56 cm', '57 cm', '58 cm', 7),
(37, 'Jika panjang sisi miring sebuah segitiga siku-siku adalah 13 cm dan satu sisinya adalah 5 cm, berapakah panjang sisi yang lainnya?', '12 cm', '10 cm', '11 cm', '12 cm', '13 cm', 7),
(38, 'Pada segitiga siku-siku, jika panjang sisi-sisi tegak lurusnya adalah 9 cm dan 12 cm, berapakah panjang sisi miringnya?', '15 cm', '14 cm', '15 cm', '16 cm', '17 cm', 14),
(39, 'Sebuah segitiga memiliki sudut-sudut 30 derajat, 60 derajat, dan 90 derajat. Jenis segitiga apakah ini?', 'Segitiga siku-siku', 'Segitiga sama sisi', 'Segitiga sama kaki', 'Segitiga siku-siku', 'Segitiga sembarang', 14),
(40, 'Jika panjang alas dan tinggi sebuah segitiga masing-masing adalah 10 cm dan 6 cm, berapakah luas segitiga tersebut?', '30 cm^2', '20 cm^2', '30 cm^2', '40 cm^2', '50 cm^2', 14),
(41, 'Pada segitiga sama kaki, jika sudut di antara dua sisi yang sama adalah 40 derajat, berapakah besar sudut di kaki segitiga lainnya?', '70 derajat', '60 derajat', '70 derajat', '80 derajat', '90 derajat', 14),
(42, 'Jika sebuah segitiga sama sisi memiliki panjang sisi 8 cm, berapakah keliling segitiga tersebut?', '24 cm', '20 cm', '22 cm', '24 cm', '26 cm', 14),
(43, 'Apa yang dimaksud dengan majas hiperbola dalam bahasa Indonesia?', 'Pernyataan yang berlebihan', 'Pernyataan yang merendahkan', 'Pernyataan yang berlebihan', 'Pernyataan yang menggambarkan', 'Pernyataan yang meremehkan', 16),
(44, 'Pilih kalimat yang menggunakan majas personifikasi.', 'Angin malam berbisik lirih', 'Rumah itu berdiri megah', 'Angin malam berbisik lirih', 'Air laut begitu tenang', 'Bunga itu tumbuh subur', 16),
(45, 'Apa fungsi dari kalimat utama dalam sebuah paragraf?', 'Mengungkapkan ide pokok', 'Menjelaskan rincian', 'Mengungkapkan ide pokok', 'Memberikan contoh', 'Menyimpulkan paragraf', 16),
(46, 'Struktur teks deskripsi yang benar adalah...', 'Pengenalan, deskripsi, simpulan', 'Pengenalan, deskripsi, simpulan', 'Pendahuluan, isi, penutup', 'Pembuka, inti, penutup', 'Pengantar, isi, kesimpulan', 16),
(47, 'Dalam teks eksposisi, apa tujuan dari argumen yang disajikan?', 'Meyakinkan pembaca', 'Menyampaikan informasi', 'Meyakinkan pembaca', 'Menghibur pembaca', 'Menjelaskan proses', 16),
(48, 'Apa yang dimaksud dengan infografis?', 'Penyajian informasi dalam bentuk visual', 'Penyajian informasi dalam bentuk tulisan panjang', 'Penyajian informasi dalam bentuk visual', 'Penyajian informasi dalam bentuk suara', 'Penyajian informasi dalam bentuk video', 17),
(49, 'Apa tujuan utama dari infografis?', 'Menyederhanakan informasi kompleks', 'Menambah kompleksitas informasi', 'Menyederhanakan informasi kompleks', 'Menyajikan data tanpa visualisasi', 'Membuat informasi menjadi lebih sulit dipahami', 17),
(50, 'Infografis efektif digunakan untuk?', 'Menyajikan data statistik', 'Menyajikan data statistik', 'Membuat narasi panjang', 'Menjelaskan proses langkah demi langkah', 'Menyajikan peta', 17),
(51, 'Apa elemen penting dalam infografis?', 'Grafik dan teks', 'Hanya teks', 'Hanya grafik', 'Grafik dan teks', 'Hanya gambar', 17),
(52, 'Bagaimana cara membuat infografis yang efektif?', 'Gunakan visualisasi yang jelas dan ringkas', 'Gunakan visualisasi yang rumit dan padat', 'Hindari penggunaan gambar', 'Gunakan visualisasi yang jelas dan ringkas', 'Jangan gunakan warna kontras', 17),
(53, 'Apa yang dimaksud dengan presentasi PowerPoint?', 'Sebuah alat untuk membuat slide presentasi', 'Sebuah alat untuk membuat dokumen teks', 'Sebuah alat untuk membuat slide presentasi', 'Sebuah alat untuk mengedit foto', 'Sebuah alat untuk membuat video', 18),
(54, 'Apa keuntungan menggunakan PowerPoint dalam presentasi?', 'Memungkinkan penyajian visual yang menarik', 'Memerlukan banyak waktu untuk persiapan', 'Memungkinkan penyajian visual yang menarik', 'Tidak mendukung multimedia', 'Hanya mendukung teks', 18),
(55, 'Dalam PowerPoint, apa fungsi dari slide master?', 'Menetapkan format dan tata letak slide', 'Menambahkan animasi pada slide', 'Menetapkan format dan tata letak slide', 'Mengatur transisi slide', 'Mengatur warna latar belakang slide', 18),
(56, 'Apa fitur utama yang dapat digunakan untuk menambah visual pada slide PowerPoint?', 'Gambar, grafik, dan diagram', 'Hanya teks', 'Gambar, grafik, dan diagram', 'Hanya video', 'Hanya tabel', 18),
(57, 'Apa yang harus diperhatikan saat mendesain slide PowerPoint?', 'Kejelasan dan konsistensi desain', 'Kepadatan informasi', 'Kejelasan dan konsistensi desain', 'Hanya menggunakan warna gelap', 'Menggunakan font yang sangat kecil', 18),
(58, 'What is the main purpose of the video recommendation?', 'To provide educational content', 'To provide entertainment', 'To provide educational content', 'To promote products', 'To share personal experiences', 23),
(59, 'Which English language skill is most likely to be enhanced by watching educational videos?', 'Listening', 'Writing', 'Listening', 'Reading', 'Speaking', 23),
(60, 'What should be the focus of an English language learning video?', 'Clear pronunciation and relevant content', 'Background music and special effects', 'Clear pronunciation and relevant content', 'Complex vocabulary and long sentences', 'Fast-paced visuals and animations', 23),
(61, 'How can viewers make the most of educational videos for language learning?', 'By actively taking notes and practicing', 'By watching passively without interaction', 'By actively taking notes and practicing', 'By only watching the videos once', 'By ignoring the subtitles', 23),
(62, 'What feature in educational videos can aid comprehension?', 'Subtitles and interactive elements', 'Fast talking and no subtitles', 'Subtitles and interactive elements', 'No visual aids', 'Complex jargon without explanations', 23),
(63, 'What is the primary use of a language learning module?', 'To provide structured lessons and practice', 'To provide random information', 'To provide structured lessons and practice', 'To entertain viewers', 'To showcase new vocabulary', 26),
(64, 'Which element is crucial in a language learning module?', 'Clear explanations and exercises', 'Only visual effects', 'Clear explanations and exercises', 'Background music', 'Speed of delivery', 26),
(65, 'How should a module be organized for effective learning?', 'In a logical sequence of topics', 'In a random order', 'In a logical sequence of topics', 'With minimal explanations', 'With complex language', 26),
(66, 'What is a common feature of language learning modules?', 'Exercises and quizzes', 'Background music', 'Exercises and quizzes', 'Extensive use of jargon', 'No interactive components', 26),
(67, 'What can enhance the effectiveness of a language learning module?', 'Interactive elements and feedback', 'Only text without interaction', 'Interactive elements and feedback', 'Long and dense paragraphs', 'No visual aids', 26),
(68, 'What is the main purpose of using PowerPoint for language learning?', 'To create engaging and visual presentations', 'To create text documents', 'To create engaging and visual presentations', 'To design websites', 'To edit videos', 27),
(69, 'Which feature of PowerPoint helps in organizing content?', 'Slide layout and design', 'Background color only', 'Slide layout and design', 'Slide transitions', 'Animation effects', 27),
(70, 'How can animations in PowerPoint enhance a presentation?', 'By making it more engaging and dynamic', 'By making it less informative', 'By making it more engaging and dynamic', 'By slowing down the presentation', 'By adding unnecessary effects', 27),
(71, 'What is a key element to consider when designing slides in PowerPoint?', 'Clarity and simplicity of content', 'Use of excessive text', 'Clarity and simplicity of content', 'Using multiple fonts', 'Bright, contrasting colors', 27),
(72, 'How can PowerPoint help in presenting complex information?', 'By using charts and diagrams', 'By adding background music', 'By using charts and diagrams', 'By including lengthy paragraphs', 'By avoiding visual aids', 27),
(78, 'What is the primary advantage of using infographics for language learning?', 'To simplify and visually present information', 'To provide lengthy textual explanations', 'To simplify and visually present information', 'To create complex diagrams', 'To use only text-based content', 36),
(79, 'Which element is most important in an infographic?', 'Clarity of visuals and information', 'Use of many colors', 'Clarity of visuals and information', 'Complex graphics', 'Large amounts of text', 36),
(80, 'How can infographics improve comprehension of language concepts?', 'By presenting information in a visually organized manner', 'By including a lot of text', 'By presenting information in a visually organized manner', 'By avoiding visual elements', 'By using only verbal explanations', 36),
(81, 'What should be avoided when creating an infographic for language learning?', 'Overloading it with text and complex graphics', 'Using clear visuals and simple text', 'Overloading it with text and complex graphics', 'Ensuring visual clarity and simplicity', 'Focusing on a single concept', 36),
(82, 'What role do colors play in an infographic?', 'They enhance visual appeal and help differentiate information', 'They distract from the content', 'They enhance visual appeal and help differentiate information', 'They make the infographic look cluttered', 'They are irrelevant to the content', 36),
(88, 'What is the main benefit of using infographics in math education?', 'To visually represent mathematical concepts and data', 'To provide text-heavy explanations', 'To visually represent mathematical concepts and data', 'To create complex equations', 'To present numerical data in raw form', 38),
(89, 'Which element should be highlighted in a math infographic?', 'Clear and concise visual representation of data', 'Detailed mathematical proofs', 'Clear and concise visual representation of data', 'Extensive use of mathematical jargon', 'Long textual explanations', 38),
(90, 'How can infographics help students understand mathematical concepts?', 'By simplifying complex concepts into visual formats', 'By providing lengthy textual descriptions', 'By simplifying complex concepts into visual formats', 'By presenting raw data without context', 'By using complex formulas', 38),
(91, 'What should be avoided in a math infographic?', 'Overloading with too much text and complex visuals', 'Using clear and relevant visuals', 'Overloading with too much text and complex visuals', 'Keeping visuals simple and focused', 'Presenting data in a structured manner', 38),
(92, 'What role do visuals play in a math infographic?', 'They help in understanding and retaining mathematical concepts', 'They distract from the main content', 'They help in understanding and retaining mathematical concepts', 'They are irrelevant to the content', 'They should be avoided', 38),
(93, 'Apa yang dimaksud dengan kedaulatan rakyat?', 'Kekuasaan tertinggi di tangan rakyat', 'Kekuasaan tertinggi di tangan rakyat', 'Kekuasaan tertinggi di tangan presiden', 'Kekuasaan tertinggi di tangan menteri', 'Kekuasaan tertinggi di tangan gubernur', 39),
(94, 'Siapakah yang memiliki kekuasaan untuk membuat undang-undang di Indonesia?', 'DPR', 'Presiden', 'Menteri', 'DPR', 'Mahkamah Konstitusi', 39),
(95, 'Apa yang dimaksud dengan hak asasi manusia?', 'Hak yang dimiliki oleh seluruh manusia sejak lahir', 'Hak yang dimiliki oleh warga negara tertentu', 'Hak yang dimiliki oleh seluruh manusia sejak lahir', 'Hak yang diberikan oleh pemerintah', 'Hak yang dimiliki oleh para pejabat', 39),
(96, 'Apa yang menjadi dasar hukum negara Indonesia?', 'UUD 1945', 'UUD 1945', 'Pancasila', 'Proklamasi', 'GBHN', 39),
(97, 'Apa fungsi dari lembaga Mahkamah Konstitusi?', 'Mengadili sengketa antar lembaga negara', 'Mengawasi pelaksanaan undang-undang', 'Menegakkan hukum di tingkat pertama', 'Mengadili sengketa antar lembaga negara', 'Memberikan putusan akhir pada kasus pidana', 39),
(98, 'Apa tujuan dari sistem pemerintahan presidensial?', 'Presiden sebagai kepala negara dan kepala pemerintahan', 'Presiden memiliki kekuasaan penuh', 'Presiden sebagai kepala negara dan kepala pemerintahan', 'Presiden hanya sebagai kepala negara', 'Presiden sebagai kepala pemerintahan dan menteri sebagai kepala negara', 40),
(99, 'Apa yang dimaksud dengan negara hukum?', 'Negara yang mengutamakan hukum sebagai dasar negara', 'Negara yang mengutamakan hukum sebagai dasar negara', 'Negara yang tidak memperhatikan hukum', 'Negara yang kekuasaan tertinggi ada pada raja', 'Negara yang hukum dibuat hanya untuk rakyat', 40),
(100, 'Apa itu kekuasaan eksekutif?', 'Kekuasaan untuk menegakkan hukum dan administrasi negara', 'Kekuasaan untuk membuat undang-undang', 'Kekuasaan untuk menegakkan hukum dan administrasi negara', 'Kekuasaan untuk mengadili perkara', 'Kekuasaan untuk memutuskan sengketa antar lembaga', 40),
(101, 'Apa peran DPR dalam sistem pemerintahan Indonesia?', 'Menetapkan anggaran dan mengawasi pemerintah', 'Menetapkan anggaran dan mengawasi pemerintah', 'Membuat keputusan akhir dalam perkara pidana', 'Menentukan kebijakan luar negeri', 'Mengangkat pejabat tinggi negara', 40),
(102, 'Apa yang dimaksud dengan check and balances dalam pemerintahan?', 'Sistem pengawasan antar lembaga pemerintahan', 'Sistem pengawasan antar lembaga pemerintahan', 'Kekuasaan penuh lembaga legislatif', 'Pengaturan kekuasaan di tingkat lokal', 'Pemerintahan yang dijalankan oleh presiden', 40),
(103, 'Apa yang dimaksud dengan otonomi daerah?', 'Kewenangan daerah untuk mengatur dan mengurus rumah tangganya sendiri', 'Kewenangan daerah untuk mengatur dan mengurus rumah tangganya sendiri', 'Pemerintahan pusat yang mengatur seluruh wilayah', 'Pengaturan kekuasaan oleh presiden', 'Kewenangan yang diberikan kepada pemerintah pusat', 41),
(104, 'Siapakah yang memiliki kekuasaan legislatif di Indonesia?', 'DPR', 'Presiden', 'DPR', 'Mahkamah Konstitusi', 'Menteri', 41),
(105, 'Apa yang menjadi dasar dari pembentukan undang-undang di Indonesia?', 'UUD 1945', 'Proklamasi', 'UUD 1945', 'Pancasila', 'GBHN', 41),
(106, 'Apa fungsi dari lembaga eksekutif?', 'Menegakkan dan melaksanakan undang-undang', 'Mengeluarkan peraturan perundang-undangan', 'Menegakkan dan melaksanakan undang-undang', 'Mengawasi lembaga legislatif', 'Menyelesaikan sengketa hukum', 41),
(107, 'Apa yang dimaksud dengan kedaulatan negara?', 'Kekuasaan penuh negara dalam menentukan urusan dalam negeri dan luar negeri', 'Kekuasaan penuh negara dalam menentukan urusan dalam negeri dan luar negeri', 'Kekuasaan negara di bawah pimpinan presiden', 'Kekuasaan tertinggi di tangan menteri', 'Kekuasaan untuk membuat peraturan daerah', 41),
(108, 'Apa yang dimaksud dengan HAM?', 'Hak yang dimiliki oleh seluruh manusia tanpa kecuali', 'Hak yang dimiliki oleh seluruh manusia tanpa kecuali', 'Hak yang diberikan oleh pemerintah', 'Hak yang dimiliki hanya oleh warga negara', 'Hak khusus untuk golongan tertentu', 42),
(109, 'Apa peran Mahkamah Konstitusi dalam sistem pemerintahan Indonesia?', 'Mengadili sengketa pemilu', 'Mengadili sengketa pemilu', 'Menetapkan anggaran negara', 'Mengatur pelaksanaan peraturan perundang-undangan', 'Mengawasi jalannya pemerintahan daerah', 42),
(110, 'Apa yang dimaksud dengan negara kesatuan?', 'Negara yang memiliki satu pemerintahan pusat', 'Negara yang memiliki satu pemerintahan pusat', 'Negara yang terdiri dari beberapa negara bagian', 'Negara yang tidak memiliki struktur pemerintahan', 'Negara yang memiliki dua cabang pemerintahan', 42),
(111, 'Apa yang dimaksud dengan fungsi legislatif?', 'Fungsi membuat dan menetapkan undang-undang', 'Fungsi membuat dan menetapkan undang-undang', 'Fungsi menegakkan hukum dan administrasi', 'Fungsi mengadili perkara dan sengketa', 'Fungsi menentukan kebijakan luar negeri', 42),
(112, 'Siapakah yang memiliki kewenangan untuk mengubah konstitusi di Indonesia?', 'DPR dan MPR', 'Presiden', 'DPR dan MPR', 'Mahkamah Konstitusi', 'Menteri', 42),
(113, 'Apa yang dimaksud dengan rukun iman?', 'Pokok ajaran dalam agama Islam', 'Kewajiban yang harus dilakukan dalam shalat', 'Pokok ajaran dalam agama Islam', 'Syarat sah puasa Ramadan', 'Tata cara zakat fitrah', 43),
(114, 'Apa yang menjadi sumber hukum utama dalam Islam?', 'Al-Qur\'an', 'Hadis', 'Ijma', 'Qiyas', 'Al-Qur\'an', 43),
(115, 'Apa yang dimaksud dengan syahadat?', 'Perkataan pengakuan iman', 'Perkataan pengakuan iman', 'Kewajiban dalam shalat', 'Jenis zakat', 'Puasa wajib di bulan Ramadan', 43),
(116, 'Apa yang menjadi tujuan dari ibadah haji?', 'Untuk mendekatkan diri kepada Allah dan mengikuti sunnah Nabi', 'Untuk mendekatkan diri kepada Allah dan mengikuti sunnah Nabi', 'Menjadi kaya', 'Memperoleh gelar haji', 'Mengunjungi tempat suci', 43),
(117, 'Apa yang harus dilakukan jika seseorang melanggar larangan puasa?', 'Membayar fidyah', 'Mengganti puasa di hari lain', 'Membayar fidyah', 'Menambah puasa sehari lagi', 'Mengulang puasa pada bulan berikutnya', 43),
(118, 'Apa yang dimaksud dengan zakat?', 'Pemberian harta untuk membersihkan diri', 'Pemberian harta untuk membersihkan diri', 'Kewajiban melaksanakan shalat', 'Sumbangan untuk orang miskin', 'Kewajiban puasa di bulan Ramadan', 44),
(119, 'Apa yang menjadi syarat sahnya shalat?', 'Menutup aurat dan menghadap kiblat', 'Menutup aurat dan menghadap kiblat', 'Menunjukkan kepedulian sosial', 'Berpuasa di bulan Ramadan', 'Membayar zakat', 44),
(120, 'Apa yang dimaksud dengan Hadis?', 'Kumpulan ajaran dan tindakan Nabi Muhammad SAW', 'Kumpulan ajaran dan tindakan Nabi Muhammad SAW', 'Kitab suci umat Islam', 'Kumpulan doa dan dzikir', 'Pernyataan iman seorang Muslim', 44),
(121, 'Apa yang harus dilakukan sebelum menjalankan ibadah haji?', 'Menyiapkan bekal dan mental', 'Menyiapkan bekal dan mental', 'Mengambil zakat', 'Melakukan puasa sunnah', 'Mempelajari Hadis', 44),
(122, 'Apa yang dimaksud dengan puasa Sunnah?', 'Puasa yang dilakukan di luar bulan Ramadan', 'Puasa yang dilakukan di luar bulan Ramadan', 'Puasa wajib bagi semua Muslim', 'Puasa di bulan Ramadan', 'Puasa yang dilakukan setelah shalat', 44),
(123, 'Apa yang dimaksud dengan doa?', 'Permohonan kepada Allah', 'Permohonan kepada Allah', 'Ritual khusus dalam shalat', 'Puasa wajib bagi umat Islam', 'Perayaan hari besar Islam', 45),
(124, 'Apa yang dimaksud dengan ibadah?', 'Aktivitas yang dilakukan untuk mendekatkan diri kepada Allah', 'Aktivitas yang dilakukan untuk mendekatkan diri kepada Allah', 'Pekerjaan sehari-hari', 'Sosialisasi dengan masyarakat', 'Kegiatan rutin harian', 45),
(125, 'Apa yang menjadi syarat sahnya ibadah haji?', 'Menjaga kesopanan dan melaksanakan rukun haji', 'Menjaga kesopanan dan melaksanakan rukun haji', 'Menjaga kesehatan dan berdoa', 'Menyiapkan dana yang cukup', 'Menjaga pola makan selama perjalanan', 45),
(126, 'Apa yang dimaksud dengan shalat?', 'Ritual ibadah yang dilakukan dengan gerakan khusus dan bacaan', 'Ritual ibadah yang dilakukan dengan gerakan khusus dan bacaan', 'Kewajiban memberikan zakat', 'Puasa di bulan Ramadan', 'Doa kepada Allah', 45),
(127, 'Apa yang menjadi tujuan dari shalat?', 'Untuk mengingat dan mendekatkan diri kepada Allah', 'Untuk mengingat dan mendekatkan diri kepada Allah', 'Untuk mendapatkan keuntungan duniawi', 'Untuk memperpanjang usia', 'Untuk merayakan hari besar Islam', 45),
(128, 'Apa yang dimaksud dengan iman?', 'Keyakinan hati yang diucapkan dan diamalkan', 'Keyakinan hati yang diucapkan dan diamalkan', 'Kewajiban dalam shalat', 'Zakat yang harus dikeluarkan', 'Puasa di bulan Ramadan', 46),
(129, 'Apa yang menjadi tujuan dari membaca Al-Qur\'an?', 'Untuk mendapatkan petunjuk hidup dan mendekatkan diri kepada Allah', 'Untuk mendapatkan petunjuk hidup dan mendekatkan diri kepada Allah', 'Untuk memperkaya pengetahuan duniawi', 'Untuk mendapatkan hadiah', 'Untuk menambah waktu luang', 46),
(130, 'Apa yang dimaksud dengan sunnah?', 'Kegiatan atau perkataan Nabi Muhammad SAW yang dijadikan contoh', 'Kegiatan atau perkataan Nabi Muhammad SAW yang dijadikan contoh', 'Kitab suci umat Islam', 'Hukum yang ditetapkan oleh pemerintah', 'Tata cara puasa Ramadan', 46),
(131, 'Apa yang harus dilakukan setelah shalat?', 'Berdoa dan memohon ampunan', 'Berdoa dan memohon ampunan', 'Langsung beraktivitas', 'Membaca berita terbaru', 'Menghitung zakat', 46),
(132, 'Apa yang dimaksud dengan halal?', 'Hal yang diperbolehkan dalam ajaran Islam', 'Hal yang diperbolehkan dalam ajaran Islam', 'Hal yang dilarang dalam ajaran Islam', 'Hal yang tidak terkait dengan ajaran Islam', 'Hal yang hanya berlaku untuk hari raya', 46);

-- --------------------------------------------------------

--
-- Struktur dari tabel `students`
--

CREATE TABLE `students` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nis` int NOT NULL,
  `class` varchar(255) DEFAULT NULL,
  `alamat` varchar(25) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') NOT NULL,
  `gaya_belajar` varchar(25) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `students`
--

INSERT INTO `students` (`id`, `user_id`, `name`, `nis`, `class`, `alamat`, `no_hp`, `date_of_birth`, `jenis_kelamin`, `gaya_belajar`, `profile_photo`) VALUES
(1, 1, 'A. MUH. FACHRY RAIMAN', 205001, 'IPS', 'Pangkep', '843637640', '2005-06-15', 'laki-laki', 'infografis', 'uploads/student-icon-png-15.jpg'),
(2, 3, 'FAHMI FARID', 205002, 'IPS', 'Pangkep', '083192312123', '2024-07-03', 'laki-laki', 'video', 'uploads/student-icon-png-15.jpg'),
(4, 5, 'SULJALALI WAL IKRAM', 205003, 'IPS', 'Pangkep', '876487329', '2024-07-04', 'laki-laki', 'modul', 'uploads/student-icon-png-15.jpg'),
(5, 7, 'ILHAM BAKRI', 205007, 'IPS', 'Pangkep', '837428734', '2024-07-25', 'laki-laki', 'video', 'uploads/student-icon-png-15.jpg'),
(6, 8, 'MUH IKRAM', 205008, 'IPS', 'Pangkep', '877264762', '2024-07-31', 'laki-laki', 'video', 'uploads/student-icon-png-15.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `student_subjects`
--

CREATE TABLE `student_subjects` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `subject_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `student_subjects`
--

INSERT INTO `student_subjects` (`id`, `student_id`, `subject_id`) VALUES
(1, 1, 1),
(10, 1, 2),
(47, 1, 3),
(50, 1, 4),
(54, 1, 5),
(6, 2, 1),
(2, 2, 2),
(17, 2, 3),
(52, 2, 4),
(55, 2, 5),
(7, 4, 1),
(12, 4, 2),
(18, 4, 3),
(51, 4, 4),
(57, 4, 5),
(9, 5, 1),
(14, 5, 2),
(48, 5, 3),
(4, 5, 4),
(56, 5, 5),
(8, 6, 1),
(13, 6, 2),
(49, 6, 3),
(53, 6, 4),
(5, 6, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `subjects`
--

CREATE TABLE `subjects` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `teacher_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `teacher_id`) VALUES
(1, 'Matematika', 'Al Jabar, Teorema Petagoras, ', 1),
(2, 'Bahasa Indonesia', 'Belajar Menggunakan Bahasa indonesia yang benar', 2),
(3, 'Bahasa Inggris', 'Belajar Berbahasa Inggris Dengan Benar', 3),
(4, 'Agama', 'Tentang Pendidikan Agama Islam, Sejarah, Aqidah Akhlak, Alqur\'an dan Hadits', 5),
(5, 'PKN', 'Pembelajaran tentang hak dan kewajiban sebagai warga negara', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `teachers`
--

CREATE TABLE `teachers` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nip` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `alamat` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') NOT NULL,
  `profile_photo` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `nip`, `name`, `alamat`, `department`, `no_hp`, `tanggal_lahir`, `jenis_kelamin`, `profile_photo`) VALUES
(1, 4, 200502, 'RATNAWATY, S.Pd', 'BORONG-BORONG', 'Bahasa Indonesia', '083192312659', '1992-07-09', 'perempuan', 'hijab-icon-2.png'),
(2, 6, 205013, 'NINING KUSNADI, S.Pd', 'MAJANNANG', 'Hbash', '89327498', '2024-07-26', 'perempuan', 'hijab-icon-2.png'),
(3, 9, 2050017, 'SULASTRI ARDIYANTI, S.Pd', 'JL. A.L DG Manrapi Sabbang Paru', 'Bahasa Inggris', '859812741', '2024-07-01', 'perempuan', 'hijab-icon-2.png'),
(4, 10, 2050019, 'YUNIARTI, S.Pd, Gr', 'JL. COPPO TOMPONG ', 'PKN', '876123812', '2024-07-27', 'perempuan', 'hijab-icon-2.png'),
(5, 11, 2050033, 'TRI ABDUL WAHID, S.Ag', 'JL. PELELANGAN ', 'Agama', '857162782', '2024-07-17', 'laki-laki', 'User-Avatar-PNG-Free-Download.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int NOT NULL,
  `teacher_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(6, 4, 5),
(7, 5, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tugas`
--

CREATE TABLE `tugas` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `jelas` varchar(255) NOT NULL,
  `description` text,
  `due_date` date DEFAULT NULL,
  `teacher_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tugas`
--

INSERT INTO `tugas` (`id`, `title`, `jelas`, `description`, `due_date`, `teacher_id`, `subject_id`) VALUES
(1, 'Tugas Bhs indo', '', 'Kerjakan Halaman 50-52', '2024-08-09', 2, 2),
(2, 'Tugas Matematika', '', 'Kerjakan Halaman 16', '2024-08-09', 1, 1),
(3, 'Tugas Bahasa Inggris', '', 'Kerjakan Halaman 50-52', '2024-08-09', 3, 3),
(4, 'Tugas PKN', '', 'Kerjakan Halaman 50-52', '2024-08-09', 4, 5),
(5, 'Tugas Matematika', '', 'Kerjakan halaman 24', '2024-08-19', 1, 1),
(12, 'Tugas 2', '', 'CF196EAB80FA8B549BCE8DBCB65843DE6393088C_large.jpg', '2024-08-31', 2, 2),
(17, 'Tugas ea ea', 'Kerjakan Sesuai Instruksi', 'feed.png', '2024-08-29', 1, 1),
(18, 'Tugas Agama', 'Kerjakan', 'Dasar-dasar pendidikan agama Islam.pdf', '2024-09-01', 5, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('student','teacher') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'siswa1', '$2y$10$Z3expNnPRLuuaZNeA0SKSee.yZ8xwE6kH8HaQ/Z.jja5y97XLQXZm', 'student1@example.com', 'student', '2024-07-23 15:31:13'),
(3, 'siswa2', '$2y$10$REOYnMMwIigrrPZDPR53rOf0KvW.Rgs66b2jHfkbsaeyCz9Txs7Im', 'siswa2@Gmail.com', 'student', '2024-07-24 03:29:48'),
(4, 'guru1', '$2y$10$U2fdzoSnnzvg290Yx3fApubYg5zZJHZJEd.2zhTHBWroW4mdWweEG', 'guru@Gmail.com', 'teacher', '2024-07-24 03:34:41'),
(5, 'siswa3', '$2y$10$XYKm/H4kYABY4RcMGM08wuPH/KkxcgT3Tk9KuZm5pJvTeiQWH8YRa', 'nanda123@Gmail.com', 'student', '2024-07-24 09:05:40'),
(6, 'guru2', '$2y$10$WofzwMoEj711mzXRI7X/uOWFa1qTFb8puk1c7z32ff4FNZ6HtsNMu', 'bagus123@Gmail.com', 'teacher', '2024-07-24 09:10:05'),
(7, 'siswa4', '$2y$10$8g1Jt/p8ITa8TpAVZv6U9.GT33NFNxqsCqrs9TnPNepqzEF9V6jOC', 'denil123@Gmail.com', 'student', '2024-07-24 13:18:12'),
(8, 'siswa5', '$2y$10$M5xkWPQXyDlspd5XvU5gjewbEaqeYJD3zQ93aXFBOgV46wdyqrvSu', 'salman@gmail.com', 'student', '2024-07-24 13:18:28'),
(9, 'guru3', '$2y$10$V4atQUmjk12Pd0tQcl2SpuATLhFmUZucGfH.hmGPnpfpqVCPPCPS2', 'zaky1123@Gmail.com', 'teacher', '2024-07-25 04:53:02'),
(10, 'guru4', '$2y$10$QKukDdPXZW13pkf/16jLmOCuiUKtkKhfjJSMBf4B5ZSPKLjzBaD2a', 'liza@Gmail.com', 'teacher', '2024-07-25 04:53:24'),
(11, 'guru5', '$2y$10$xa4blEthuHw8LdUBXpNrj.gAVcoLRGd22gPRAsPZL758SgoaJe/7S', 'fali@gmail.com', 'teacher', '2024-07-25 04:53:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `agama`
--
ALTER TABLE `agama`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`),
  ADD KEY `fk_agama_subject` (`subject_id`);

--
-- Indeks untuk tabel `bahasa_indonesia`
--
ALTER TABLE `bahasa_indonesia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`),
  ADD KEY `fk_bahasa_indonesia_subject` (`subject_id`);

--
-- Indeks untuk tabel `bahasa_inggris`
--
ALTER TABLE `bahasa_inggris`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`),
  ADD KEY `fk_bahasa_inggris_subject` (`subject_id`);

--
-- Indeks untuk tabel `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_subject` (`student_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indeks untuk tabel `kumpul_tugas`
--
ALTER TABLE `kumpul_tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indeks untuk tabel `matematika`
--
ALTER TABLE `matematika`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`),
  ADD KEY `fk_matematika_subject` (`subject_id`);

--
-- Indeks untuk tabel `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswa_id` (`siswa_id`,`soal_id`),
  ADD KEY `nilai_siswa_soal_rekomendasi_fk` (`soal_id`),
  ADD KEY `rekomendasi_id` (`rekomendasi_id`);

--
-- Indeks untuk tabel `pkn`
--
ALTER TABLE `pkn`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`),
  ADD KEY `fk_pkn_subject` (`subject_id`);

--
-- Indeks untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indeks untuk tabel `soal_rekomendasi`
--
ALTER TABLE `soal_rekomendasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rekomendasi_id` (`rekomendasi_id`);

--
-- Indeks untuk tabel `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_subject` (`student_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indeks untuk tabel `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`);

--
-- Indeks untuk tabel `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indeks untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `fk_subject` (`subject_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `agama`
--
ALTER TABLE `agama`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `bahasa_indonesia`
--
ALTER TABLE `bahasa_indonesia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `bahasa_inggris`
--
ALTER TABLE `bahasa_inggris`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `kumpul_tugas`
--
ALTER TABLE `kumpul_tugas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `matematika`
--
ALTER TABLE `matematika`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT untuk tabel `pkn`
--
ALTER TABLE `pkn`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT untuk tabel `soal_rekomendasi`
--
ALTER TABLE `soal_rekomendasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT untuk tabel `students`
--
ALTER TABLE `students`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT untuk tabel `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `agama`
--
ALTER TABLE `agama`
  ADD CONSTRAINT `agama_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `fk_agama_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Ketidakleluasaan untuk tabel `bahasa_indonesia`
--
ALTER TABLE `bahasa_indonesia`
  ADD CONSTRAINT `bahasa_indonesia_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `fk_bahasa_indonesia_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Ketidakleluasaan untuk tabel `bahasa_inggris`
--
ALTER TABLE `bahasa_inggris`
  ADD CONSTRAINT `bahasa_inggris_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `fk_bahasa_inggris_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Ketidakleluasaan untuk tabel `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Ketidakleluasaan untuk tabel `kumpul_tugas`
--
ALTER TABLE `kumpul_tugas`
  ADD CONSTRAINT `kumpul_tugas_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tugas` (`id`);

--
-- Ketidakleluasaan untuk tabel `matematika`
--
ALTER TABLE `matematika`
  ADD CONSTRAINT `fk_matematika_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `matematika_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Ketidakleluasaan untuk tabel `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD CONSTRAINT `nilai_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_siswa_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal_rekomendasi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_siswa_ibfk_3` FOREIGN KEY (`rekomendasi_id`) REFERENCES `recommendations` (`id`),
  ADD CONSTRAINT `nilai_siswa_soal_rekomendasi_fk` FOREIGN KEY (`soal_id`) REFERENCES `soal_rekomendasi` (`id`);

--
-- Ketidakleluasaan untuk tabel `pkn`
--
ALTER TABLE `pkn`
  ADD CONSTRAINT `fk_pkn_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `pkn_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Ketidakleluasaan untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  ADD CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Ketidakleluasaan untuk tabel `soal_rekomendasi`
--
ALTER TABLE `soal_rekomendasi`
  ADD CONSTRAINT `soal_rekomendasi_ibfk_1` FOREIGN KEY (`rekomendasi_id`) REFERENCES `recommendations` (`id`);

--
-- Ketidakleluasaan untuk tabel `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD CONSTRAINT `student_subjects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Ketidakleluasaan untuk tabel `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Ketidakleluasaan untuk tabel `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Ketidakleluasaan untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `fk_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
