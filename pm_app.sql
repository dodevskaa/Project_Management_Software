-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 11:20 PM
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
-- Database: `pm_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `edited` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `task_id`, `user_id`, `content`, `created_at`, `updated_at`, `edited`) VALUES
(1, 1, 5, 'moze da se nadopolnuva so tek na vreme.', '2025-09-09 19:40:51', NULL, 0),
(2, 1, 5, 'Admin changed the status to In Progress', '2025-09-09 20:30:30', NULL, 0),
(3, 1, 4, 'Anja changed the status to In Progress', '2025-09-09 20:30:59', NULL, 0),
(4, 1, 4, 'Anja changed the status to Done', '2025-09-09 20:31:03', NULL, 0),
(5, 1, 4, 'bazata se koregira soodvetno na izmenite', '2025-09-09 20:31:34', NULL, 0),
(6, 2, 7, 'Taylor changed the status to QA', '2025-09-09 20:32:24', NULL, 0),
(7, 2, 7, 'Taylor changed the status to In Progress', '2025-09-09 20:32:29', NULL, 0),
(9, 5, 9, 'Jon changed the status to In Progress', '2025-09-09 20:47:48', NULL, 0),
(12, 3, 5, 'Bazata moze da se nadograduva.', '2025-09-10 00:15:32', '2025-09-10 02:24:14', 1),
(13, 3, 5, 'Admin changed the status to QA', '2025-09-10 00:15:40', NULL, 0),
(15, 6, 6, 'Senior24 changed the status to In Progress', '2025-09-10 01:44:19', NULL, 0),
(16, 6, 6, 'Senior24 changed the status to Done', '2025-09-10 01:48:43', NULL, 0),
(17, 8, 7, 'Taylor changed the status to In Progress', '2025-09-10 01:50:37', NULL, 0),
(19, 9, 6, 'test comment', '2025-09-10 19:46:51', NULL, 0),
(21, 8, 6, 'test 2', '2025-09-10 20:02:23', NULL, 0),
(22, 7, 6, 'test', '2025-09-10 20:02:28', NULL, 0),
(23, 10, 12, 'John Doe changed the status to QA', '2025-09-10 20:40:17', NULL, 0),
(24, 11, 12, 'John Doe changed the status to In Progress', '2025-09-10 20:40:21', NULL, 0),
(25, 11, 12, 'test', '2025-09-10 20:40:50', NULL, 0),
(26, 10, 12, 'test', '2025-09-10 20:40:56', NULL, 0),
(27, 11, 4, 'Anja changed the status to Done', '2025-09-10 20:42:20', NULL, 0),
(28, 11, 4, 'haj', '2025-09-10 20:42:32', NULL, 0),
(29, 6, 13, 'Anna changed the status to To Do', '2025-09-10 20:54:39', NULL, 0),
(30, 6, 13, 'Anna changed the status to In Progress', '2025-09-10 20:54:40', NULL, 0),
(32, 16, 14, 'JustSenior changed the status to QA', '2025-09-10 20:55:34', NULL, 0),
(33, 16, 14, 'JustSenior changed the status to Done', '2025-09-10 20:55:40', NULL, 0),
(34, 15, 14, 'JustSenior changed the status to Done', '2025-09-10 20:55:44', NULL, 0),
(36, 14, 14, 'JustSenior changed the status to Done', '2025-09-10 20:55:56', NULL, 0),
(37, 6, 14, 'JustSenior changed the status to Done', '2025-09-10 20:56:06', NULL, 0),
(38, 10, 14, 'JustSenior changed the status to In Progress', '2025-09-10 20:57:38', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `estimated_time` varchar(100) DEFAULT NULL,
  `team_lead_id` int(11) DEFAULT NULL,
  `status` enum('Active','Done','Expired') DEFAULT 'Active',
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `requirements`, `estimated_time`, `team_lead_id`, `status`, `deadline`, `created_at`) VALUES
(1, 'Vtor proekt ', 'This project management software will serve as a simplified task and project management system. The system allows for a hierarchical structure where users are categorized by experience level: Senior, Mid, and Junior, with Admins having full system control. A subset of Senior users can be designated as Team Leads, who are responsible for managing teams and projects.', '1. xxxx\r\n2. yyyy\r\n3. zzzzzz', '1 month', 6, 'Active', '2025-09-10', '2025-09-09 09:44:03'),
(2, 'Test1', 'testttttt123', '1.\r\n2. \r\n3.', '10 days', 6, 'Expired', '2025-09-01', '2025-09-09 10:00:22'),
(3, 'Project - Parking', 'To create a project for the inspection of parking places.', '1. figma\n2. database\n3. initial logic of the project\n4. ddd\n5. ccc\n6. aaaa', '1 month', 9, 'Active', '2025-09-30', '2025-09-09 20:40:02'),
(8, 'Brainster Project', 'Create a new brainster project that will calculate the points of all students enrolled in the Full Stack course.\r\nData should be pulled from the database, which should be created manually.', '/', '1 month', 9, 'Active', '2025-10-31', '2025-09-10 01:02:01'),
(9, 'Test for Mid', 'mid', '/', '10 days', 6, 'Active', '2025-09-25', '2025-09-10 20:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_in_team` enum('Senior','Mid','Junior') DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`id`, `project_id`, `user_id`, `role_in_team`, `added_at`) VALUES
(1, 1, 4, 'Junior', '2025-09-09 19:26:25'),
(2, 1, 7, 'Mid', '2025-09-09 19:47:14'),
(3, 3, 8, 'Mid', '2025-09-09 20:46:27'),
(4, 3, 4, 'Junior', '2025-09-09 20:46:34'),
(5, 3, 9, 'Senior', '2025-09-09 20:46:41'),
(6, 3, 7, 'Senior', '2025-09-09 23:55:44'),
(9, 8, 6, 'Senior', '2025-09-10 01:46:38'),
(10, 8, 7, 'Mid', '2025-09-10 01:49:34'),
(11, 9, 12, 'Senior', '2025-09-10 20:26:24'),
(12, 9, 4, 'Senior', '2025-09-10 20:26:28'),
(13, 9, 14, 'Senior', '2025-09-10 20:26:33'),
(14, 9, 9, 'Senior', '2025-09-10 20:26:40'),
(15, 9, 7, 'Senior', '2025-09-10 20:26:47'),
(17, 2, 7, 'Senior', '2025-09-10 20:44:40'),
(18, 2, 12, 'Senior', '2025-09-10 20:44:45'),
(19, 2, 14, 'Senior', '2025-09-10 20:44:49'),
(20, 2, 9, 'Senior', '2025-09-10 20:45:02'),
(21, 2, 13, 'Senior', '2025-09-10 21:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'To Do',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `created_by`, `status`, `assigned_to`, `created_at`, `updated_at`) VALUES
(1, 1, 'Create Database', 'da se kreira celosno bazata za potrebite na proektot.', 5, 'Done', 4, '2025-09-09 19:40:16', '2025-09-09 20:31:03'),
(2, 1, 'Pocetna faza na proektot.', 'Da se kreira pocetnata faza na proektot.', 6, 'In Progress', 7, '2025-09-09 19:45:58', '2025-09-09 20:32:29'),
(3, 3, 'Create Database', 'kreiraj baza za proektot.', 5, 'QA', 8, '2025-09-09 20:41:16', '2025-09-10 00:15:40'),
(5, 3, 'Initial phase of the project.\n', 'To post the initial logic of the project.', 9, 'In Progress', 9, '2025-09-09 20:47:38', '2025-09-09 20:47:48'),
(6, 2, 'initial functionslity', '/', 6, 'Done', 13, '2025-09-10 01:44:08', '2025-09-10 20:56:06'),
(7, 8, 'Create Database', '/', 9, 'To Do', 6, '2025-09-10 01:46:48', '2025-09-10 01:46:52'),
(8, 8, 'Figma Design.', '/', 9, 'In Progress', 7, '2025-09-10 01:49:52', '2025-09-10 01:50:37'),
(9, 1, 'test test', 'test', 6, 'To Do', 7, '2025-09-10 19:46:38', '2025-09-10 19:46:43'),
(10, 9, 'xxx', '/', 5, 'In Progress', 12, '2025-09-10 20:26:57', '2025-09-10 20:57:38'),
(11, 9, '2', '/', 5, 'Done', 4, '2025-09-10 20:27:14', '2025-09-10 20:42:20'),
(12, 9, '3', '/', 5, 'To Do', 14, '2025-09-10 20:27:24', '2025-09-10 20:27:29'),
(13, 9, '4', '/', 5, 'To Do', 9, '2025-09-10 20:27:36', '2025-09-10 20:28:19'),
(14, 2, 'test mid', '/', 5, 'Done', 7, '2025-09-10 20:45:09', '2025-09-10 20:55:56'),
(15, 2, 'test senior', '/', 5, 'Done', 13, '2025-09-10 20:45:17', '2025-09-10 20:55:44'),
(16, 2, 'test 2', '/', 5, 'Done', 7, '2025-09-10 20:45:22', '2025-09-10 20:55:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('Admin','TeamLead','Senior','Mid','Junior') NOT NULL DEFAULT 'Junior',
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `level`, `approved`, `created_at`) VALUES
(4, 'Anja', 'anja@junior.com', '$2y$10$5QVFtGo78e8KTeORedMQvepzjUCHypOgikISBEKfiJW.4.x7yHb5O', 'Junior', 1, '2025-09-07 16:26:30'),
(5, 'Admin', 'admin@example.com', '$2y$10$l0QR2eWp3dO8mOMFvU4S4Ov/3ZDUAD64oxAbJbl8mVVMtj3jBOI8y', 'Admin', 1, '2025-09-08 19:43:43'),
(6, 'Senior24', 'senior24@senior.com', '$2y$10$X4Hc1fQjk21Y4QoUHpHsMeZy5PqF7FeM61NwomnsG0RAJ8LM42kO2', 'TeamLead', 1, '2025-09-08 19:46:05'),
(7, 'Taylor', 'taylor@mid.com', '$2y$10$YEqwg5BltJ4FF.BpEIT3Ouo7Wp8DLvN9GNeESdSExc6Fq8Q15Cm7O', 'Mid', 1, '2025-09-09 09:57:33'),
(8, 'Joe', 'joe@mid.com', '$2y$10$IHnHZlxXTHldsMVGd8prku9f7UaRZcGDZG7/PULa8BWgFw4u.8lS2', 'Mid', 1, '2025-09-09 19:48:24'),
(9, 'Jon', 'jon@senior.com', '$2y$10$FRrWDr7kYYQ3wom/hVB1y.fBvYIJm3nSPhnmwYEETMIluOvLBRqBK', 'TeamLead', 1, '2025-09-09 19:48:57'),
(10, 'Test Junior', 'test@junior.com', '$2y$10$5DiXPMT4k1BuDNvRDaaknePsFiER27pbjEgiHQ49m/6Msbyyk2Ru2', 'Junior', 0, '2025-09-09 20:51:12'),
(12, 'John Doe', 'johndoe@mid.com', '$2y$10$aha2AMtUJsYhXpLWOmu72ubjbrf5/oDGBGXFmgLNcNLU/u/rYdqCm', 'Mid', 1, '2025-09-10 01:30:52'),
(13, 'Anna', 'anna@junior.com', '$2y$10$IDmKq2h2foDd4GfwozRH2.gtRwiBnY765gFLfOA8Kmv.VcJQCR4vW', 'Junior', 1, '2025-09-10 01:33:02'),
(14, 'JustSenior', 'just@senior.com', '$2y$10$Ww8Dzego0hCwGRJydTFxJ.EA1ERz.HADOghPHZ2euoC9FYpT20/qe', 'Senior', 1, '2025-09-10 01:38:32'),
(15, 'Lola', 'lola@junior.com', '$2y$10$Iq.jujabfLezC0uRififO.x0tEb.TiM8au5pCkW2twI.txt6zxaOi', 'Junior', 0, '2025-09-10 01:42:59'),
(16, 'LastMember', 'last@mid.com', '$2y$10$W91bh6Han0mglam3wPk/weXgrIMqC0kZeykiAKm.0A1O3G0ionRgG', 'Mid', 0, '2025-09-10 21:19:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_lead_id` (`team_lead_id`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `fk_task_created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`team_lead_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_task_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
