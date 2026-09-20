-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2026 at 06:11 PM
-- Server version: 12.3.3-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moneydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `base_group_member`
--

CREATE TABLE `base_group_member` (
  `id` int(11) NOT NULL,
  `id_mem` int(11) NOT NULL,
  `id_name_group` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `base_group_member`
--

INSERT INTO `base_group_member` (`id`, `id_mem`, `id_name_group`) VALUES
(1, 4, 1),
(2, 5, 1),
(3, 6, 1),
(4, 7, 2),
(5, 8, 2),
(6, 2, 1),
(7, 3, 1),
(9, 11, 2),
(10, 2, 3),
(11, 11, 3),
(12, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `id_group_admin` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `is_success` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `details`, `id_group_admin`, `is_deleted`, `is_success`) VALUES
(1, 'เก็บเงินค่าเสื้อเอก ปี 2', 'ชำระค่าเสื้อเอกคนละ 3000 บาท', 1, 0, 0),
(2, 'โครงการจิตอาสาปลูกป่า', 'ค่าลงทะเบียนเดินทางและอาหาร 200 บาท', 1, 0, 0),
(3, 'เก็บเงินห้อง สัปดาห์ 18', '', 1, 0, 1),
(4, 'test', '', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `group_admins`
--

CREATE TABLE `group_admins` (
  `id` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_group` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_admins`
--

INSERT INTO `group_admins` (`id`, `id_admin`, `id_group`) VALUES
(1, 1, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `group_mem`
--

CREATE TABLE `group_mem` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `details` varchar(255) NOT NULL,
  `id_group_admin` int(11) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_mem`
--

INSERT INTO `group_mem` (`id`, `title`, `details`, `id_group_admin`, `is_deleted`) VALUES
(1, 'ป.ตรี 4 ปี ปี 2', 'กลุ่มนักศึกษาชั้นปีที่ 2', 1, 0),
(2, 'ทีมจิตอาสา A', 'กลุ่มลงทะเบียนทีมงานจิตอาสา', 1, 0),
(3, 'ปี 5', 'ปีแก่ ปีลึก', 1, 0),
(4, 'กีฬาสี สีบลาๆ', '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `rule` enum('support','admin','user') NOT NULL,
  `admin_group` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `fname`, `lname`, `user`, `pass`, `rule`, `admin_group`, `is_deleted`) VALUES
(1, 'สมชาย', 'สายเปย์', 'admin1', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'admin', 1, 0),
(2, 'วรัญชัย', 'วิใจคำ', 'om', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'admin', 1, 0),
(3, 'วิภา', 'ผู้ช่วย', 'support1', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'support', 1, 0),
(4, 'กิตติ', 'รักเรียน', 'kitti', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'user', 1, 0),
(5, 'นภา', 'แจ่มใส', 'napha', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'user', 1, 0),
(6, 'อนุชา', 'ตั้งใจ', 'anucha', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'user', 1, 0),
(7, 'สิริพร', 'งดงาม', 'siriporn', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'user', 1, 0),
(8, 'ธีรภัทร์', 'กล้าหาญ', 'theerapat', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'user', 1, 0),
(9, 'ปรียา', 'ใจดี', 'preeya', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'user', 0, 0),
(10, 'ณัฐวุฒิ', 'สุดซอย', 'nattawut', '$2y$10$ipoUPM5434uerWeg3QU9ieAhyqiLlYT/qFkwpNRuMWsZmK1K5HPrm', 'user', 0, 0),
(11, 'oiuytr', 'oiuyf', 'pp', '$2y$10$BwTXf0xwGF1J1NM8bL6Zt.vZmNlKutePoeBajLdwW.xhNjKG.h8a6', 'user', 1, 0),
(12, 'ผม', 'ชื่ออะไร', 'name', '$2y$10$75VEBKlUx8z6ibQYvvMwpeYIK0OoevrL6yNEgvOZzfwaRu8N/gBr6', 'user', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `mem_event`
--

CREATE TABLE `mem_event` (
  `id` int(11) NOT NULL,
  `id_mem` int(11) NOT NULL,
  `id_event` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mem_event`
--

INSERT INTO `mem_event` (`id`, `id_mem`, `id_event`) VALUES
(3, 7, 1),
(16, 4, 1),
(17, 6, 1),
(19, 3, 1),
(20, 2, 1),
(21, 4, 2),
(22, 5, 2),
(23, 6, 2),
(24, 2, 2),
(25, 3, 2),
(26, 8, 1),
(27, 11, 1),
(29, 12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `slips`
--

CREATE TABLE `slips` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_event` int(11) NOT NULL,
  `add_by` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `type` enum('รายรับ','รายจ่าย') NOT NULL,
  `status` enum('รอตรวจสอบ','ผ่าน','ไม่ผ่าน') NOT NULL DEFAULT 'รอตรวจสอบ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slips`
--

INSERT INTO `slips` (`id`, `file_name`, `date`, `id_event`, `add_by`, `amount`, `title`, `details`, `type`, `status`) VALUES
(1, '1_123.jpg', '2026-09-16 07:49:01', 1, 4, 350.00, 'ค่าเสื้อเอก', 'ชำระผ่านพร้อมเพย์', 'รายรับ', 'ผ่าน'),
(2, 'slip_002.png', '2026-09-16 07:49:01', 1, 7, 350.00, 'ค่าเสื้อเอก', 'โอนช่วงเย็น', 'รายรับ', 'ไม่ผ่าน'),
(3, 'slip_003.jpg', '2026-09-16 07:49:01', 2, 7, 200.00, 'ค่าจิตอาสา', 'ร่วมโครงการปลูกป่า', 'รายรับ', 'ผ่าน'),
(4, 'event_1_20260920112213_6.jpg', '2026-09-20 09:22:13', 1, 6, NULL, NULL, NULL, 'รายรับ', 'ผ่าน'),
(5, 'event_1_20260920112530_3.jpg', '2026-09-20 09:25:30', 1, 3, NULL, NULL, NULL, 'รายรับ', 'ไม่ผ่าน'),
(6, 'event_2_20260920164712_5.jpg', '2026-09-20 14:40:17', 2, 5, NULL, NULL, NULL, 'รายรับ', 'ผ่าน'),
(7, 'event_2_20260920164742_6.jpg', '2026-09-20 14:47:24', 2, 6, NULL, NULL, NULL, 'รายรับ', 'ผ่าน');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `base_group_member`
--
ALTER TABLE `base_group_member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_admins`
--
ALTER TABLE `group_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_mem`
--
ALTER TABLE `group_mem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user`);

--
-- Indexes for table `mem_event`
--
ALTER TABLE `mem_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mem_even_members` (`id_mem`),
  ADD KEY `fk_mem_event_events` (`id_event`);

--
-- Indexes for table `slips`
--
ALTER TABLE `slips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_slips_events` (`id_event`),
  ADD KEY `fk_slips_members` (`add_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `base_group_member`
--
ALTER TABLE `base_group_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `group_admins`
--
ALTER TABLE `group_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `group_mem`
--
ALTER TABLE `group_mem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `mem_event`
--
ALTER TABLE `mem_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `slips`
--
ALTER TABLE `slips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mem_event`
--
ALTER TABLE `mem_event`
  ADD CONSTRAINT `fk_mem_even_members` FOREIGN KEY (`id_mem`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mem_event_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `slips`
--
ALTER TABLE `slips`
  ADD CONSTRAINT `fk_slips_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_slips_members` FOREIGN KEY (`add_by`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
