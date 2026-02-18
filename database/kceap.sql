-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 05:18 AM
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
-- Database: `kceap`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `fullname`, `username`, `password`, `created_at`) VALUES
(2, 'Matt andrei belano', 'admin', '$2y$10$QUZOYZYo53GtJGl9BhUUZ.hArUQGGwLdrra2d60e35xn6H5Foa366', '2025-06-20 13:43:08');

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `college_account`
--

CREATE TABLE `college_account` (
  `id` int(10) UNSIGNED NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `school` varchar(150) DEFAULT NULL,
  `course` varchar(150) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `semester` varchar(50) DEFAULT '1st semester',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `schedule_date` date DEFAULT NULL,
  `schedule_time` time DEFAULT NULL,
  `status` varchar(155) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `college_documents`
--

CREATE TABLE `college_documents` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `college_renew_documents`
--

CREATE TABLE `college_renew_documents` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `college_reports`
--

CREATE TABLE `college_reports` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `school` varchar(255) DEFAULT NULL,
  `course` varchar(150) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'graduated',
  `school_year` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_reports`
--

INSERT INTO `college_reports` (`id`, `applicant_id`, `first_name`, `middle_name`, `last_name`, `school`, `course`, `year_level`, `semester`, `address`, `phone_number`, `email`, `status`, `school_year`, `created_at`) VALUES
(3, 49, 'MATT', 'ANDREI', 'BELANO', 'CPSU-KABANKALAN', 'EDUC - SCIENCE', '4th Year', '2nd semester', 'DAAN BANUA', '09665028045', 'lottiesguanzon@gmail.com', 'graduated', '2026-2027', '2026-02-18 04:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `college_schedule`
--

CREATE TABLE `college_schedule` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `middleName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `yearLevel` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phoneNumber` varchar(50) DEFAULT NULL,
  `emailAddress` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `college_schedule_list`
--

CREATE TABLE `college_schedule_list` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `school` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `highschool_account`
--

CREATE TABLE `highschool_account` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `strand` varchar(100) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `schedule_date` date DEFAULT NULL,
  `schedule_time` time DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `semester` varchar(50) DEFAULT '1st semester'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `highschool_documents`
--

CREATE TABLE `highschool_documents` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `highschool_renew_documents`
--

CREATE TABLE `highschool_renew_documents` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `file_path` varchar(250) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `highschool_schedule`
--

CREATE TABLE `highschool_schedule` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `middleName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) NOT NULL,
  `school` varchar(150) NOT NULL,
  `strand` varchar(50) NOT NULL,
  `yearLevel` varchar(20) NOT NULL,
  `address` varchar(150) NOT NULL,
  `phoneNumber` varchar(30) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `highschool_schedule_list`
--

CREATE TABLE `highschool_schedule_list` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `school` varchar(150) NOT NULL,
  `strand` varchar(50) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hs_reports`
--

CREATE TABLE `hs_reports` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `school` varchar(150) DEFAULT NULL,
  `strand` varchar(150) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(155) DEFAULT NULL,
  `school_year` varchar(50) DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hs_reports`
--

INSERT INTO `hs_reports` (`id`, `applicant_id`, `first_name`, `middle_name`, `last_name`, `school`, `strand`, `year_level`, `semester`, `address`, `phone_number`, `email`, `status`, `school_year`, `archived_at`) VALUES
(3, 32, 'RIANNE CRISTOPHER', 'J', 'AGUILAR', 'SOUTHLAND-HS', 'GAS', 'Grade 12', '2nd semester', 'LOCOTAN', '09665028042', 'belanomattandrei@gmail.com', 'graduated', '2026-2027', '2026-02-18 04:15:35');

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
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `college_account`
--
ALTER TABLE `college_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `college_documents`
--
ALTER TABLE `college_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `college_renew_documents`
--
ALTER TABLE `college_renew_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `college_reports`
--
ALTER TABLE `college_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `college_schedule`
--
ALTER TABLE `college_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `college_schedule_list`
--
ALTER TABLE `college_schedule_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `highschool_account`
--
ALTER TABLE `highschool_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `highschool_documents`
--
ALTER TABLE `highschool_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `highschool_renew_documents`
--
ALTER TABLE `highschool_renew_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `highschool_schedule`
--
ALTER TABLE `highschool_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `highschool_schedule_list`
--
ALTER TABLE `highschool_schedule_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hs_reports`
--
ALTER TABLE `hs_reports`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `college_account`
--
ALTER TABLE `college_account`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `college_documents`
--
ALTER TABLE `college_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `college_renew_documents`
--
ALTER TABLE `college_renew_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `college_reports`
--
ALTER TABLE `college_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `college_schedule`
--
ALTER TABLE `college_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `college_schedule_list`
--
ALTER TABLE `college_schedule_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `highschool_account`
--
ALTER TABLE `highschool_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `highschool_documents`
--
ALTER TABLE `highschool_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `highschool_renew_documents`
--
ALTER TABLE `highschool_renew_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `highschool_schedule`
--
ALTER TABLE `highschool_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `highschool_schedule_list`
--
ALTER TABLE `highschool_schedule_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `hs_reports`
--
ALTER TABLE `hs_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
