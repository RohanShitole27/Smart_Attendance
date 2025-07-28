-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 09:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mini`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `day` varchar(10) DEFAULT NULL,
  `timetable_id` int(11) DEFAULT NULL,
  `enr_no` varchar(20) DEFAULT NULL,
  `status` enum('present','absent') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `teacher` varchar(50) DEFAULT NULL,
  `room` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`code`, `name`, `teacher`, `room`) VALUES
('ADL', 'Advanced Data Lab', 'MS', 'S619'),
('AIML', 'AI and ML', 'OG', 'S221'),
('CF', 'Cloud Fundamentals', 'AAJ', 'S514'),
('SHD', 'Soft Skills', '', 'S519'),
('SP', 'Software Practices', 'MMK', 'S520'),
('SQL', 'SQL Basics', '', 'S519');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `time_from` time NOT NULL,
  `time_to` time NOT NULL,
  `subject_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `day`, `time_from`, `time_to`, `subject_code`) VALUES
(1, 'Monday', '08:45:00', '09:50:00', 'ADL');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('teacher','student') NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `enr_no` varchar(50) DEFAULT NULL,
  `roll_call` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `name`, `enr_no`, `roll_call`) VALUES
(1, 'teacher1', '$2y$10$IerKYBBRSbikBL269LtUQO/3zrRfsSdscbHmD3rDXsVYTi/BVMA16', 'teacher', 'Prof. Sharma', 'TCH001', 'T101'),
(2, 'student1', '$2y$10$wD1Xe.OYZHhey8fFSrHdW.gCaHGH9QjejZKVWiSWtdcKC9wYPiiyK', 'student', 'Ravi Kumar', 'ENR2025A001', 'R01'),
(3, 'student2', '$2y$10$wD1Xe.OYZHhey8fFSrHdW.gCaHGH9QjejZKVWiSWtdcKC9wYPiiyK', 'student', 'student number 2 ', 'ENR2025A002', 'R02'),
(4, 'student3', '$2y$10$wD1Xe.OYZHhey8fFSrHdW.gCaHGH9QjejZKVWiSWtdcKC9wYPiiyK', 'student', 'student number 3', 'ENR2025A003', 'R03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_code` (`subject_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`subject_code`) REFERENCES `subjects` (`code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
