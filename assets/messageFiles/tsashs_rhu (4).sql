-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 08, 2024 at 08:00 AM
-- Server version: 10.3.39-MariaDB-cll-lve
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tsashs_rhu`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE `info` (
  `info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `child_name` varchar(225) NOT NULL,
  `mother_name` varchar(225) NOT NULL,
  `father_name` varchar(225) NOT NULL,
  `health_center` varchar(225) NOT NULL,
  `bday` date NOT NULL,
  `barangay` varchar(225) NOT NULL,
  `place_birth` varchar(225) NOT NULL,
  `height` varchar(15) NOT NULL,
  `weight` varchar(15) NOT NULL,
  `address` varchar(225) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `sex` enum('male','female') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `last_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `quantity`, `last_updated`) VALUES
(5, 1, 100, '0000-00-00 00:00:00'),
(6, 6, 98, '0000-00-00 00:00:00'),
(8, 7, 100, '0000-00-00 00:00:00'),
(10, 7, 100, '0000-00-00 00:00:00'),
(11, 11, 100, '2024-08-18 15:06:33'),
(12, 12, 99, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `mailfiles`
--

CREATE TABLE `mailfiles` (
  `file_id` int(11) NOT NULL,
  `mail_id` int(11) NOT NULL,
  `file` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mails`
--

CREATE TABLE `mails` (
  `mail_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `remarks` enum('user','admin') DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('read','unread') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mails`
--

INSERT INTO `mails` (`mail_id`, `user_id`, `message`, `remarks`, `date`, `status`) VALUES
(1, 1, 'Hi sir. meron po kayong bakuna. Tuesday po available c nurse.', 'admin', '2024-08-18 14:40:55', 'read'),
(2, 1, 'Cge po doc. Punta po kami. ', 'user', '2024-08-18 14:43:09', 'read'),
(3, 1, 'ready na po ung vaccine', 'admin', '2024-08-18 15:05:09', 'read'),
(4, 9, 'Hi kuys', 'user', '2024-08-18 15:20:53', 'read'),
(5, 9, 'Hello!!', 'admin', '2024-08-18 15:21:17', 'read');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(225) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `picture` varchar(1000) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `category`, `price`, `picture`, `created_at`, `updated_at`) VALUES
(6, 'Hepatitis B Vaccine', 'Maiiwasan ang sakit na Hepatitis B. Dapat ibigay pagkasilang.', 'Vaccines', 0.00, '805c3ac67dbd0f344c1e2a0d0e269aa3.jpg', '2024-08-18 05:33:46', '2024-08-18 05:33:46'),
(11, 'BCG Vaccine', 'Maiiwasan ang sakit na Tuberculosis (TB). Dapat ibigay pagkasilang.', 'Vaccines', 0.00, '6cb63eb7d52ea949760bccf3039e5c9d.jpg', '2024-08-18 05:48:35', '2024-08-18 15:06:33'),
(12, 'Pentavalent Vaccine (DPT-Hep B-HIB)', 'Dipterya, Tetano, Pulmonya, Meningitis, Hepatitis', 'Vaccines', 0.00, '962ca2c885b5ae03ca7c8bb6d9377610.jpg', '2024-08-18 06:07:42', '2024-08-18 06:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_no` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review` text NOT NULL,
  `stars` enum('1','2','3','4','5') NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_no`, `user_id`, `review`, `stars`, `created_at`) VALUES
(1, 1, 'Ang sarap ng burger.', '5', '2024-08-14 02:59:11'),
(2, 1, 'Good services', '4', '2024-08-14 03:02:38'),
(3, 1, 'Ang sungit ng cashier pero masarap foods', '3', '2024-08-14 03:20:42'),
(4, 3, 'Good services. Thumbs up!!', '5', '2024-08-14 03:28:54'),
(5, 1, 'Nice job po', '5', '2024-08-14 14:11:56'),
(6, 1, 'Sunget nung manager', '1', '2024-08-14 14:12:24'),
(7, 1, 'Ang galing ni nurse Kim Domingo', '5', '2024-08-18 15:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `item_type` enum('Product','Service') NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `sale_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('paid','cancelled','placed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `user_id`, `item_type`, `item_id`, `quantity`, `total_price`, `sale_date`, `status`) VALUES
(1, 1, 'Product', 11, 1, 0.00, '2024-08-18 11:45:27', 'paid'),
(2, 1, 'Product', 11, 1, 0.00, '2024-08-18 14:36:58', 'paid'),
(3, 1, 'Product', 6, 1, 0.00, '2024-08-18 14:54:17', 'paid'),
(4, 1, 'Product', 12, 1, 0.00, '2024-08-18 15:03:18', 'paid'),
(5, 9, 'Product', 6, 1, 0.00, '2024-08-18 15:20:27', 'placed');

-- --------------------------------------------------------

--
-- Table structure for table `sessionlogs`
--

CREATE TABLE `sessionlogs` (
  `session_id` int(11) NOT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `session_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `status`) VALUES
(1, 'James Bryan Bagunu', 'bagunujames@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'active'),
(2, 'System Admin', 'royaltea@ideveloptech.org', '81dc9bdb52d04dc20036dbd8313ed055', 'active'),
(3, 'Kimberly Bagunu', 'jamesbryan.bagunu@deped.gov.ph', '81dc9bdb52d04dc20036dbd8313ed055', 'active'),
(4, 'anniejean', 'jeanannie1012@gmail.com', 'a2bb5fb189c9477b92464c5efff095cf', 'active'),
(5, 'Kim Velilia', 'kimberly.vellilia@deped.gov.ph', '81dc9bdb52d04dc20036dbd8313ed055', 'active'),
(6, 'Irish Joy Eda', 'edairishjoy@gmail.com', '969c9f5244526e201615592c222fa66b', 'active'),
(7, 'Admin', 'bakuna@ideveloptech.org', '81dc9bdb52d04dc20036dbd8313ed055', 'active'),
(8, 'Hipolito Mascraba', 'hipo@sample.com', '81dc9bdb52d04dc20036dbd8313ed055', 'active'),
(9, 'John Art ', 'malsijohnart@gmail.com', '5cb98bf1447956ce3a3d99424fd3db9c', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`info_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`);

--
-- Indexes for table `mailfiles`
--
ALTER TABLE `mailfiles`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `mails`
--
ALTER TABLE `mails`
  ADD PRIMARY KEY (`mail_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_no`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`);

--
-- Indexes for table `sessionlogs`
--
ALTER TABLE `sessionlogs`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `info`
--
ALTER TABLE `info`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mailfiles`
--
ALTER TABLE `mailfiles`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mails`
--
ALTER TABLE `mails`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sessionlogs`
--
ALTER TABLE `sessionlogs`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
