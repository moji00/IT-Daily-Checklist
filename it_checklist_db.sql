-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 05:27 AM
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
-- Database: `it_checklist_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `created_by`, `created_at`, `category`) VALUES
(3, 'Access Point', '1ST FLOOR ACCESS POINT', 3, '2025-08-11 06:44:46', 'Network'),
(18, 'Bizbox', 'abcd', 3, '2025-08-13 02:50:38', 'Server'),
(19, 'Test', '123', 3, '2025-08-13 06:18:40', 'Security'),
(20, 'Test 123', '123', 3, '2025-08-14 01:41:16', 'Security');

-- --------------------------------------------------------

--
-- Table structure for table `task_descriptions`
--

CREATE TABLE `task_descriptions` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_descriptions`
--

INSERT INTO `task_descriptions` (`id`, `task_id`, `text`, `created_at`) VALUES
(1, 18, 'a', '2025-08-13 06:18:13'),
(2, 18, 'b', '2025-08-13 06:18:13'),
(3, 18, 'c', '2025-08-13 06:18:13'),
(4, 19, '1', '2025-08-13 06:19:10'),
(5, 19, '2', '2025-08-13 06:19:10'),
(6, 19, '3', '2025-08-13 06:19:10'),
(7, 3, 'AP-1', '2025-08-13 06:24:01'),
(8, 3, 'AP-2', '2025-08-13 06:24:01'),
(9, 3, 'AP-3', '2025-08-13 06:24:01'),
(10, 3, 'AP-4', '2025-08-13 06:24:31'),
(11, 3, 'AP-5', '2025-08-13 06:24:31'),
(13, 20, '1', '2025-08-14 01:41:33'),
(14, 20, '2', '2025-08-14 01:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@tmc.com', '$2y$10$3Lr/0hZUfk1HbpOBFPe17.Tl9zjZ.u3tXqEQbKdxXv38Jc7AdMWZS', 'admin', '2025-08-11 06:13:29'),
(3, 'TMC IT', 'it@tmc.com', '$2y$10$5tH31V4XDUFuS5AGLtvqce7qnbDEfQ8RCXxzT3gw5YvswhIUOTvC6', 'user', '2025-08-11 06:40:43'),
(6, 'Paul', 'pmojica@tmc.com', '$2y$10$juV.dZO2yydjCFspR8sitOt9B5oGviMnmsyDZ1OVKU4ppS6PyRP/.', 'user', '2025-08-12 08:47:18'),
(7, 'User', 'user@tmc.com', '$2y$10$6YbAjXOg66klimoBX6MIe.hDHOzunfV7tGwr8QyfBWuTq4CaA4XNe', 'user', '2025-08-14 02:14:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_description_checks`
--

CREATE TABLE `user_description_checks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_description_id` int(11) NOT NULL,
  `check_date` date NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_description_checks`
--

INSERT INTO `user_description_checks` (`id`, `user_id`, `task_description_id`, `check_date`, `status`, `completed_at`) VALUES
(1, 3, 1, '2025-08-13', 'completed', '2025-08-13 14:18:16'),
(2, 3, 2, '2025-08-13', 'completed', '2025-08-13 14:18:17'),
(3, 3, 3, '2025-08-13', 'completed', '2025-08-13 14:18:18'),
(4, 3, 4, '2025-08-13', 'completed', '2025-08-13 14:19:14'),
(5, 3, 5, '2025-08-13', 'completed', '2025-08-13 14:19:15'),
(6, 3, 6, '2025-08-13', 'completed', '2025-08-13 14:19:16'),
(7, 3, 7, '2025-08-13', 'completed', '2025-08-13 14:24:44'),
(8, 3, 8, '2025-08-13', 'completed', '2025-08-13 14:24:46'),
(9, 3, 9, '2025-08-13', 'completed', '2025-08-13 14:24:47'),
(10, 3, 10, '2025-08-13', 'completed', '2025-08-13 14:24:48'),
(11, 3, 11, '2025-08-13', 'completed', '2025-08-13 14:24:49'),
(13, 3, 1, '2025-08-14', 'completed', '2025-08-14 10:25:49'),
(14, 3, 2, '2025-08-14', 'completed', '2025-08-14 10:25:51'),
(15, 3, 3, '2025-08-14', 'completed', '2025-08-14 10:25:52'),
(16, 3, 4, '2025-08-14', 'completed', '2025-08-14 10:25:54'),
(17, 3, 5, '2025-08-14', 'completed', '2025-08-14 10:25:55'),
(18, 3, 6, '2025-08-14', 'completed', '2025-08-14 10:25:57'),
(19, 3, 13, '2025-08-14', 'completed', '2025-08-14 10:26:01'),
(20, 3, 14, '2025-08-14', 'completed', '2025-08-14 10:26:02'),
(21, 3, 7, '2025-08-14', 'completed', '2025-08-14 10:26:03'),
(22, 3, 8, '2025-08-14', 'completed', '2025-08-14 10:26:04'),
(23, 3, 9, '2025-08-14', 'completed', '2025-08-14 10:26:05'),
(24, 3, 10, '2025-08-14', 'completed', '2025-08-14 10:26:06'),
(25, 3, 11, '2025-08-14', 'completed', '2025-08-14 10:26:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_tasks`
--

CREATE TABLE `user_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `task_date` date NOT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `completed_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_tasks`
--

INSERT INTO `user_tasks` (`id`, `user_id`, `task_id`, `task_date`, `status`, `completed_at`, `remarks`, `updated_at`) VALUES
(9, 1, 1, '2025-08-11', 'pending', NULL, NULL, '2025-08-11 06:40:02'),
(10, 1, 2, '2025-08-11', 'pending', NULL, NULL, '2025-08-11 06:40:02'),
(42, 1, 3, '2025-08-11', 'pending', NULL, NULL, '2025-08-11 08:11:53'),
(67, 3, 1, '2025-08-11', 'completed', NULL, NULL, '2025-08-11 09:07:42'),
(68, 3, 2, '2025-08-11', 'completed', NULL, NULL, '2025-08-11 09:07:45'),
(69, 3, 3, '2025-08-11', 'completed', NULL, NULL, '2025-08-11 09:07:47'),
(524, 6, 3, '2025-08-12', 'completed', NULL, NULL, '2025-08-12 08:47:53'),
(584, 3, 3, '2025-08-13', 'completed', NULL, NULL, '2025-08-13 06:35:51'),
(1050, 3, 18, '2025-08-13', 'completed', NULL, NULL, '2025-08-13 05:38:23'),
(1077, 6, 18, '2025-08-13', 'pending', NULL, NULL, '2025-08-13 03:02:51'),
(1078, 6, 3, '2025-08-13', 'pending', NULL, NULL, '2025-08-13 03:02:51'),
(1207, 3, 19, '2025-08-13', 'completed', NULL, NULL, '2025-08-13 06:33:49'),
(1227, 6, 19, '2025-08-13', 'pending', NULL, NULL, '2025-08-13 06:20:44'),
(1308, 6, 18, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:38:15'),
(1309, 6, 19, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:38:20'),
(1310, 6, 3, '2025-08-14', 'pending', NULL, NULL, '2025-08-14 01:26:14'),
(1548, 3, 18, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:30:15'),
(1549, 3, 19, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:30:20'),
(1550, 3, 20, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:30:22'),
(1551, 3, 3, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:30:24'),
(1620, 7, 18, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:35:05'),
(1621, 7, 19, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:35:10'),
(1622, 7, 20, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:35:20'),
(1623, 7, 3, '2025-08-14', 'completed', NULL, NULL, '2025-08-14 02:35:48'),
(1650, 6, 20, '2025-08-14', 'pending', NULL, NULL, '2025-08-14 02:38:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_descriptions`
--
ALTER TABLE `task_descriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_description_checks`
--
ALTER TABLE `user_description_checks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_desc_date` (`user_id`,`task_description_id`,`check_date`),
  ADD KEY `task_description_id` (`task_description_id`);

--
-- Indexes for table `user_tasks`
--
ALTER TABLE `user_tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_task_date` (`user_id`,`task_id`,`task_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `task_descriptions`
--
ALTER TABLE `task_descriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_description_checks`
--
ALTER TABLE `user_description_checks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_tasks`
--
ALTER TABLE `user_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1680;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `task_descriptions`
--
ALTER TABLE `task_descriptions`
  ADD CONSTRAINT `task_descriptions_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_description_checks`
--
ALTER TABLE `user_description_checks`
  ADD CONSTRAINT `user_description_checks_ibfk_1` FOREIGN KEY (`task_description_id`) REFERENCES `task_descriptions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
