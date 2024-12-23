-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2024 at 12:43 PM
-- Wersja serwera: 10.4.28-MariaDB
-- Wersja PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `log_manager`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `severity` enum('Emergency','Alert','Critical','Error','Warning','Notice','Informational','Debug') NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `project_id`, `user_id`, `timestamp`, `severity`, `message`) VALUES
(1, 1, 1, '2024-12-16 12:30:04', 'Critical', '121323'),
(2, 1, 1, '2024-12-16 12:54:41', 'Debug', '1233'),
(3, 2, 1, '2024-12-16 13:02:13', 'Informational', 'Aniaaaaa'),
(4, 6, 2, '2024-12-16 13:28:57', 'Warning', '31313'),
(5, 2, 1, '2024-12-16 14:48:54', 'Critical', 'test'),
(6, 1, 1, '2024-12-16 14:49:08', 'Critical', 'test1'),
(7, 1, 1, '2024-12-16 14:51:58', 'Critical', '1'),
(8, 1, 1, '2024-12-16 14:52:11', 'Error', '1'),
(9, 1, 1, '2024-12-16 14:52:19', 'Emergency', '1221313'),
(10, 1, 1, '2024-12-16 14:52:31', 'Alert', '131313'),
(11, 1, 1, '2024-12-16 14:52:49', 'Error', '1313444'),
(12, 1, 1, '2024-12-16 15:10:28', 'Warning', '123123123123'),
(13, 9, 1, '2024-12-16 20:25:04', 'Notice', 'test'),
(14, 8, 1, '2024-12-16 21:07:12', 'Notice', '1231231'),
(15, 8, 1, '2024-12-16 21:15:43', 'Notice', '1231231'),
(16, 1, 1, '2024-12-16 21:16:01', 'Critical', 'An error occurred in the application'),
(17, 2, 1, '2024-12-16 21:16:08', 'Critical', 'An error occurred in the application'),
(18, 9, 1, '2024-12-16 21:23:37', 'Warning', 'asdasda'),
(19, 9, 1, '2024-12-16 21:26:18', 'Warning', 'asdasda'),
(20, 2, 1, '2024-12-16 21:29:00', 'Critical', 'An error occurred in the application'),
(21, 9, 1, '2024-12-16 21:29:07', 'Emergency', 'DASDASD'),
(22, 9, 1, '2024-12-16 21:31:04', 'Emergency', 'DASDASD'),
(23, 9, 1, '2024-12-16 21:32:32', 'Emergency', 'DASDASD'),
(24, 7, 1, '2024-12-16 21:32:46', 'Error', 'sadasda'),
(25, 7, 1, '2024-12-16 21:33:58', 'Error', 'sadasda'),
(26, 7, 1, '2024-12-16 21:34:53', 'Error', 'sadasda'),
(27, 7, 1, '2024-12-16 21:34:55', 'Error', 'sadasda'),
(28, 7, 1, '2024-12-16 21:35:27', 'Error', 'sadasda'),
(29, 7, 1, '2024-12-16 21:35:32', 'Error', 'sadasda'),
(30, 7, 1, '2024-12-16 21:36:46', 'Error', 'sadasda'),
(31, 7, 1, '2024-12-16 21:37:33', 'Error', 'sadasda'),
(32, 2, 1, '2024-12-16 21:37:42', 'Critical', 'An error occurred in the application'),
(33, 1, 1, '2024-12-22 13:47:24', 'Critical', '21212122'),
(34, 1, 1, '2024-12-22 14:01:36', 'Critical', 'An error occurred in the application'),
(36, 1, 1, '2024-12-22 14:15:14', 'Alert', '11'),
(37, 1, 1, '2024-12-22 14:15:39', 'Emergency', '1'),
(38, 1, 1, '2024-12-22 14:19:25', 'Emergency', '1'),
(39, 1, 1, '2024-12-22 14:20:16', 'Emergency', '1'),
(40, 8, 1, '2024-12-22 14:21:15', 'Warning', '123123123123123'),
(45, 8, 1, '2024-12-22 14:29:02', 'Warning', '123123123123123'),
(47, 1, 1, '2024-12-22 14:38:45', 'Critical', 'An error occurred in the application'),
(48, 1, 1, '2024-12-22 14:38:59', 'Critical', 'An error occurred in the application'),
(49, 2, 1, '2024-12-22 14:39:01', 'Critical', 'An error occurred in the application'),
(50, 2, 1, '2024-12-22 14:40:03', 'Critical', 'An error occurred in the application'),
(51, 7, 1, '2024-12-22 14:40:39', 'Critical', 'An error occurred in the application'),
(52, 2, 1, '2024-12-22 14:40:53', 'Alert', '123123124444'),
(53, 1, 1, '2024-12-22 15:10:39', 'Critical', 'An error occurred in the application');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
