-- ========================================================
-- Schema Database: meta_sarpras
-- Berdasarkan: MVC_Pengaduan&Peminjaman Barang Kampus.xlsx
-- ========================================================

CREATE DATABASE IF NOT EXISTS `meta_sarpras`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `meta_sarpras`;

-- --------------------------------------------------------
-- 1. Tabel: admin
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_admin` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel: mahasiswa
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mahasiswa` (
  `id_mahasiswa` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_mahasiswa` VARCHAR(100) NOT NULL,
  `nim` VARCHAR(20) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel: pengaduan
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pengaduan` (
  `id_pengaduan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_mahasiswa` INT NOT NULL,
  `id_admin` INT NULL,
  `deskripsi_pengaduan` TEXT NOT NULL,
  `foto_pengaduan` VARCHAR(255) NULL,
  `tanggal` DATE NOT NULL,
  `status_pengaduan` VARCHAR(50) NOT NULL DEFAULT 'Menunggu',
  CONSTRAINT `fk_pengaduan_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE,
  CONSTRAINT `fk_pengaduan_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Tabel: peminjaman
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `peminjaman` (
  `id_peminjaman` INT AUTO_INCREMENT PRIMARY KEY,
  `id_mahasiswa` INT NOT NULL,
  `id_admin` INT NULL,
  `nama_peminjaman` VARCHAR(150) NOT NULL,
  `foto_fisik_barang` VARCHAR(255) NULL,
  `tgl_peminjaman` DATE NOT NULL,
  `batas_kembali` DATE NOT NULL,
  `tgl_pengembalian_aktual` DATE NULL,
  `status_peminjaman` VARCHAR(50) NOT NULL DEFAULT 'Menunggu Konfirmasi',
  `kondisi_peminjaman` VARCHAR(100) NULL DEFAULT 'Baik',
  `keterangan` TEXT NULL,
  `denda` INT NOT NULL DEFAULT 0,
  `keterangan_denda` VARCHAR(255) NULL DEFAULT '-',
  `jumlah` INT NOT NULL DEFAULT 1,
  `catatan_admin` TEXT NULL,
  CONSTRAINT `fk_peminjaman_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE,
  CONSTRAINT `fk_peminjaman_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Tabel: tanggapan
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tanggapan` (
  `id_tanggapan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_admin` INT NOT NULL,
  `id_pengaduan` INT NULL,
  `id_peminjaman` INT NULL,
  `isi_tanggapan` TEXT NOT NULL,
  `tanggal_tanggapan` DATE NOT NULL,
  CONSTRAINT `fk_tanggapan_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  CONSTRAINT `fk_tanggapan_pengaduan` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE,
  CONSTRAINT `fk_tanggapan_peminjaman` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Data Awal / Contoh (Seeder)
-- Password Admin: zaki123
-- Password Mahasiswa: mahasiswa
-- --------------------------------------------------------
INSERT IGNORE INTO `admin` (`id_admin`, `username`, `password`, `nama_admin`) VALUES
(1, 'zakiajja', '$2y$12$vDvnVfMticpvKDollPFZru/3CsNwf4WS8BfgeXZXGZCG/Kh87VCY.', 'Zaki');

INSERT IGNORE INTO `mahasiswa` (`id_mahasiswa`, `nama_mahasiswa`, `nim`, `password`) VALUES
(1, 'Yazid', '2425010', '$2y$12$QasRAG5Y9rjbcLTZQsKeAOQO/NpGgH7Y9c7Wtjljvn5p6sXx9W/OW');

INSERT IGNORE INTO `pengaduan` (`id_pengaduan`, `id_mahasiswa`, `id_admin`, `deskripsi_pengaduan`, `foto_pengaduan`, `tanggal`, `status_pengaduan`) VALUES
(1, 1, 1, 'Spidol tidak nyata', NULL, CURDATE(), 'Sedang diproses');

INSERT IGNORE INTO `peminjaman` (`id_peminjaman`, `id_mahasiswa`, `id_admin`, `nama_peminjaman`, `foto_fisik_barang`, `tgl_peminjaman`, `batas_kembali`, `tgl_pengembalian_aktual`, `status_peminjaman`, `kondisi_peminjaman`, `keterangan`, `denda`, `keterangan_denda`, `jumlah`, `catatan_admin`) VALUES
(1, 1, 1, 'Tv', NULL, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Telah Dikembalikan', 'Baik', 'Ingin Meminjam Tv di Labkom lt2 karena dibutuhkan untuk presentasi projek akhir', 0, '-', 1, '-');

INSERT IGNORE INTO `tanggapan` (`id_tanggapan`, `id_admin`, `id_pengaduan`, `id_peminjaman`, `isi_tanggapan`, `tanggal_tanggapan`) VALUES
(1, 1, 1, 1, 'Spidol sedang di refill', CURDATE());
