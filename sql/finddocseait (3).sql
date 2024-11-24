-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 24, 2024 at 09:08 AM
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
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `submitted_by`, `accepted_by`, `office_id`, `document_type`, `details`, `purpose`, `recipient_office_id`, `document_path`, `status`, `tracking_number`, `created_at`, `updated_at`, `share_with`) VALUES
(24, 1, 2, 5, 'Leave (Form 6)', 'qwe', 'qwe', 2, '../../uploads/THEPLAGUE.pdf', 'pending', 'TRK-1732438195-4333', '2024-11-24 08:49:55', '2024-11-24 09:06:15', NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tracking_logs`
--

INSERT INTO `tracking_logs` (`id`, `tracking_number`, `submitted_by`, `office_id`, `document_type`, `details`, `purpose`, `recipient_office_id`, `document_path`, `status`, `title`, `message`, `created_at`, `updated_at`) VALUES
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
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `office_id` (`office_id`),
  KEY `fk_permissions` (`permissions_id`),
  KEY `idx_csrf_token` (`csrf_token`(250))
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `fullname`, `phone_number`, `role_id`, `permissions_id`, `office_id`, `passkey`, `otp_verified`, `google_auth_secret`, `csrf_token`, `token_timestamp`) VALUES
(1, 'keyan123', '$2y$10$5qoFXz7DEGpUwP.KWCrlW.ArzA5nZT9O4BihOpp/7ffZw/VQRIIBm', 'keyanandydelgado@gmail.com', 'Keyan Andy Delgado', NULL, 1, NULL, 5, '$2y$10$.fwxWsuln3UrRBJ.mB4fYOu2ilL/IwezhKgxpDU/RUPjydLrESSh6', 1, NULL, '517bdb14282011cbd353d36154f1cf686a94e5cd172b742e9298a8e449c8ac7f', '2024-11-24 09:08:06'),
(2, 'jener123', '$2y$10$kavEQvPnbIhi4mMMsAlAAuBl4YE1/0kpEbHbdq4yvxWvSmivrYimu', 'jener@gmail.com', 'Jener Kevin Ogatis', NULL, 1, NULL, 2, '', 1, NULL, NULL, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission`) VALUES
(24, 1, 'share_documents'),
(23, 1, 'delete_documents'),
(22, 1, 'download_documents'),
(21, 1, 'view_documents');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
