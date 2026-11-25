-- ========================================================
-- Migrasi Tambah Kolom Foto Profil pada Tabel user
-- Jalankan pada database meta_sarpras yang sudah ada
-- agar data pengaduan/peminjaman lama tetap aman.
-- ========================================================

USE `meta_sarpras`;

ALTER TABLE `user`
  ADD COLUMN IF NOT EXISTS `foto` VARCHAR(255) NULL DEFAULT NULL AFTER `nim`;
