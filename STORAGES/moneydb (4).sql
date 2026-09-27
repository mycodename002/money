-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2026 at 10:44 AM
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
(1, 3, 1),
(3, 4, 1);

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
(1, 2, 1);

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
(1, 'ป.ตรี 4 ปี ', 'ปีการศึกษา 2568', 1, 0);

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
(1, 'CPETC', '22', 'cpetc', '$2y$10$6DJUbFgMLYRmaR2nvLGPe.Ku/eIres/uFA1mwoZfKsBUFb0u8wjKq', 'support', 0, 0),
(2, 'วรัญชัย', 'วิใจคำ', 'waranchai', '$2y$10$zunayBQ3jRLXqYyATHltwuLtYtIQAdJC7a3I9hLpIea5lirLWkR.S', 'admin', 1, 0),
(3, 'วรัชยา', 'ค้าคล่อง', 'nam', '$2y$10$PXxT.X5wPLGUXZIYANl6KOcE88DTXvHMOZT0ccpAMw..zP2iaHJsu', 'user', 1, 0),
(4, 'จิรายุส', 'หลำคำ', 'gun', '$2y$10$y.gddFojTTs8aVm9WFXqTOkreyigoqzDX228wpEyoen4hoIVRpzU2', 'user', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `mem_event`
--

CREATE TABLE `mem_event` (
  `id` int(11) NOT NULL,
  `id_mem` int(11) NOT NULL,
  `id_event` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` enum('รอตรวจสอบ','ผ่าน','ไม่ผ่าน') NOT NULL DEFAULT 'รอตรวจสอบ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_admins`
--
ALTER TABLE `group_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `group_mem`
--
ALTER TABLE `group_mem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mem_event`
--
ALTER TABLE `mem_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `slips`
--
ALTER TABLE `slips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
