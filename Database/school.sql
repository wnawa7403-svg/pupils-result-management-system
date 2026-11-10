-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 03:32 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(10) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `className` varchar(10) NOT NULL,
  `class_status` varchar(10) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `teacher_id`, `className`, `class_status`, `date_added`) VALUES
(61, 16, 'EIGHT C', 'Active', '2024-11-10 02:50:41'),
(62, 12, 'TEN C', 'Active', '2024-11-10 03:05:42'),
(63, 12, 'TEN A', 'Active', '2024-11-10 03:55:21'),
(68, 0, 'TEN D', 'Inactive', '2024-11-12 03:00:04'),
(69, 0, 'TEN f', 'Active', '2024-11-12 03:02:23'),
(72, 12, 'TWELVE A', 'Active', '2024-11-20 20:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `eot`
--

CREATE TABLE `eot` (
  `marks_id` int(10) NOT NULL,
  `subject_name` varchar(50) NOT NULL,
  `pupil_Id` varchar(10) NOT NULL,
  `eot` int(4) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `test_name` varchar(10) NOT NULL,
  `ClassName` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `eot`
--

INSERT INTO `eot` (`marks_id`, `subject_name`, `pupil_Id`, `eot`, `remarks`, `FullName`, `test_name`, `ClassName`) VALUES
(47, 'RDRIR3kyOXFXMjYxK0p4cFZHaE9WOVFpNHpUelp3SkxOQ3ZjTV', '7', 0, 'failed', 'dEFBWHRKT2htOURINGZtMTNWUWV4dlJhYU5QdWtKTy96QzlvU25ZZS96OD06Ov0tK2iq2r9qOaEh7ypCR5Q=', 'EOT', 'EightA'),
(48, 'RDRIR3kyOXFXMjYxK0p4cFZHaE9WOVFpNHpUelp3SkxOQ3ZjTV', '8', 0, 'failed', 'MTcxRkt6L3dkRzlQaW5DV21hU25BSC9KdjNydm8vVVozZmpGd1huc1h3MD06OkOJ2ex/vbFE4S7rkG5T43M=', 'EOT', 'EightA');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `ID` int(11) NOT NULL,
  `user_id` varchar(30) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `Role` varchar(10) NOT NULL,
  `account` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`ID`, `user_id`, `Password`, `Role`, `account`) VALUES
(2, 'admin@gmail.com', 'admin123*', 'Admin', ''),
(5, 'staff1@gmail.com', 'teacher123*', 'Teacher', ''),
(6, 'wnawa7403@gmail.com', 'teacher123*', 'Teacher', ''),
(7, 'wnawa7403@gmail.com', 'teacher123*', 'Teacher', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'pupil123*', 'Pupil', ''),
(0, '', 'teacher123*', 'Teacher', '');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `marks_id` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL,
  `pupils_Id` int(10) NOT NULL,
  `testOne` varchar(10) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  `test_id` int(10) NOT NULL,
  `class_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`marks_id`, `subject_id`, `pupils_Id`, `testOne`, `remarks`, `test_id`, `class_id`) VALUES
(68, 0, 57, 'AB', 'ABSENT', 0, 0),
(69, 0, 57, '100', 'Excellent', 0, 0),
(70, 0, 60, 'AB', 'ABSENT', 0, 0),
(71, 0, 57, '89', 'Excellent', 0, 0),
(72, 0, 57, '100', 'Excellent', 0, 0),
(73, 0, 57, '0', 'ABSENT', 0, 0),
(74, 0, 57, '100', 'Excellent', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pupils_tb`
--

CREATE TABLE `pupils_tb` (
  `pupils_Id` int(11) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `DOB` varchar(100) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `address` varchar(30) NOT NULL,
  `parents` varchar(11) NOT NULL,
  `year` year(4) NOT NULL,
  `parents_no` varchar(100) NOT NULL,
  `ClassName` varchar(10) DEFAULT NULL,
  `profile_image` blob NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pupils_tb`
--

INSERT INTO `pupils_tb` (`pupils_Id`, `FullName`, `DOB`, `Gender`, `RegDate`, `address`, `parents`, `year`, `parents_no`, `ClassName`, `profile_image`, `email`) VALUES
(11, 'SERAL MWAMBA', '2024-11-08', 'Female', '2024-11-18 22:00:00', 'ndola', 'MR MWAMBA', 2024, '000008772', 'EightA', 0x57494e5f32303233303732365f31385f30385f31395f50726f2e6a7067, '');

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `pupil_id` int(11) DEFAULT NULL,
  `Class_id` int(11) DEFAULT NULL,
  `Subject_id` int(11) DEFAULT NULL,
  `marks` int(11) DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(10) NOT NULL,
  `subject_name` varchar(50) NOT NULL,
  `class_id` int(10) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `date_added` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `class_id`, `teacher_id`, `date_added`) VALUES
(21, 'SCIENCE', 59, 16, '2024-11-10 03:16:35.140903'),
(22, 'COMPUTER STUDIES', 59, 12, '2024-11-10 03:41:13.942742'),
(23, 'CIVIC EDUCATION', 59, 13, '2024-11-10 03:57:47.815384');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_tb`
--

CREATE TABLE `teacher_tb` (
  `teacher_id` int(11) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `middle_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `phone_no` varchar(11) NOT NULL,
  `profile_image` blob NOT NULL,
  `teacher_status` varchar(10) NOT NULL,
  `Department` varchar(50) NOT NULL,
  `Teaching_number` varchar(15) NOT NULL,
  `position` varchar(15) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `NRC` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teacher_tb`
--

INSERT INTO `teacher_tb` (`teacher_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone_no`, `profile_image`, `teacher_status`, `Department`, `Teaching_number`, `position`, `gender`, `date`, `NRC`) VALUES
(12, 'nawa', 'kambole', 'william', 'wnawa7403@gmail.com', '+2609721895', '', 'Permanent', 'MATHS', '344', 'TEACHER', 'Male', '2024-04-01', '999'),
(13, 'clementina', 'yombwe', 'mwamb', 'clem@gmail.com', '0977777777', '', 'Permanent', 'MATHS', '78956', 'class teacher', 'Male', '2024-04-03', '14308'),
(16, 'WILLIAM', 'NAWA', 'KAMBOLE', 'admin@gmail.com', '000008772', 0x616e746962696f7469632e6a706567, 'Permanent', '', '8888888', 'class teacher', 'Male', '2024-11-10', '17885.8750');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `term_id` int(10) NOT NULL,
  `t_name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`term_id`, `t_name`) VALUES
(18, 'term(Two)'),
(19, 'term(one)'),
(20, 'term(three'),
(21, 'Mock Exam'),
(23, 'Select Ter');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `test_id` int(10) NOT NULL,
  `test_name` varchar(50) NOT NULL,
  `test_status` varchar(10) NOT NULL,
  `year` year(4) NOT NULL,
  `date_added` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`test_id`, `test_name`, `test_status`, `year`, `date_added`) VALUES
(1, 'TEST 1', 'Active', 2022, '2024-11-10 10:02:54.938546'),
(2, 'TEST 1', 'Active', 2022, '2024-11-10 10:02:54.938546'),
(3, 'TEST 2', 'Active', 2022, '2024-11-10 10:02:54.938546'),
(4, 'EOT', 'Active', 2022, '2024-11-10 10:03:27.940453'),
(5, 'TEST 1', 'Active', 2024, '2024-11-11 02:48:18.623560'),
(6, 'MOCK EXAM', 'Active', 2022, '2024-11-11 19:31:29.462387');

-- --------------------------------------------------------

--
-- Table structure for table `test2`
--

CREATE TABLE `test2` (
  `marks_id` int(10) NOT NULL,
  `subject_name` varchar(50) NOT NULL,
  `pupil_Id` int(10) NOT NULL,
  `testTwo` varchar(10) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `test_name` varchar(10) NOT NULL,
  `ClassName` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `test2`
--

INSERT INTO `test2` (`marks_id`, `subject_name`, `pupil_Id`, `testTwo`, `remarks`, `FullName`, `test_name`, `ClassName`) VALUES
(23, 'dUd2VEY3MWRYNFgvUGI4TU01Ym1FZz09Ojr+1sNOKuV22KfLYx', 7, '0', 'failed', 'WHBKOExSMkNwSVFPaGZUK25JVHdFRmpEUm9XZjVZZ3l5MTgvTnBxUjZjND06OolJNOLdSnx7fB7ji3jQINs=', 'TEST 2', 'EightA'),
(24, 'dUd2VEY3MWRYNFgvUGI4TU01Ym1FZz09Ojr+1sNOKuV22KfLYx', 8, '0', 'failed', 'OUtkRWVvVXI4V21HZTFIU3M5YVhxYUtpWEFOL1VqWjRtZ2c1aVVkd1RlQT06OrlYu9db1Z1nzOUjZgctPgc=', 'TEST 2', 'EightA'),
(25, 'eUk1RjVyWm5xTkk2cldIcWJoTEdjdz09Ojrm25Ep/ckv9m3yaC', 7, 'AB', 'ABSENT', 'Y3NNQkhQSGVVWENwT2RGYmFSZUtGMHlMVmltOS9YRDhKZTFzbHNoNXRqRT06OtFb83YMVQM5jmqm4HVT/lg=', 'TEST 2', 'EightA'),
(26, 'eUk1RjVyWm5xTkk2cldIcWJoTEdjdz09Ojrm25Ep/ckv9m3yaC', 8, 'NE', 'NOT EXAMINED', 'aEJudWtHSm5UZXNZblFvd3NQYVJ6NjJzaEZCWndDY3VnUHllVFlNUUgraz06Ot2QgacTKewweKmac8dMDKM=', 'TEST 2', 'EightA');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `user_id` varchar(30) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `Role` varchar(10) NOT NULL,
  `account` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `user_id`, `Password`, `Role`, `account`) VALUES
(2, 'admin@gmail.com', 'admin123*', 'Admin', ''),
(5, 'staff1@gmail.com', 'teacher123*', 'Teacher', ''),
(6, 'wnawa7403@gmail.com', 'pupil123*', 'Pupil', ''),
(7, 'wnawa7403@gmail.com', 'teacher123*', 'Teacher', ''),
(34, 'wnawa7403@gmail.com', 'student123*', 'Student', ''),
(35, 'wnawa@gmail.com', 'student123*', 'Student', ''),
(36, 'wnawa200@gmail.com', 'student123*', 'Student', ''),
(37, 'mwamba@gmail.com', 'student123*', 'Student', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `eot`
--
ALTER TABLE `eot`
  ADD PRIMARY KEY (`marks_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`marks_id`);

--
-- Indexes for table `pupils_tb`
--
ALTER TABLE `pupils_tb`
  ADD PRIMARY KEY (`pupils_Id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `teacher_tb`
--
ALTER TABLE `teacher_tb`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`term_id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `test2`
--
ALTER TABLE `test2`
  ADD PRIMARY KEY (`marks_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `eot`
--
ALTER TABLE `eot`
  MODIFY `marks_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `marks_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `pupils_tb`
--
ALTER TABLE `pupils_tb`
  MODIFY `pupils_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `teacher_tb`
--
ALTER TABLE `teacher_tb`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `term_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `test_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `test2`
--
ALTER TABLE `test2`
  MODIFY `marks_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
