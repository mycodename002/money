-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2026 at 07:21 AM
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
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `titel` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `titel`, `details`, `is_deleted`) VALUES
(1, 'เก็บตังค์โว้ย 2', '', 0),
(2, 'กิจกรรมจิตอาสาปลูกป่า', 'ร่วมกันปลูกป่าชายเลนและเก็บขยะชายหาด', 0),
(3, 'เก็บตังค์โว้ยย 3', 'อาทิตย์อะไรวะ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `groupname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `groupname`) VALUES
(1, 'ป.ตรี 4ปี '),
(2, 'ป.ตรี เทียบโอน');

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
(2, 21, 1);

-- --------------------------------------------------------

--
-- Table structure for table `group_admin_event`
--

CREATE TABLE `group_admin_event` (
  `id` int(11) NOT NULL,
  `id_group_admin` int(11) NOT NULL,
  `id_event` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_admin_event`
--

INSERT INTO `group_admin_event` (`id`, `id_group_admin`, `id_event`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `group_mem`
--

CREATE TABLE `group_mem` (
  `id` int(11) NOT NULL,
  `titel` varchar(50) NOT NULL,
  `details` varchar(255) NOT NULL,
  `id_group_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_mem`
--

INSERT INTO `group_mem` (`id`, `titel`, `details`, `id_group_admin`) VALUES
(1, 'กลุ่มที่ 1 สัมมนา', 'กลุ่มย่อยสำหรับประสานงานสัมมนา', 1),
(2, 'ทีมจิตอาสา A', 'ทีมเตรียมอุปกรณ์ปลูกป่า', 2),
(3, 'ฝ่ายจัดเลี้ยง', 'ทีมดูแลอาหารและสถานที่งานปีใหม่', 3);

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
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `fname`, `lname`, `user`, `pass`, `rule`, `is_deleted`) VALUES
(1, 'สมชาย', 'ชาย', 'somchai', '$2y$10$T8ZCb1zLsMor7wcfDVGwoOEg57MLgFcJRc/6mJDZrdMwhXVvXiLCG', 'admin', 0),
(2, 'วิภา', 'ภา', 'wipha', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'support', 0),
(3, 'กิตติ', 'ตติ', 'kitti', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(4, 'นภา', 'ภา', 'napha', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(5, 'อนุชา', 'ชา', 'anucha', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(6, 'สิริพร', 'พร', 'siriporn', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(7, 'ธีรภัทร์', 'ภัทร์', 'theerapat', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(8, 'ปรียา', 'ปรีย์', 'preeya', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(9, 'ณัฐวุฒิ', 'วุฒิ', 'nattawut', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(10, 'กัญญารัตน์', 'ก้อย', 'kanyarat', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(11, 'ชยพล', 'พล', 'chayapol', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(12, 'ธนกฤต', 'กฤต', 'thanakrit', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(13, 'ปานทิพย์', 'ทิพย์', 'panthip', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(14, 'พิชญะ', 'พีท', 'pichaya', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(15, 'ภัทรวดี', 'ภัทร', 'pattarawadee', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(16, 'เมธาสิทธิ์', 'เมธ', 'methasit', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(17, 'วรินทร', 'ริน', 'warinthorn', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(18, 'ศุภโชค', 'โชค', 'suphachok', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(19, 'อริสา', 'ริสา', 'arisa', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(20, 'เอกชัย', 'เอก', 'ekachai', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1234567890abcdefghijklm', 'user', 0),
(21, 'วรัญชัย', 'วิใจคำ', 'om', '$2y$10$T8ZCb1zLsMor7wcfDVGwoOEg57MLgFcJRc/6mJDZrdMwhXVvXiLCG', 'admin', 0),
(23, 'วรัญชัย', 'วิใจคำ', 'om1', '$2y$10$xshylO5kwAXDZr6DomyYh.syazka1lJGtWJOOX90/yUoRGjQ31kJu', 'user', 0),
(25, 'วรัชยา', 'ค้าคล่อง', 'nam', '$2y$10$F/TSbDymWXvQ5NGHXrvLMe/NAO/VHBXac8Kud47OFLXGLEPk3AanK', 'admin', 0);

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
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 1, 2),
(12, 3, 2),
(13, 5, 2),
(14, 7, 2),
(15, 9, 2),
(16, 11, 2),
(17, 12, 2),
(18, 13, 2),
(19, 14, 2),
(20, 15, 2),
(21, 2, 3),
(22, 4, 3),
(23, 6, 3),
(24, 8, 3),
(25, 10, 3),
(26, 16, 3),
(27, 17, 3),
(28, 18, 3),
(29, 19, 3),
(30, 20, 3);

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
  `amount` decimal(10,2) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `type` enum('รายรับ','รายจ่าย') NOT NULL,
  `status` enum('รอตรวจสอบ','ผ่าน','ไม่ผ่าน') NOT NULL DEFAULT 'รอตรวจสอบ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slips`
--

INSERT INTO `slips` (`id`, `file_name`, `date`, `id_event`, `add_by`, `amount`, `title`, `details`, `type`, `status`) VALUES
(1, 'slip_001.jpg', '2026-09-10 06:22:46', 1, 1, 1500.00, '', '', 'รายรับ', 'ผ่าน'),
(2, 'slip_002.png', '2026-09-10 06:22:46', 1, 3, 1500.00, '', '', 'รายรับ', 'รอตรวจสอบ'),
(3, 'slip_003.jpg', '2026-09-10 06:22:46', 2, 2, 5000.00, 'ค่าอุปกรณ์ปลูกป่าชำระค่าลงทะเบียน กิ', 'ซื้อต้นกล้าและถุงมือ', 'รายจ่าย', 'ผ่าน'),
(4, 'slip_004.pdf', '2026-09-10 06:22:46', 3, 4, 1200.00, 'ค่ามัดจำสถานที่', 'มัดจำห้องจัดเลี้ยง', 'รายจ่าย', 'รอตรวจสอบ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_admins`
--
ALTER TABLE `group_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_admin_event`
--
ALTER TABLE `group_admin_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_group_admin_members` (`id_group_admin`),
  ADD KEY `fk_group_admin_events` (`id_event`);

--
-- Indexes for table `group_mem`
--
ALTER TABLE `group_mem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_group_mem_group_admin` (`id_group_admin`);

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
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `group_admins`
--
ALTER TABLE `group_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `group_admin_event`
--
ALTER TABLE `group_admin_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `group_mem`
--
ALTER TABLE `group_mem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `mem_event`
--
ALTER TABLE `mem_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `slips`
--
ALTER TABLE `slips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `group_admin_event`
--
ALTER TABLE `group_admin_event`
  ADD CONSTRAINT `fk_group_admin_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_group_admin_members` FOREIGN KEY (`id_group_admin`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_mem`
--
ALTER TABLE `group_mem`
  ADD CONSTRAINT `fk_group_mem_group_admin` FOREIGN KEY (`id_group_admin`) REFERENCES `group_admin_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
