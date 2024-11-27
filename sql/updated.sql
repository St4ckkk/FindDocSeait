-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 27, 2024 at 12:39 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finddocseait`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submitted_by` int DEFAULT NULL,
  `accepted_by` int DEFAULT NULL,
  `office_id` int DEFAULT NULL,
  `document_type` varchar(255) NOT NULL,
  `details` text,
  `purpose` text,
  `recipient_office_id` int DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tracking_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `share_with` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `office_id` (`office_id`),
  KEY `recipient_office_id` (`recipient_office_id`),
  KEY `fk_share_with` (`share_with`),
  KEY `fk_accepted_by` (`accepted_by`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `submitted_by`, `accepted_by`, `office_id`, `document_type`, `details`, `purpose`, `recipient_office_id`, `document_path`, `status`, `tracking_number`, `created_at`, `updated_at`, `share_with`) VALUES
(26, 1, NULL, 2, 'Leave (Form 6)', 'qwe', 'qwe', 1, '../../uploads/THEPLAGUE (2).pdf', 'submitted', 'TRK-1732589243-9828', '2024-11-26 02:47:23', '2024-11-27 02:37:13', NULL),
(25, 1, 2, 5, 'Leave (Form 6)', 'qwe', 'qwe', 1, '../../uploads/THEPLAGUE (1).pdf', 'pending', 'TRK-1732585361-4041', '2024-11-26 01:42:41', '2024-11-27 02:37:25', NULL),
(27, 1, NULL, 5, 'Report Card', 'qwe', 'qwe', 1, '../../uploads/THEPLAGUE (2).pdf', 'submitted', 'TRK-1732625704-5746', '2024-11-26 12:55:04', '2024-11-27 02:37:19', NULL),
(28, 2, 1, 1, 'Leave (Form 6)', 'qwe', 'qwe', 5, '../../uploads/THEPLAGUE (1) (1).pdf', 'pending', 'TRK-1732674973-4452', '2024-11-27 02:36:13', '2024-11-27 02:37:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ip_blocklist`
--

DROP TABLE IF EXISTS `ip_blocklist`;
CREATE TABLE IF NOT EXISTS `ip_blocklist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `blocked_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blocked_by` (`blocked_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `risk_level` varchar(50) NOT NULL,
  `device_type` varchar(50) NOT NULL,
  `os_type` varchar(50) NOT NULL,
  `browser_type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `ip_address`, `user_agent`, `status`, `risk_level`, `device_type`, `os_type`, `browser_type`) VALUES
(48, 1, '10.121.166.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Avast/130.0.0.0', 'success', 'low', 'unknown', 'unknown', 'unknown'),
(46, 3, '10.34.112.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Avast/130.0.0.0', 'success', 'low', 'unknown', 'unknown', 'unknown'),
(47, 1, '10.121.166.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Avast/130.0.0.0', 'success', 'low', 'unknown', 'unknown', 'unknown'),
(45, 1, '10.121.166.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Avast/130.0.0.0', 'blocked', 'high', 'unknown', 'unknown', 'unknown'),
(44, 1, '10.121.166.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Avast/130.0.0.0', 'error', 'high', 'unknown', 'unknown', 'unknown'),
(43, 1, '10.121.166.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Avast/130.0.0.0', 'error', 'high', 'unknown', 'unknown', 'unknown'),
(42, 1, '10.121.166.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Avast/130.0.0.0', 'error', 'high', 'unknown', 'unknown', 'unknown');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

DROP TABLE IF EXISTS `offices`;
CREATE TABLE IF NOT EXISTS `offices` (
  `office_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`office_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`office_id`, `name`) VALUES
(1, 'Scholarship Office'),
(2, 'Registrar Office'),
(3, 'President Office'),
(4, 'Accounting Office'),
(5, 'Dean Office'),
(6, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(255) NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`) VALUES
(1, 'read'),
(2, 'write'),
(3, 'delete');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Super Admin'),
(3, 'Guest'),
(4, 'School Personnel'),
(5, 'Student'),
(6, 'Editor'),
(7, 'Viewer');

-- --------------------------------------------------------

--
-- Table structure for table `tracking_logs`
--

DROP TABLE IF EXISTS `tracking_logs`;
CREATE TABLE IF NOT EXISTS `tracking_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tracking_number` varchar(50) DEFAULT NULL,
  `submitted_by` int DEFAULT NULL,
  `office_id` int DEFAULT NULL,
  `document_type` varchar(255) NOT NULL,
  `details` text,
  `purpose` text,
  `recipient_office_id` int DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `office_id` (`office_id`),
  KEY `recipient_office_id` (`recipient_office_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tracking_logs`
--

INSERT INTO `tracking_logs` (`id`, `tracking_number`, `submitted_by`, `office_id`, `document_type`, `details`, `purpose`, `recipient_office_id`, `document_path`, `status`, `title`, `message`, `created_at`, `updated_at`) VALUES
(42, 'TRK-1732674973-4452', 2, 1, 'Leave (Form 6)', 'qwe', 'qwe', 5, '../../uploads/THEPLAGUE (1) (1).pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Scholarship Office', '2024-11-27 02:36:13', '2024-11-27 02:37:56'),
(41, 'TRK-1732674973-4452', 2, 2, 'Leave (Form 6)', 'qwe', 'qwe', 1, '../../uploads/THEPLAGUE (1) (1).pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-27 02:36:13', '2024-11-27 02:36:13'),
(40, 'TRK-1732585361-4041', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 2, '../../uploads/THEPLAGUE (1).pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-26 01:42:41', '2024-11-27 01:41:36'),
(39, 'TRK-1732625704-5746', 1, 5, 'Report Card', 'qwe', 'qwe', 2, '../../uploads/THEPLAGUE (2).pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-26 12:55:04', '2024-11-26 12:55:04'),
(38, 'TRK-1732589243-9828', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 1, '../../uploads/THEPLAGUE (2).pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-26 02:47:23', '2024-11-26 02:47:23'),
(37, 'TRK-1732585361-4041', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 2, '../../uploads/THEPLAGUE (1).pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-26 01:42:41', '2024-11-26 01:42:41'),
(36, 'TRK-1732438195-4333', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 2, '../../uploads/THEPLAGUE.pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-24 08:49:55', '2024-11-24 09:06:15'),
(35, 'TRK-1732438195-4333', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 2, '../../uploads/THEPLAGUE.pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-24 08:49:55', '2024-11-24 08:52:55'),
(34, 'TRK-1732438195-4333', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 2, '../../uploads/THEPLAGUE.pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-24 08:49:55', '2024-11-24 08:49:55'),
(33, 'TRK-1732436364-6980', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 3, '../../uploads/watermarked.pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-24 08:19:24', '2024-11-24 08:47:57'),
(32, 'TRK-1732436364-6980', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 3, '../../uploads/watermarked.pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-24 08:19:24', '2024-11-24 08:45:36'),
(31, 'TRK-1732436364-6980', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 3, '../../uploads/watermarked.pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-24 08:19:24', '2024-11-24 08:44:32'),
(30, 'TRK-1732436364-6980', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 3, '../../uploads/watermarked.pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-24 08:19:24', '2024-11-24 08:19:24'),
(29, 'TRK-1732434926-7450', 1, 5, 'Report Card', 'qwe', 'qwe', 2, '../../uploads/watermarked.pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-24 07:55:26', '2024-11-24 08:07:36'),
(28, 'TRK-1732434926-7450', 1, 5, 'Report Card', 'qwe', 'qwe', 2, '../../uploads/watermarked.pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-24 07:55:26', '2024-11-24 07:55:26'),
(27, 'TRK-1732434056-5993', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 2, '../../uploads/watermarked.pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-24 07:40:56', '2024-11-24 07:40:56'),
(26, 'TRK-1732427526-5204', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 3, '../../uploads/THEPLAGUE.pdf', 'pending', 'Request Approved', 'Document request reviewed and approved by the Dean Office', '2024-11-24 05:52:06', '2024-11-24 05:52:14'),
(25, 'TRK-1732427526-5204', 1, 5, 'Leave (Form 6)', 'qwe', 'qwe', 3, '../../uploads/THEPLAGUE.pdf', 'submitted', 'Document Submitted', 'Document request received and logged in the system', '2024-11-24 05:52:06', '2024-11-24 05:52:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `permissions_id` int DEFAULT NULL,
  `office_id` int DEFAULT NULL,
  `passkey` varchar(150) NOT NULL,
  `otp_verified` tinyint(1) DEFAULT '0',
  `google_auth_secret` varchar(255) DEFAULT NULL,
  `csrf_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `token_timestamp` timestamp NULL DEFAULT NULL,
  `login_attempts` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `office_id` (`office_id`),
  KEY `fk_permissions` (`permissions_id`),
  KEY `idx_csrf_token` (`csrf_token`(250))
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `fullname`, `phone_number`, `role_id`, `permissions_id`, `office_id`, `passkey`, `otp_verified`, `google_auth_secret`, `csrf_token`, `token_timestamp`, `login_attempts`) VALUES
(1, 'keyan123', '$2y$10$5qoFXz7DEGpUwP.KWCrlW.ArzA5nZT9O4BihOpp/7ffZw/VQRIIBm', 'zorenempal@gmail.com', 'Keyan Andy Delgado', NULL, 3, NULL, 5, '$2y$10$Dkt836wDuoHKV3icX.mFh.8JBY88VvQABsA3aQI2HwjmVwJm7aH3S', 1, NULL, '5f9ad98ed504904013672d784ad3e0e86a95b0020775afc0885c5139551317be', '2024-11-27 12:25:28', 0),
(4, 'abdul123', '$2y$10$kFZB5pkfPApfh3GCW.BF5u/sv/UdqTmD.UPSm4HBVsmc0CCQKhEhW', 'abdulmarot@gmail.com', 'Abdul Marot', NULL, 1, NULL, 2, '', 0, NULL, NULL, NULL, 0),
(2, 'jener123', '$2y$10$kavEQvPnbIhi4mMMsAlAAuBl4YE1/0kpEbHbdq4yvxWvSmivrYimu', 'jener@gmail.com', 'Jener Kevin Ogatis', NULL, 1, NULL, 2, '', 1, NULL, NULL, NULL, 0),
(3, 'lynch123', '$2y$10$nHqlNAS/qYczq.uD8uvVX.vpSz/MUpsWizVdIbhRcvBEWl4hM1X/y', 'lynch123', 'Lynch Nico Futolan', NULL, 1, NULL, 3, '', 1, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission`) VALUES
(51, 1, 'view_documents'),
(47, 3, 'view_documents'),
(48, 3, 'download_documents'),
(35, 2, 'download_documents'),
(34, 2, 'view_documents'),
(49, 3, 'delete_documents'),
(36, 2, 'delete_documents'),
(46, 0, 'download_documents'),
(50, 3, 'share_documents'),
(52, 1, 'download_documents'),
(53, 1, 'delete_documents'),
(54, 1, 'share_documents');

-- --------------------------------------------------------

--
-- Table structure for table `virtual_ips`
--

DROP TABLE IF EXISTS `virtual_ips`;
CREATE TABLE IF NOT EXISTS `virtual_ips` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `virtual_ip` varchar(45) DEFAULT NULL,
  `assigned_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_used` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_blocked` tinyint(1) DEFAULT '0',
  `block_reason` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `virtual_ips`
--

INSERT INTO `virtual_ips` (`id`, `user_id`, `virtual_ip`, `assigned_date`, `last_used`, `is_blocked`, `block_reason`) VALUES
(11, 1, '10.121.166.152', '2024-11-27 12:19:31', '2024-11-27 12:21:16', 0, NULL),
(12, 3, '10.34.112.179', '2024-11-27 12:20:43', '2024-11-27 12:20:43', 0, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
