-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2026 at 11:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `talenta_florist`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`, `dibuat_pada`) VALUES
(1, 'Administrator', 'admin', '$2y$10$2jeTywo4vpLG7ynslfX2c.OBPtPmF6J4i55JM9Oh2nyub/3c8ZSZG', '2026-05-27 01:55:18');

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `nim` varchar(30) NOT NULL,
  `peran` varchar(100) DEFAULT 'Anggota',
  `foto` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `nama`, `nim`, `peran`, `foto`, `urutan`) VALUES
(1, 'Jeremy Jehuda Paskah Sompie', '240211060006', 'Anggota', NULL, 1),
(2, 'Delon Christiano Poluakan', '240211060020', 'Anggota', NULL, 2),
(3, 'Noveria Mumek', '240211060024', 'Anggota', NULL, 3),
(4, 'Kristian Aji Suseno', '240211060070', 'Anggota', NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `urutan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `slug`, `nama`, `urutan`) VALUES
(1, 'bucket', 'Bucket', 1),
(2, 'rangkaian', 'Rangkaian', 2),
(3, 'dekorasi', 'Dekorasi', 3),
(4, 'krans', 'Bunga Krans', 4);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `penerima` enum('admin','pelanggan') NOT NULL,
  `pelanggan_id` int(11) DEFAULT NULL,
  `pesan` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `dibaca` tinyint(1) NOT NULL DEFAULT 0,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `penerima`, `pelanggan_id`, `pesan`, `link`, `dibaca`, `dibuat_pada`) VALUES
(1, 'admin', NULL, '🌸 Pesanan baru dari kaka jajaja — Money Bouquet (Variasi 1) x3 | #5', 'admin/pesanan/index.php', 1, '2026-06-04 20:35:20'),
(2, 'pelanggan', 1, 'Pesanan #5 (Money Bouquet – Variasi 1) telah dikonfirmasi ✅.', 'riwayat_pesanan.php', 1, '2026-06-04 20:35:56'),
(3, 'admin', NULL, '🌸 Pesanan baru dari kaka jajaja — Rangkaian Full Mawar (Variasi 1) x1 | #6', 'admin/pesanan/index.php', 1, '2026-06-05 12:49:20'),
(4, 'pelanggan', 1, '✅ Pesanan #3 Anda telah dikonfirmasi oleh Talenta Florist!', 'riwayat_pesanan.php', 1, '2026-06-05 13:03:01'),
(5, 'pelanggan', 1, '✅ Pesanan #6 Anda telah dikonfirmasi oleh Talenta Florist!', 'riwayat_pesanan.php', 1, '2026-06-05 13:03:20'),
(6, 'admin', NULL, '🌸 Pesanan baru dari kaka jajaja — Rangkaian Campur Mawar (Mewah) (Variasi 1) x1 | #7', 'admin/pesanan/index.php', 1, '2026-06-05 13:10:18'),
(7, 'pelanggan', 1, '🎉 Pesanan #6 selesai. Terima kasih sudah memesan di Talenta Florist! 🌸', 'riwayat_pesanan.php', 1, '2026-06-05 13:11:54'),
(8, 'pelanggan', 1, '🎉 Pesanan #7 selesai. Terima kasih sudah memesan di Talenta Florist! 🌸', 'riwayat_pesanan.php', 1, '2026-06-05 13:14:34'),
(9, 'pelanggan', 1, '✅ Pesanan #7 Anda telah dikonfirmasi oleh Talenta Florist!', 'riwayat_pesanan.php', 1, '2026-06-05 13:15:25'),
(10, 'pelanggan', 1, '✅ Pesanan #7 Anda telah dikonfirmasi oleh Talenta Florist!', 'riwayat_pesanan.php', 1, '2026-06-05 13:16:31'),
(11, 'admin', NULL, '🌸 Pesanan baru dari kaka jajaja — Bunga Krans Duka (Variasi 1) x1 | #8', 'admin/pesanan/index.php', 1, '2026-06-05 13:35:17'),
(12, 'pelanggan', 1, '✅ Pesanan #8 Anda telah dikonfirmasi oleh Talenta Florist!', 'riwayat_pesanan.php', 1, '2026-06-05 13:35:54'),
(13, 'admin', NULL, '🌸 Pesanan baru dari kaka jajaja — Dekorasi Tempat (Variasi 1) x1 | #9', 'admin/pesanan/index.php', 1, '2026-06-05 16:41:53'),
(14, 'pelanggan', 1, '❌ Pesanan #9 dibatalkan. Hubungi kami jika ada pertanyaan.', 'riwayat_pesanan.php', 1, '2026-06-05 16:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `username`, `email`, `password`, `telepon`, `alamat`, `dibuat_pada`) VALUES
(1, 'kaka jajaja', 'kja', 'kja@gmail.com', '$2y$10$CnDaMO9QsZlRKPRK9rMxQOM1p1cOlyS8dfa5jPfiprwPBdEVIMjv6', '08123456789', 'jl tomohon', '2026-05-29 11:35:16'),
(2, 'usa', 'usa_123', 'usa@gmail.com', '$2y$10$xNImL0iUQoxz56MJhSv0hehSV9BMcaPFdJxvCHCoFsO5oAJbXYuVm', '081234567777', 'jl.viadolorosa', '2026-06-04 10:24:01'),
(3, 'aku', 'aku12', 'aku@gmail.com', '$2y$10$slSkYB3qjlJsst9n5Vh9fO69ncGUnkyHwKa1DPqrCReH2LdVd1W8.', '081234677899', 'jalan pemandangan', '2026-06-05 08:49:37');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `variasi_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `total_harga` int(11) NOT NULL DEFAULT 0,
  `nama_penerima` varchar(150) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('menunggu','dikonfirmasi','dikirim','selesai','dibatalkan') DEFAULT 'menunggu',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `pelanggan_id`, `produk_id`, `variasi_id`, `jumlah`, `total_harga`, `nama_penerima`, `telepon`, `alamat`, `catatan`, `status`, `dibuat_pada`) VALUES
(1, 1, 2, 3, 1, 150000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'selesai', '2026-05-31 12:13:25'),
(2, 1, 2, 3, 1, 150000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'selesai', '2026-05-31 12:15:01'),
(3, 1, 2, 3, 1, 150000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'dikonfirmasi', '2026-06-04 06:44:57'),
(4, 2, 10, 41, 4, 30000000, 'usa', '081234567777', 'jl.viadolorosa', 'tolong tambahkan kudanya', 'dibatalkan', '2026-06-04 10:33:23'),
(5, 1, 2, 3, 3, 450000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'dikonfirmasi', '2026-06-04 12:35:20'),
(6, 1, 8, 33, 1, 650000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'selesai', '2026-06-05 04:49:20'),
(7, 1, 7, 26, 1, 1000000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'dikonfirmasi', '2026-06-05 05:10:18'),
(8, 1, 12, 50, 1, 150000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'dikonfirmasi', '2026-06-05 05:35:17'),
(9, 1, 9, 35, 1, 2500000, 'kaka jajaja', '08123456789', 'jl tomohon', '', 'dibatalkan', '2026-06-05 08:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `badge` varchar(60) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `kategori_id`, `nama`, `deskripsi`, `badge`, `aktif`, `dibuat_pada`) VALUES
(1, 1, 'Bouquet Boneka', 'Bucket bunga dengan boneka, cocok untuk hadiah spesial.', 'Custom', 1, '2026-05-27 01:55:18'),
(2, 1, 'Money Bouquet', 'Bucket yang memadukan keindahan bunga dengan lembaran uang kertas yang disusun artistik, menciptakan hadiah istimewa yang berkesan dan fungsional.', 'Custom', 1, '2026-05-27 01:55:18'),
(3, 1, 'Bouquet Mawar', 'Buket mawar premium disusun dengan komposisi padat dan simetris, memancarkan kemewahan klasik yang sangat cocok untuk momen perayaan spesial.', 'Custom', 1, '2026-05-27 01:55:18'),
(4, 1, 'Bucket Krisan Campur', 'Kombinasi harmonis berbagai jenis krisan dan bunga musiman yang disusun rimbun untuk menciptakan kesan segar dan ceria.', 'Custom', 1, '2026-05-27 01:55:18'),
(5, 2, 'Rangkaian Meja Full Krisan', 'Rangkaian bunga krisan yang disusun padat dengan hiasan aster kecil, memancarkan kesan anggun, harmonis, dan penuh ketulusan.', 'Custom', 1, '2026-05-27 01:55:18'),
(6, 2, 'Rangkaian Campur Mawar', 'Rangkaian bunga yang memadukan krisan dan mawar dengan bentuk oval dan bulat, sangat ideal diletakkan di tengah meja panjang atau meja tamu.', 'Custom', 1, '2026-05-27 01:55:18'),
(7, 2, 'Rangkaian Campur Mawar (Mewah)', 'Rangkaian bunga yang memadukan mawar dengan bunga mewah lain seperti bunga terompet, casablanca, dan bunga lainnya yang memancarkan kesan mewah.', 'Custom', 1, '2026-05-27 01:55:18'),
(8, 2, 'Rangkaian Full Mawar', 'Rangkaian full mawar dengan bentuk bulat dan oval yang cocok ditaruh di meja.', 'Custom', 1, '2026-05-27 01:55:18'),
(9, 3, 'Dekorasi Tempat', 'Rangkaian bunga dekoratif bergaya mini garden yang dirancang memanjang untuk memberikan batas visual yang estetis dan segar pada area panggung atau podium.', 'Custom', 1, '2026-05-27 01:55:18'),
(10, 3, 'Dekorasi Kendaraan', 'Rangkaian bunga fresh premium yang dirancang khusus untuk mempercantik kendaraan.', 'Custom', 1, '2026-05-27 01:55:18'),
(11, 4, 'Bunga Papan Suka dan Duka', 'Rangkaian bunga papan yang dirancang sebagai bentuk penghormatan dan penyampaian pesan dari hati untuk momen-momen penting.', 'Custom', 1, '2026-05-27 01:55:18'),
(12, 4, 'Bunga Krans Duka', 'Rangkaian bunga melingkar yang dirancang khusus sebagai simbol penghormatan terakhir dan ungkapan simpati yang mendalam.', 'Custom', 1, '2026-05-27 01:55:18');

-- --------------------------------------------------------

--
-- Table structure for table `testimoni`
--

CREATE TABLE `testimoni` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `aktif` tinyint(1) DEFAULT 1,
  `pelanggan_id` int(11) DEFAULT NULL,
  `pesanan_id` int(11) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimoni`
--

INSERT INTO `testimoni` (`id`, `nama`, `pesan`, `rating`, `aktif`, `pelanggan_id`, `pesanan_id`, `dibuat_pada`) VALUES
(1, 'Maria T.', 'Rangkaian bunganya sangat cantik dan tahan lama! Pengiriman tepat waktu untuk hari pernikahan kami.', 5, 1, NULL, NULL, '2026-05-27 01:55:18'),
(2, 'Reza A.', 'Pesan untuk wisuda adik, hasilnya melampaui ekspektasi. Highly recommended!', 5, 1, NULL, NULL, '2026-05-27 01:55:18'),
(3, 'Sandra K.', 'Customer service responsif, bunga fresh, dan harga terjangkau. Akan pesan lagi!', 5, 1, NULL, NULL, '2026-05-27 01:55:18'),
(4, 'kaka jajaja', 'Bunga nya bagus banget', 5, 1, NULL, NULL, '2026-05-30 02:18:48'),
(5, 'kaka jajaja', 'waww', 5, 1, NULL, NULL, '2026-05-30 02:50:56'),
(9, 'kaka jajaja', 'kakaka', 5, 1, 1, 1, '2026-06-04 08:49:22');

-- --------------------------------------------------------

--
-- Table structure for table `variasi`
--

CREATE TABLE `variasi` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `variasi`
--

INSERT INTO `variasi` (`id`, `produk_id`, `nama`, `harga`, `gambar`, `urutan`) VALUES
(1, 1, 'Variasi 1', 175000, 'image/Bucket01.png', 1),
(2, 1, 'Variasi 2', 225000, 'image/Bucket02.png', 2),
(3, 2, 'Variasi 1', 150000, 'image/Bucket03.png', 1),
(4, 3, 'Variasi 1', 500000, 'image/Bucket04.png', 1),
(5, 3, 'Variasi 2', 400000, 'image/Bucket05.png', 2),
(6, 3, 'Variasi 3', 450000, 'image/Bucket06.png', 3),
(7, 3, 'Variasi 4', 400000, 'image/Bucket07.png', 4),
(8, 3, 'Variasi 5', 500000, 'image/Bucket08.png', 5),
(9, 4, 'Variasi 1', 350000, 'image/Bucket09.png', 1),
(10, 4, 'Variasi 2', 250000, 'image/Bucket10.png', 2),
(11, 4, 'Variasi 3', 300000, 'image/Bucket11.png', 3),
(12, 4, 'Variasi 4', 300000, 'image/Bucket12.png', 4),
(13, 4, 'Variasi 5', 150000, 'image/Bucket13.png', 5),
(14, 4, 'Variasi 6', 225000, 'image/Bucket14.png', 6),
(15, 5, 'Variasi 1', 30000, 'image/Rangkaian03.png', 1),
(16, 5, 'Variasi 2', 50000, 'image/Rangkaian09.png', 2),
(17, 5, 'Variasi 3', 150000, 'image/Rangkaian06.png', 3),
(18, 5, 'Variasi 4', 200000, 'image/Rangkaian05.png', 4),
(19, 5, 'Variasi 5', 300000, 'image/Rangkaian01.png', 5),
(20, 6, 'Variasi 1', 125000, 'image/Rangkaian08.png', 1),
(21, 6, 'Variasi 2', 250000, 'image/Rangkaian07.jpeg', 2),
(22, 6, 'Variasi 3', 750000, 'image/Rangkaian04.png', 3),
(23, 6, 'Variasi 4', 350000, 'image/Rangkaian13.png', 4),
(24, 6, 'Variasi 5', 350000, 'image/Rangkaian14.png', 5),
(25, 6, 'Variasi 6', 700000, 'image/Rangkaian20.png', 6),
(26, 7, 'Variasi 1', 1000000, 'image/Rangkaian11.png', 1),
(27, 7, 'Variasi 2', 1000000, 'image/Rangkaian12.png', 2),
(28, 7, 'Variasi 3', 1000000, 'image/Rangkaian15.png', 3),
(29, 7, 'Variasi 4', 750000, 'image/Rangkaian16.png', 4),
(30, 7, 'Variasi 5', 200000, 'image/Rangkaian17.png', 5),
(31, 7, 'Variasi 6', 1250000, 'image/Rangkaian18.png', 6),
(32, 7, 'Variasi 7', 1250000, 'image/Rangkaian19.png', 7),
(33, 8, 'Variasi 1', 650000, 'image/Rangkaian10.png', 1),
(34, 8, 'Variasi 2', 650000, 'image/Rangkaian02.png', 2),
(35, 9, 'Variasi 1', 2500000, 'image/Deckor01.jpeg', 1),
(36, 9, 'Variasi 2', 3000000, 'image/Deckor02.jpeg', 2),
(37, 9, 'Variasi 3', 5000000, 'image/Deckor03.jpeg', 3),
(38, 9, 'Variasi 4', 7500000, 'image/Deckor07.jpeg', 4),
(39, 9, 'Variasi 5', 3500000, 'image/Deckor05.jpeg', 5),
(40, 9, 'Variasi 6', 7500000, 'image/Deckor06.jpeg', 6),
(41, 10, 'Variasi 1', 7500000, 'image/Deckor04.jpeg', 1),
(42, 11, 'Variasi 1', 500000, 'image/Papan01.jpeg', 1),
(43, 11, 'Variasi 2', 1000000, 'image/Papan04.jpeg', 2),
(44, 11, 'Variasi 3', 750000, 'image/Papan08.jpeg', 3),
(45, 11, 'Variasi 4', 1000000, 'image/Papan05.jpeg', 4),
(46, 11, 'Variasi 5', 1750000, 'image/Papan06.jpeg', 5),
(47, 11, 'Variasi 6', 750000, 'image/Papan07.jpeg', 6),
(48, 11, 'Variasi 7', 3500000, 'image/Papan02.jpeg', 7),
(49, 11, 'Variasi 8', 3000000, 'image/Papan09.jpeg', 8),
(50, 12, 'Variasi 1', 150000, 'image/Papan12.jpeg', 1),
(51, 12, 'Variasi 2', 250000, 'image/Papan10.jpeg', 2),
(52, 12, 'Variasi 3', 600000, 'image/Papan11.jpeg', 3),
(53, 12, 'Variasi 4', 500000, 'image/Papan13.jpeg', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_belum_baca` (`penerima`,`dibaca`),
  ADD KEY `idx_pelanggan` (`pelanggan_id`,`dibaca`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggan_id` (`pelanggan_id`),
  ADD KEY `produk_id` (`produk_id`),
  ADD KEY `variasi_id` (`variasi_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `testimoni`
--
ALTER TABLE `testimoni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_testimoni_pesanan` (`pesanan_id`);

--
-- Indexes for table `variasi`
--
ALTER TABLE `variasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `testimoni`
--
ALTER TABLE `testimoni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `variasi`
--
ALTER TABLE `variasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_3` FOREIGN KEY (`variasi_id`) REFERENCES `variasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `testimoni`
--
ALTER TABLE `testimoni`
  ADD CONSTRAINT `fk_testimoni_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `variasi`
--
ALTER TABLE `variasi`
  ADD CONSTRAINT `variasi_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
