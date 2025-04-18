-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 17, 2025 at 04:29 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
-- /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
-- /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
-- /*!40101 SET NAMES utf8mb4 */;

--
-- Database: `b8_38488238_rizki`
--

-- --------------------------------------------------------

--
-- Table structure for table `autentifikasi`
--

CREATE TABLE IF NOT EXISTS `autentifikasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE IF NOT EXISTS `jurusan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE IF NOT EXISTS `log_login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_studi`
--

CREATE TABLE IF NOT EXISTS `program_studi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `jurusan_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`),
  KEY `jurusan_id` (`jurusan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pengajuan_kp`
--

CREATE TABLE IF NOT EXISTS `riwayat_pengajuan_kp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `path_file` varchar(255) DEFAULT NULL,
  `tipe_file` varchar(50) DEFAULT NULL,
  `status_verifikasi` enum('disetujui','ditolak') NOT NULL,
  `tanggal_verifikasi` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `surat_pengajuan_kp`
--

CREATE TABLE IF NOT EXISTS `surat_pengajuan_kp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tipe_file` varchar(50) NOT NULL,
  `status_verifikasi` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor` varchar(50) NOT NULL,
  `jenis` enum('mahasiswa','dosen','pegawai','admin') NOT NULL,
  `nama` varchar(255) NOT NULL,
  `program_studi_id` int DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor` (`nomor`),
  UNIQUE KEY `email` (`email`),
  KEY `program_studi_id` (`program_studi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `autentifikasi`
--
ALTER TABLE `autentifikasi`
  ADD CONSTRAINT `autentifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD CONSTRAINT `program_studi_ibfk_1` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_pengajuan_kp`
--
ALTER TABLE `riwayat_pengajuan_kp`
  ADD CONSTRAINT `riwayat_pengajuan_kp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `surat_pengajuan_kp`
--
ALTER TABLE `surat_pengajuan_kp`
  ADD CONSTRAINT `surat_pengajuan_kp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`program_studi_id`) REFERENCES `program_studi` (`id`) ON DELETE SET NULL;

COMMIT;

-- /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
-- /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
-- /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
