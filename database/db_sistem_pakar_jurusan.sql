-- ============================================
-- Database: db_sistem_pakar_jurusan
-- Sistem Pakar Penentuan Jurusan
-- Metode Forward Chaining
-- ============================================

CREATE DATABASE IF NOT EXISTS db_sistem_pakar_jurusan;
USE db_sistem_pakar_jurusan;

-- ============================================
-- Tabel Users
-- ============================================
DROP TABLE IF EXISTS detail_konsultasi;
DROP TABLE IF EXISTS hasil_penentuan;
DROP TABLE IF EXISTS konsultasi;
DROP TABLE IF EXISTS rule_jurusan;
DROP TABLE IF EXISTS atribut;
DROP TABLE IF EXISTS jurusan;
DROP TABLE IF EXISTS siswa;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    level ENUM('admin','siswa') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Tabel Siswa
-- ============================================
CREATE TABLE siswa (
    nisn VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Tabel Jurusan
-- ============================================
CREATE TABLE jurusan (
    id_jurusan INT AUTO_INCREMENT PRIMARY KEY,
    nama_jurusan VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ============================================
-- Tabel Atribut
-- ============================================
CREATE TABLE atribut (
    id_atribut INT AUTO_INCREMENT PRIMARY KEY,
    kategori VARCHAR(50) NOT NULL,
    nama_atribut VARCHAR(200) NOT NULL
) ENGINE=InnoDB;

-- ============================================
-- Tabel Rule Jurusan
-- ============================================
CREATE TABLE rule_jurusan (
    id_rule INT AUTO_INCREMENT PRIMARY KEY,
    id_jurusan INT NOT NULL,
    id_atribut INT NOT NULL,
    FOREIGN KEY (id_jurusan) REFERENCES jurusan(id_jurusan) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_atribut) REFERENCES atribut(id_atribut) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Tabel Konsultasi
-- ============================================
CREATE TABLE konsultasi (
    id_konsultasi INT AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20) NOT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nisn) REFERENCES siswa(nisn) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Tabel Detail Konsultasi
-- ============================================
CREATE TABLE detail_konsultasi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_konsultasi INT NOT NULL,
    id_atribut INT NOT NULL,
    jawaban VARCHAR(200) NOT NULL,
    FOREIGN KEY (id_konsultasi) REFERENCES konsultasi(id_konsultasi) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_atribut) REFERENCES atribut(id_atribut) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Tabel Hasil Penentuan
-- ============================================
CREATE TABLE hasil_penentuan (
    id_hasil INT AUTO_INCREMENT PRIMARY KEY,
    id_konsultasi INT NOT NULL,
    id_jurusan INT NOT NULL,
    persentase DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_konsultasi) REFERENCES konsultasi(id_konsultasi) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_jurusan) REFERENCES jurusan(id_jurusan) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- SEED DATA
-- ============================================

-- Admin User (password: admin123)
INSERT INTO users (username, password, level) VALUES 
('admin', MD5('admin123'), 'admin');

-- ============================================
-- Data Jurusan
-- ============================================
INSERT INTO jurusan (id_jurusan, nama_jurusan) VALUES
(1, 'Teknik Komputer Jaringan (TKJ)'),
(2, 'Teknik Instalasi Tenaga Listrik (TITL)'),
(3, 'Teknik Bisnis Sepeda Motor (TBSM)'),
(4, 'Teknik Kendaraan Ringan (TKR)'),
(5, 'Multimedia (MM)'),
(6, 'Otomatisasi Tata Kelola Perkantoran (OTKP)'),
(7, 'Akuntansi Keuangan Lembaga (AKL)');

-- ============================================
-- Data Atribut
-- ============================================

-- Kategori: Cita-cita (ID 1-7)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(1,  'Cita-cita', 'Programmer'),
(2,  'Cita-cita', 'Ahli Listrik'),
(3,  'Cita-cita', 'Pembuat Sepeda Motor'),
(4,  'Cita-cita', 'Montir'),
(5,  'Cita-cita', 'Designer'),
(6,  'Cita-cita', 'Staff Administrasi'),
(7,  'Cita-cita', 'Akuntan');

-- Kategori: Minat (ID 8-14)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(8,  'Minat', 'Bermain Komputer'),
(9,  'Minat', 'Perbaikan Listrik'),
(10, 'Minat', 'Modifikasi Sepeda Motor'),
(11, 'Minat', 'Perbaikan Mesin'),
(12, 'Minat', 'Menggambar/Melukis'),
(13, 'Minat', 'Membuat Surat Menyurat'),
(14, 'Minat', 'Menghitung Anggaran Harian');

-- Kategori: Kesukaan (ID 15-21)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(15, 'Kesukaan', 'Penggunaan Komputer'),
(16, 'Kesukaan', 'Memasang Rangkaian Listrik'),
(17, 'Kesukaan', 'Membuat Sepeda Motor'),
(18, 'Kesukaan', 'Struktur Rangkaian Mesin'),
(19, 'Kesukaan', 'Membuat Desain Gambar Video dan Animasi'),
(20, 'Kesukaan', 'Persuratan'),
(21, 'Kesukaan', 'Laba dan Neraca Keuangan');

-- Kategori: Keingintahuan (ID 22-28)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(22, 'Keingintahuan', 'Bidang Komputer'),
(23, 'Keingintahuan', 'Pembangunan'),
(24, 'Keingintahuan', 'Inovasi Sepeda Motor Yang Terus Berkembang'),
(25, 'Keingintahuan', 'Kendaraan Sebuah Kebutuhan Primer'),
(26, 'Keingintahuan', 'Bidang Designer'),
(27, 'Keingintahuan', 'Bidang Perkantoran'),
(28, 'Keingintahuan', 'Bidang Akuntansi');

-- Kategori: Kebutuhan (ID 29-35)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(29, 'Kebutuhan', 'Komputer Banyak Digunakan Diberbagai Bidang'),
(30, 'Kebutuhan', 'Pembangunan Terus Berkembang'),
(31, 'Kebutuhan', 'Sepeda Motor Banyak Digunakan Diberbagai Bidang'),
(32, 'Kebutuhan', 'Penggunaan Kendaraan Sebagai Rutinitas'),
(33, 'Kebutuhan', 'Designer Banyak Digunakan Untuk Membuat Apapun Yang Terlihat Modern'),
(34, 'Kebutuhan', 'Otomatisasi Banyak Digunakan Di Perkantoran'),
(35, 'Kebutuhan', 'Keuangan Banyak Digunakan Di Berbagai Bidang');

-- Kategori: Pengetahuan (ID 36-42)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(36, 'Pengetahuan', 'Mengerti Dasar Komputer'),
(37, 'Pengetahuan', 'Mengerti Dasar Listrik'),
(38, 'Pengetahuan', 'Mengerti Dasar Sepeda Motor'),
(39, 'Pengetahuan', 'Mengerti Dasar Mesin'),
(40, 'Pengetahuan', 'Mengerti Dasar Designer'),
(41, 'Pengetahuan', 'Mengerti Dasar Persuratan'),
(42, 'Pengetahuan', 'Mengerti Dasar Keuangan');

-- Kategori: Mudah Memahami (ID 43-49)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(43, 'Mudah Memahami', 'Mengerti Dalam Memecahkan Masalah Terkait Komputer'),
(44, 'Mudah Memahami', 'Mengerti Dalam Memecahkan Masalah Terkait Listrik'),
(45, 'Mudah Memahami', 'Mengerti Dalam Memecahkan Masalah Terkait Sepeda Motor'),
(46, 'Mudah Memahami', 'Mengerti Dalam Memecahkan Masalah Terkait Mesin'),
(47, 'Mudah Memahami', 'Mengerti Dalam Memecahkan Masalah Terkait Designer'),
(48, 'Mudah Memahami', 'Mengerti Dalam Bahasa Persuratan'),
(49, 'Mudah Memahami', 'Mengerti Dalam Memecahkan Masalah Terkait Keuangan');

-- Kategori: Mampu Mengenal Masalah (ID 50-56)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(50, 'Mampu Mengenal Masalah', 'Mengerti Dasar Kerusakan Komputer'),
(51, 'Mampu Mengenal Masalah', 'Mengerti Dasar Kerusakan Listrik'),
(52, 'Mampu Mengenal Masalah', 'Mengerti Dasar Pembuatan Sepeda Motor'),
(53, 'Mampu Mengenal Masalah', 'Mengerti Dasar Kerusakan Mesin'),
(54, 'Mampu Mengenal Masalah', 'Mengerti Dasar Pembuatan Designer'),
(55, 'Mampu Mengenal Masalah', 'Mengerti Dasar Pembuatan Persuratan'),
(56, 'Mampu Mengenal Masalah', 'Mengerti Dasar Perhitungan Laba dan Neraca');

-- Kategori: Kreatif (ID 57-63)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(57, 'Kreatif', 'Berkreasi Menggunakan Komputer'),
(58, 'Kreatif', 'Berkreasi Membuat Rangkaian Listrik'),
(59, 'Kreatif', 'Berkreasi Membuat/Memodifikasi Sepeda Motor'),
(60, 'Kreatif', 'Berkreasi Dalam Perbaikan Mesin'),
(61, 'Kreatif', 'Berkreasi Menggunakan Software Designer'),
(62, 'Kreatif', 'Berkreasi Terhadap Bahasa Persuratan'),
(63, 'Kreatif', 'Berkreasi Terhadap Perhitungan Laba dan Neraca');

-- Kategori: Inovatif (ID 64-70)
INSERT INTO atribut (id_atribut, kategori, nama_atribut) VALUES
(64, 'Inovatif', 'Mampu Menggunakan Beberapa Aplikasi Komputer'),
(65, 'Inovatif', 'Mampu Menggunakan Beberapa Peralatan Listrik'),
(66, 'Inovatif', 'Mampu Memodifikasi Sepeda Motor'),
(67, 'Inovatif', 'Mampu Menggunakan Beberapa Peralatan Mesin'),
(68, 'Inovatif', 'Mampu Menggambar Design Baru'),
(69, 'Inovatif', 'Mampu Membuat Persuratan Dengan Lebih Baik'),
(70, 'Inovatif', 'Mampu Menggunakan Beberapa Perhitungan Akuntansi');

-- ============================================
-- Data Rule Jurusan
-- ============================================

-- TKJ (id_jurusan=1): atribut 1,8,15,22,29,36,43,50,57,64
INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES
(1, 1), (1, 8), (1, 15), (1, 22), (1, 29),
(1, 36), (1, 43), (1, 50), (1, 57), (1, 64);

-- TITL (id_jurusan=2): atribut 2,9,16,23,30,37,44,51,58,65
INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES
(2, 2), (2, 9), (2, 16), (2, 23), (2, 30),
(2, 37), (2, 44), (2, 51), (2, 58), (2, 65);

-- TBSM (id_jurusan=3): atribut 3,10,17,24,31,38,45,52,59,66
INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES
(3, 3), (3, 10), (3, 17), (3, 24), (3, 31),
(3, 38), (3, 45), (3, 52), (3, 59), (3, 66);

-- TKR (id_jurusan=4): atribut 4,11,18,25,32,39,46,53,60,67
INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES
(4, 4), (4, 11), (4, 18), (4, 25), (4, 32),
(4, 39), (4, 46), (4, 53), (4, 60), (4, 67);

-- MM (id_jurusan=5): atribut 5,12,19,26,33,40,47,54,61,68
INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES
(5, 5), (5, 12), (5, 19), (5, 26), (5, 33),
(5, 40), (5, 47), (5, 54), (5, 61), (5, 68);

-- OTKP (id_jurusan=6): atribut 6,13,20,27,34,41,48,55,62,69
INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES
(6, 6), (6, 13), (6, 20), (6, 27), (6, 34),
(6, 41), (6, 48), (6, 55), (6, 62), (6, 69);

-- AKL (id_jurusan=7): atribut 7,14,21,28,35,42,49,56,63,70
INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES
(7, 7), (7, 14), (7, 21), (7, 28), (7, 35),
(7, 42), (7, 49), (7, 56), (7, 63), (7, 70);
