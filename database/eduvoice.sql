-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 13, 2025 at 08:26 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eduvoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `jawabans`
--

CREATE TABLE `jawabans` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pertanyaan_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondens_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teks` text COLLATE utf8mb4_unicode_ci,
  `opsi_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kuesioners`
--

CREATE TABLE `kuesioners` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(3, '2025_09_06_072558_create_periodes_table', 1),
(7, '0001_01_01_000000_create_users_table', 2),
(8, '2025_09_06_013513_create_respondens_table', 2),
(9, '2025_09_06_072558_create_kuesioners_table', 2),
(10, '2025_09_06_074720_create_pertanyaans_table', 2),
(11, '2025_09_06_074951_create_opsi_jawabans_table', 2),
(12, '2025_09_06_074952_create_jawabans_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `opsi_jawabans`
--

CREATE TABLE `opsi_jawabans` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pertanyaan_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teks` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skor` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pertanyaans`
--

CREATE TABLE `pertanyaans` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kuesioner_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('text','radio','chackbox','date','number') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `unsur` enum('Persyaratan Pelayanan','Prosedur Pelayanan','Kecepatan Pelayanan','Kewajaran Biaya Pelayanan','Kesesuaian Produk Layanan','Kemampuan Petugas Pelayanan','Kesopanan Petugas Layanan','Kualitas Sarana Prasarana','Penanganan Pengaduan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respondens`
--

CREATE TABLE `respondens` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jk` enum('Laki-Laki','Perempuan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Laki-Laki',
  `umur` tinyint UNSIGNED NOT NULL,
  `pekerjaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saran` text COLLATE utf8mb4_unicode_ci,
  `kritik` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','kepala','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 'admin@example.com', '$2y$12$g8wm0JLIx/baFR91/WbnEedSHfuBF4YEWv2g52G1pi/F2POEmccpG', 'admin', '2025-09-09 17:23:56', '2025-09-09 17:23:56'),
(2, 'Kepala Dinas', 'kepala', 'kepala@example.com', '$2y$12$lOOLemzH4b99zgHi1QUE/.22LvS4xSFisHBXgHO1eJotnCADJNl/O', 'kepala', '2025-09-09 17:23:56', '2025-09-09 17:23:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jawabans`
--
ALTER TABLE `jawabans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jawabans_pertanyaan_id_foreign` (`pertanyaan_id`),
  ADD KEY `jawabans_respondens_id_foreign` (`respondens_id`),
  ADD KEY `jawabans_opsi_id_foreign` (`opsi_id`);

--
-- Indexes for table `kuesioners`
--
ALTER TABLE `kuesioners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opsi_jawabans`
--
ALTER TABLE `opsi_jawabans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opsi_jawabans_pertanyaan_id_foreign` (`pertanyaan_id`);

--
-- Indexes for table `pertanyaans`
--
ALTER TABLE `pertanyaans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pertanyaans_kuesioner_id_foreign` (`kuesioner_id`);

--
-- Indexes for table `respondens`
--
ALTER TABLE `respondens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jawabans`
--
ALTER TABLE `jawabans`
  ADD CONSTRAINT `jawabans_opsi_id_foreign` FOREIGN KEY (`opsi_id`) REFERENCES `opsi_jawabans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jawabans_pertanyaan_id_foreign` FOREIGN KEY (`pertanyaan_id`) REFERENCES `pertanyaans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jawabans_respondens_id_foreign` FOREIGN KEY (`respondens_id`) REFERENCES `respondens` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opsi_jawabans`
--
ALTER TABLE `opsi_jawabans`
  ADD CONSTRAINT `opsi_jawabans_pertanyaan_id_foreign` FOREIGN KEY (`pertanyaan_id`) REFERENCES `pertanyaans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pertanyaans`
--
ALTER TABLE `pertanyaans`
  ADD CONSTRAINT `pertanyaans_kuesioner_id_foreign` FOREIGN KEY (`kuesioner_id`) REFERENCES `kuesioners` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
