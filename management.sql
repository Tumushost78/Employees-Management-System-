-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Generation Time: Dec 05, 2025 at 10:19 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `token`) VALUES
(1, 'Umuhungu', '$2y$10$bcUg2qNkd0frWCZnGglrLuiVeG5HbMKlX6qpWvV8YwuAByKc3O.Pm', '440f7d147cf7641dc3736818a4c28bab1e79ecaabbf6a76f9c19f9188bdee28dc63cf2bd70b881d17de37eb8af7079d07378');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `attended_day` int(11) DEFAULT 0,
  `attended_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `emp_id`, `attended_day`, `attended_date`) VALUES
(35, 50, 0, '2025-12-03'),
(36, 51, 0, '2025-11-30'),
(38, 54, 0, '2025-11-30'),
(42, 58, 0, '2025-12-01'),
(43, 59, 1, '2025-12-03'),
(44, 61, 0, '2025-12-03'),
(45, 60, 0, '2025-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(60) NOT NULL,
  `department_salary` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`, `department_salary`) VALUES
(44, 'Abakozi', '500.00');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `emp_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `registered_date` date DEFAULT curdate(),
  `birth_date` date NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `location` varchar(40) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `photo` varchar(70) DEFAULT 'assets/default.jpg',
  `punishment` varchar(255) DEFAULT 'Ntago afite igihango',
  `punishment_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `department_id`, `first_name`, `last_name`, `registered_date`, `birth_date`, `national_id`, `location`, `phone`, `email`, `photo`, `punishment`, `punishment_amount`) VALUES
(50, 44, 'Mutesi', 'Chantal', '2025-11-29', '2025-11-29', '119988009765432', 'Nyamata', '+250582223344', 'chantal@company.rw', '../assets/emp_692afb6aed98c.jpg', 'Ntagihango afite', '0.00'),
(51, 44, 'Mbabazi', 'Samuel', '2025-11-29', '1981-11-29', '11998800965532', 'Bugesrera', '0793175483', 'NoEmail@exampl.com', '../assets/default.jpg', 'Ntagihango afite', '0.00'),
(54, 44, 'Iradukunda', 'Joseph', '2025-11-30', '2007-11-30', '1199885098765452', 'Gisagara', '0793174483', '', '../assets/default.jpg', 'Ntagihango afite', '0.00'),
(58, 44, 'Iradukunda', 'Eric', '2025-11-30', '2019-12-01', '1199880098765532', 'Huye', '0793157483', 'eric.hr@company.rw020@gmail.com', '../assets/default.jpg', 'Ntagihango afite', '0.00'),
(59, 44, 'Abub', 'Sibomana', '2025-12-01', '1982-12-01', '1199885098765432', 'Bugesrera', '0791352345', '', '../assets/default.jpg', 'Ntagihango afite', '0.00'),
(60, 44, 'Emmanuel', 'Tumusifu', '2025-12-01', '2020-12-01', '1199880098765432', 'Bugesrera', '+250782223344', '', '../assets/emp_692ddab51fe0f.jpg', 'Ntagihango afite', '0.00'),
(61, 44, 'Uwineza', 'Sixbert', '2025-12-03', '2025-12-08', '1444888889959493383', 'Yhhshgg', '8666778287', '', '../assets/default.jpg', 'Ntagihango afite', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `payment_status` varchar(9) DEFAULT 'UNPAID',
  `paid_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `emp_id`, `payment_status`, `paid_date`) VALUES
(36, 50, 'UNPAID', NULL),
(37, 51, 'UNPAID', NULL),
(39, 54, 'PAID', '2025-11-30'),
(43, 58, 'PAID', '2025-11-30'),
(44, 59, 'UNPAID', NULL),
(45, 60, 'PAID', '2025-12-01'),
(46, 61, 'PAID', '2025-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `punishment` decimal(10,2) DEFAULT 0.00,
  `punishment_desc` varchar(255) DEFAULT 'Ntagihango afite',
  `money_recieved` decimal(10,2) NOT NULL,
  `payment_date` date DEFAULT curdate(),
  `time_at` time DEFAULT curtime()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`id`, `emp_id`, `payment_amount`, `punishment`, `punishment_desc`, `money_recieved`, `payment_date`, `time_at`) VALUES
(17, 50, '500.00', '0.00', 'Ntago afite igihango', '500.00', '2025-11-29', '17:49:26'),
(18, 51, '500.00', '200.00', 'Gusiba', '300.00', '2025-11-29', '17:50:57'),
(20, 50, '500.00', '20.00', 'Gutinda', '480.00', '2025-11-29', '18:14:59'),
(21, 50, '500.00', '100.00', 'Gutinda', '400.00', '2025-11-30', '00:03:40'),
(22, 50, '500.00', '0.00', 'Ntagihango afite', '500.00', '2025-11-29', '16:32:11'),
(25, 54, '500.00', '0.00', 'Ntago afite igihango', '500.00', '2025-11-30', '05:42:12'),
(26, 51, '500.00', '0.00', 'Ntagihango afite', '500.00', '2025-11-30', '11:12:39'),
(27, 58, '500.00', '0.00', 'Ntago afite igihango', '500.00', '2025-12-01', '01:55:43'),
(28, 59, '500.00', '180.00', 'Gutinda', '320.00', '2025-12-01', '20:15:05'),
(29, 61, '500.00', '0.00', 'Ntago afite igihango', '500.00', '2025-12-03', '10:15:55'),
(30, 60, '500.00', '100.00', 'Ntago afite igihango', '400.00', '2025-12-03', '10:16:42'),
(31, 50, '1500.00', '0.00', 'Ntagihango afite', '1500.00', '2025-12-03', '10:32:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_emp_date` (`emp_id`,`attended_date`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `fk1` (`department_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_id` (`emp_id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk4` (`emp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk3` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk2` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `fk4` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
