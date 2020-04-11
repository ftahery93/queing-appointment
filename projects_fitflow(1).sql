-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2018 at 08:37 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projects_fitflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `name_en`, `name_ar`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Yoga', 'Yoga', '1520172471.jpeg', 1, '2018-02-27 11:52:05', '2018-03-14 13:38:35');

-- --------------------------------------------------------

--
-- Table structure for table `admin_modules`
--

CREATE TABLE `admin_modules` (
  `id` int(11) NOT NULL,
  `module` text COLLATE utf8_unicode_ci NOT NULL,
  `module_prefix` text COLLATE utf8_unicode_ci NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:display',
  `created` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:display',
  `edit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:display',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:display',
  `upload` tinyint(1) DEFAULT '0' COMMENT '1:display',
  `print` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:display',
  `status` int(11) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admin_modules`
--

INSERT INTO `admin_modules` (`id`, `module`, `module_prefix`, `view`, `created`, `edit`, `deleted`, `upload`, `print`, `status`, `sort_order`) VALUES
(1, 'users', 'users', 1, 1, 1, 1, 0, 0, 1, 1),
(2, 'master records', 'master', 1, 1, 1, 1, 0, 0, 1, 2),
(3, 'dashboard', 'dashboard', 1, 0, 0, 0, 0, 0, 1, 3),
(4, 'permissions', 'permissions', 1, 1, 1, 1, 0, 0, 1, 4),
(5, 'trainers', 'trainers', 1, 1, 1, 1, 0, 0, 1, 5),
(6, 'logActivity', 'logActivity', 1, 0, 0, 0, 0, 0, 1, 6),
(7, 'registeredUsers', 'registeredUsers', 1, 1, 1, 1, 0, 0, 1, 7),
(8, 'cmsPages', 'cmsPages', 1, 0, 1, 0, 0, 0, 1, 8),
(9, 'packages', 'packages', 1, 1, 1, 1, 0, 0, 1, 9),
(10, 'contactus', 'contactus', 1, 1, 1, 1, 0, 0, 1, 10),
(11, 'languageManagement', 'languageManagement', 1, 1, 1, 1, 0, 0, 1, 11),
(12, 'incomeStatistics', 'incomeStatistics', 1, 0, 0, 0, 0, 0, 1, 12),
(13, 'vendorPackages', 'vendorPackages', 1, 0, 0, 0, 0, 0, 1, 13),
(14, 'classPackages', 'classPackages', 1, 0, 0, 0, 0, 0, 1, 14),
(15, 'classes', 'classes', 1, 0, 0, 0, 0, 0, 1, 15),
(16, 'archivedClasses', 'archivedClasses', 1, 0, 0, 0, 0, 0, 1, 16),
(17, 'notifications', 'notifications', 1, 1, 1, 1, 0, 0, 1, 17),
(18, 'transactions', 'transactions', 1, 1, 1, 1, 0, 0, 1, 18),
(19, 'settings', 'settings', 0, 0, 0, 0, 0, 0, 0, 19),
(20, 'importexportdata', 'importexportdata', 0, 0, 0, 0, 0, 0, 0, 20),
(21, 'backup', 'backup', 0, 0, 0, 0, 0, 0, 0, 21);

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` int(10) UNSIGNED NOT NULL,
  `governorate_id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `backup_lists`
--

CREATE TABLE `backup_lists` (
  `id` int(10) UNSIGNED NOT NULL,
  `file_name` text COLLATE utf8_unicode_ci NOT NULL,
  `file_size` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `backup_lists`
--

INSERT INTO `backup_lists` (`id`, `file_name`, `file_size`, `created_at`, `updated_at`) VALUES
(3, '2018-03-05-132533.zip', '5523', '2018-03-05 10:25:33', '2018-03-05 10:25:33'),
(4, '2018-03-05-142124.zip', '5557', '2018-03-05 11:21:27', '2018-03-05 11:21:27'),
(5, '2018-03-05-155254.zip', '5573', '2018-03-05 12:53:05', '2018-03-05 12:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `cc_payments`
--

CREATE TABLE `cc_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference_no` int(10) UNSIGNED NOT NULL,
  `response_code` bigint(20) DEFAULT NULL,
  `response_desc` text COLLATE utf8_unicode_ci,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receipt_no` bigint(20) DEFAULT NULL,
  `transaction_no` bigint(20) DEFAULT NULL,
  `acquirer_response_code` bigint(20) DEFAULT NULL,
  `auth_id` bigint(20) DEFAULT NULL,
  `batch_no` bigint(20) DEFAULT NULL,
  `card_type` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(10,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cmspages`
--

CREATE TABLE `cmspages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8_unicode_ci,
  `description_ar` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cmspages`
--

INSERT INTO `cmspages` (`id`, `name_en`, `name_ar`, `description_en`, `description_ar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Privacy Policy', 'سياسة الخصوصيةs', 'Privacy Policy', 'سياسة الخصوصية', 1, '2018-03-01 08:54:35', '2018-03-14 12:56:59');

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `id` int(10) UNSIGNED NOT NULL,
  `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `contactus`
--

INSERT INTO `contactus` (`id`, `fullname`, `email`, `mobile`, `message`, `created_at`, `updated_at`) VALUES
(1, 'aziz', 'aziz@gmail.com', '12345678', 'dfasf', '2018-02-28 21:00:00', '2018-02-28 21:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `subject` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `url` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gender_types`
--

CREATE TABLE `gender_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `gender_types`
--

INSERT INTO `gender_types` (`id`, `name_en`, `name_ar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Male', 'Male', 1, '2018-02-27 21:00:00', '2018-02-27 21:00:00'),
(2, 'Female', 'Female', 1, '2018-02-27 21:00:00', '2018-02-27 21:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `governorates`
--

CREATE TABLE `governorates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `governorates`
--

INSERT INTO `governorates` (`id`, `name_en`, `name_ar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Al Asimah Governorate (Capital)', 'Al Asimah Governorate (Capital)', 1, '2018-02-05 07:26:34', '2018-02-05 07:26:34'),
(2, 'Hawalli Governorate', 'Hawalli Governorate', 1, '2018-02-05 07:26:34', '2018-02-05 07:26:34'),
(3, 'Farwaniya Governorate', 'Farwaniya Governorate', 1, '2018-02-05 07:26:34', '2018-02-05 07:26:34'),
(4, 'Mubarak Al-Kabeer Governorate', 'Mubarak Al-Kabeer Governorate', 1, '2018-02-05 07:26:34', '2018-02-05 07:26:34'),
(5, 'Ahmadi Governorate', 'Ahmadi Governorate', 1, '2018-02-05 07:26:34', '2018-02-05 07:26:34'),
(6, 'Jahra Governorate', 'Jahra Governorate', 1, '2018-02-05 07:26:34', '2018-02-05 07:26:34');

-- --------------------------------------------------------

--
-- Table structure for table `importdata_tables`
--

CREATE TABLE `importdata_tables` (
  `id` int(10) UNSIGNED NOT NULL,
  `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `imported_files`
--

CREATE TABLE `imported_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `imported_table_id` int(11) DEFAULT NULL,
  `imported_file` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knet_payments`
--

CREATE TABLE `knet_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `payment_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,3) NOT NULL,
  `track_id` bigint(20) DEFAULT NULL,
  `transaction_id` bigint(20) DEFAULT NULL,
  `auth` bigint(20) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `result` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language_management`
--

CREATE TABLE `language_management` (
  `id` int(10) UNSIGNED NOT NULL,
  `label_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `language_management`
--

INSERT INTO `language_management` (`id`, `label_en`, `label_ar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Privacy', 'الإجمالية', 1, '2018-03-03 13:24:42', '2018-03-03 13:27:52');

-- --------------------------------------------------------

--
-- Table structure for table `log_activities`
--

CREATE TABLE `log_activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_type` tinyint(1) DEFAULT NULL COMMENT 'Admin:0,Vendor:1,Trainer:2',
  `vendor_id` int(11) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `log_activities`
--

INSERT INTO `log_activities` (`id`, `subject`, `url`, `method`, `ip`, `agent`, `user_id`, `user_type`, `vendor_id`, `trainer_id`, `created_at`, `updated_at`) VALUES
(1, 'Transaction - Vendor hashim amount 120.000KD has been deleted by admin', 'http://localhost:8000/admin/transactions/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 09:03:55', '2018-03-07 09:03:55'),
(2, 'Transaction - Trainer Ashik amount 12.000KD has been deleted by admin', 'http://localhost:8000/admin/transactions/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 09:03:55', '2018-03-07 09:03:55'),
(3, 'Transaction - Trainer Ashik amount 12.000KD has been updated by admin', 'http://localhost:8000/admin/transactions/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 09:30:13', '2018-03-07 09:30:13'),
(4, 'Transaction - Trainer taha amount 12.000KD has been updated by admin', 'http://localhost:8000/admin/transactions/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 09:30:26', '2018-03-07 09:30:26'),
(5, 'Transaction - Trainer taha amount 12.000KD has been updated by admin', 'http://localhost:8000/admin/transactions/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 09:30:45', '2018-03-07 09:30:45'),
(6, 'Transaction - Trainer taha amount 12.000KD has been updated by admin', 'http://localhost:8000/admin/transactions/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 09:31:13', '2018-03-07 09:31:13'),
(7, 'Notification - fdas has been created by admin', 'http://localhost:8000/admin/notifications', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 11:57:31', '2018-03-07 11:57:31'),
(8, 'Notification - gsdfg has been created by admin', 'http://localhost:8000/admin/notifications', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 11:59:30', '2018-03-07 11:59:30'),
(9, 'Notification - gsdfg has been updated by admin', 'http://localhost:8000/admin/notifications/1', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 12:18:55', '2018-03-07 12:18:55'),
(10, 'Notification - gsdfg has been updated by admin', 'http://localhost:8000/admin/notifications/1', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 12:19:15', '2018-03-07 12:19:15'),
(11, 'Notification - gsdfgfasdfsafas... has been updated by admin', 'http://localhost:8000/admin/notifications/1', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 12:19:45', '2018-03-07 12:19:45'),
(12, 'Notification - ["gsdfgfasdfsafas..."] has been deleted by admin', 'http://localhost:8000/admin/notifications/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 12:26:54', '2018-03-07 12:26:54'),
(13, 'Notification - fdas has been created by admin', 'http://localhost:8000/admin/notifications', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 12:56:32', '2018-03-07 12:56:32'),
(14, 'Notification - dfas has been created by admin', 'http://localhost:8000/admin/notifications', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 12:59:34', '2018-03-07 12:59:34'),
(15, 'Notification - fdasfdasfasafas... has been updated by admin', 'http://localhost:8000/admin/notifications/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 12:59:59', '2018-03-07 12:59:59'),
(16, 'Notification - as333fdasfdasfa... has been updated by admin', 'http://localhost:8000/admin/notifications/3', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 13:00:13', '2018-03-07 13:00:13'),
(17, 'Notification - ["fdasfdasfasafas...","as333fdasfdasfa..."] has been deleted by admin', 'http://localhost:8000/admin/notifications/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 1, 0, 0, 0, '2018-03-07 13:00:40', '2018-03-07 13:00:40'),
(18, 'Activity - swimming has been created by admin', 'http://localhost:8000/admin/activities', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:12:27', '2018-03-08 09:12:27'),
(19, 'Activity - boxing has been created by admin', 'http://localhost:8000/admin/activities', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:12:55', '2018-03-08 09:12:55'),
(20, 'Activity - ["swimming","boxing"] has been deleted by admin', 'http://localhost:8000/admin/masters/activities/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:13:24', '2018-03-08 09:13:24'),
(21, 'Activity - new entry has been created by admin', 'http://localhost:8000/admin/activities', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:15:28', '2018-03-08 09:15:28'),
(22, 'Activity - real entry has been created by admin', 'http://localhost:8000/admin/activities', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:15:45', '2018-03-08 09:15:45'),
(23, 'Activity - ["new entry","real entry"] has been deleted by admin', 'http://localhost:8000/admin/masters/activities/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:16:17', '2018-03-08 09:16:17'),
(24, 'Activity - new entry has been created by admin', 'http://localhost:8000/admin/activities', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:16:50', '2018-03-08 09:16:50'),
(25, 'Activity - real entry has been created by admin', 'http://localhost:8000/admin/activities', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:17:11', '2018-03-08 09:17:11'),
(26, 'Activity - fake entry has been created by admin', 'http://localhost:8000/admin/activities', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:17:29', '2018-03-08 09:17:29'),
(27, 'Activity - ["new entry","real entry","fake entry"] has been deleted by admin', 'http://localhost:8000/admin/masters/activities/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:18:22', '2018-03-08 09:18:22'),
(28, 'Area - ffasdf has been created by admin', 'http://localhost:8000/admin/areas', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:19:51', '2018-03-08 09:19:51'),
(29, 'Area - eeee has been created by admin', 'http://localhost:8000/admin/areas', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:20:05', '2018-03-08 09:20:05'),
(30, 'Area - ["ffasdf","eeee"] has been deleted by admin', 'http://localhost:8000/admin/masters/areas/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:20:14', '2018-03-08 09:20:14'),
(31, 'RegisteredUser - fakemail has been created by admin', 'http://localhost:8000/admin/registeredUsers', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:25:36', '2018-03-08 09:25:36'),
(32, 'RegisteredUser - ["Noor","abbas","fakemail"] has been trashed by admin', 'http://localhost:8000/admin/registeredUsers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:25:56', '2018-03-08 09:25:56'),
(33, 'RegisteredUser - Noor has been restore by admin', 'http://localhost:8000/admin/registeredUsers/trashed/1/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:26:09', '2018-03-08 09:26:09'),
(34, 'RegisteredUser - fakemail has been restore by admin', 'http://localhost:8000/admin/registeredUsers/trashed/3/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:26:13', '2018-03-08 09:26:13'),
(35, 'RegisteredUser - abbas has been restore by admin', 'http://localhost:8000/admin/registeredUsers/trashed/2/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:26:16', '2018-03-08 09:26:16'),
(36, 'RegisteredUser - ["fakemail"] has been trashed by admin', 'http://localhost:8000/admin/registeredUsers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:26:41', '2018-03-08 09:26:41'),
(37, 'RegisteredUser - fakemail has been deleted by admin', 'http://localhost:8000/admin/registeredUsers/trashed/3/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-08 09:26:53', '2018-03-08 09:26:53'),
(38, 'Area - newarea has been created by admin', 'http://localhost:8000/admin/areas', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 07:08:35', '2018-03-11 07:08:35'),
(39, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:30:00', '2018-03-11 08:30:00'),
(40, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:30:25', '2018-03-11 08:30:25'),
(41, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:31:53', '2018-03-11 08:31:53'),
(42, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:36:12', '2018-03-11 08:36:12'),
(43, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:36:34', '2018-03-11 08:36:34'),
(44, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:43:00', '2018-03-11 08:43:00'),
(45, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:43:32', '2018-03-11 08:43:32'),
(46, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:55:38', '2018-03-11 08:55:38'),
(47, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:56:09', '2018-03-11 08:56:09'),
(48, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:56:30', '2018-03-11 08:56:30'),
(49, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:57:07', '2018-03-11 08:57:07'),
(50, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 08:59:53', '2018-03-11 08:59:53'),
(51, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:00:21', '2018-03-11 09:00:21'),
(52, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:02:38', '2018-03-11 09:02:38'),
(53, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:04:20', '2018-03-11 09:04:20'),
(54, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:04:31', '2018-03-11 09:04:31'),
(55, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:22:28', '2018-03-11 09:22:28'),
(56, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:26:08', '2018-03-11 09:26:08'),
(57, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:29:20', '2018-03-11 09:29:20'),
(58, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:31:27', '2018-03-11 09:31:27'),
(59, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:31:41', '2018-03-11 09:31:41'),
(60, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:32:26', '2018-03-11 09:32:26'),
(61, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:32:40', '2018-03-11 09:32:40'),
(62, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:32:59', '2018-03-11 09:32:59'),
(63, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:33:29', '2018-03-11 09:33:29'),
(64, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:33:42', '2018-03-11 09:33:42'),
(65, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:34:00', '2018-03-11 09:34:00'),
(66, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:36:44', '2018-03-11 09:36:44'),
(67, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:37:37', '2018-03-11 09:37:37'),
(68, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:56:58', '2018-03-11 09:56:58'),
(69, 'Trainer - Ashik has been restore by admin', 'http://localhost:8000/admin/trainers/trashed/1/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 09:59:38', '2018-03-11 09:59:38'),
(70, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:00:08', '2018-03-11 10:00:08'),
(71, 'Trainer - Ashik has been restore by admin', 'http://localhost:8000/admin/trainers/trashed/1/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:01:00', '2018-03-11 10:01:00'),
(72, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:01:11', '2018-03-11 10:01:11'),
(73, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:06:31', '2018-03-11 10:06:31'),
(74, 'Trainer - ["ashik","tushar"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:08:12', '2018-03-11 10:08:12'),
(75, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:12:59', '2018-03-11 10:12:59'),
(76, 'Trainer - Ashik has been restore by admin', 'http://localhost:8000/admin/trainers/trashed/1/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:13:33', '2018-03-11 10:13:33'),
(77, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:13:42', '2018-03-11 10:13:42'),
(78, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:14:13', '2018-03-11 10:14:13'),
(79, 'Trainer - ["ashik"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:14:45', '2018-03-11 10:14:45'),
(80, 'Trainer - ["ashik1"] has been trashed by admin', 'http://localhost:8000/admin/trainers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:14:58', '2018-03-11 10:14:58'),
(81, 'Trainer - Ashik has been restore by admin', 'http://localhost:8000/admin/trainers/trashed/12/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:15:10', '2018-03-11 10:15:10'),
(82, 'Vendor - ["hashim"] has been trashed by admin', 'http://localhost:8000/admin/vendors/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:20:54', '2018-03-11 10:20:54'),
(83, 'Vendor - ["hashim"] has been trashed by admin', 'http://localhost:8000/admin/vendors/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:21:25', '2018-03-11 10:21:25'),
(84, 'RegisteredUser - ["Noor"] has been trashed by admin', 'http://localhost:8000/admin/registeredUsers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:23:55', '2018-03-11 10:23:55'),
(85, 'RegisteredUser - ["abbas"] has been trashed by admin', 'http://localhost:8000/admin/registeredUsers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:24:04', '2018-03-11 10:24:04'),
(86, 'RegisteredUser - abbas has been restore by admin', 'http://localhost:8000/admin/registeredUsers/trashed/2/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:24:23', '2018-03-11 10:24:23'),
(87, 'RegisteredUser - ["abbas"] has been trashed by admin', 'http://localhost:8000/admin/registeredUsers/delete', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:54:27', '2018-03-11 10:54:27'),
(88, 'RegisteredUser - abbas has been restore by admin', 'http://localhost:8000/admin/registeredUsers/trashed/2/restore', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-11 10:54:39', '2018-03-11 10:54:39'),
(89, 'Transaction - Vendor hashim amount 3KD has been created by admin', 'http://localhost:8000/admin/transactions', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-12 08:23:25', '2018-03-12 08:23:25'),
(90, 'Transaction - Vendor hashim amount 4.000KD has been updated by admin', 'http://localhost:8000/admin/transactions/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-12 08:25:04', '2018-03-12 08:25:04'),
(91, 'Package - Standard has been updated by admin', 'http://localhost:8000/admin/packages/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-14 12:27:17', '2018-03-14 12:27:17'),
(92, 'Vendor - hashim has been updated by admin', 'http://localhost:8000/admin/vendors/2', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-14 13:23:31', '2018-03-14 13:23:31'),
(93, 'Trainer - ashik has been created by admin', 'http://localhost:8000/admin/trainers', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-14 13:39:54', '2018-03-14 13:39:54'),
(94, 'Trainer - ashik has been updated by admin', 'http://localhost:8000/admin/trainers/1', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-14 13:52:39', '2018-03-14 13:52:39'),
(95, 'Trainer - ashik has been updated by admin', 'http://localhost:8000/admin/trainers/1', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-14 13:53:03', '2018-03-14 13:53:03'),
(96, 'Trainer - ashik has been updated by admin', 'http://localhost:8000/admin/trainers/1', 'PATCH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0', 1, 0, 0, 0, '2018-03-14 13:53:36', '2018-03-14 13:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2018_02_04_095824_create_activities_table', 1),
(4, '2018_02_04_124534_create_modules_table', 1),
(6, '2018_02_04_125057_create_areas_tables', 1),
(7, '2018_02_05_141417_create_permissions_table', 1),
(8, '2018_02_18_165129_create_importdata_tables_table', 1),
(9, '2018_02_27_093326_create_log_activity_table', 1),
(11, '2018_02_27_133555_create_imported_files_table', 1),
(13, '2018_02_27_145448_create_packages_table', 2),
(16, '2018_02_27_171241_create_registered_users_table', 5),
(17, '2018_02_28_120410_create_add_column_registeredusers_table', 6),
(18, '2018_03_01_113138_create_cmspages_table', 7),
(19, '2018_03_01_120606_create_contactus_table', 8),
(20, '2018_03_01_145501_create_vendors_table', 9),
(21, '2018_03_03_102752_create_vendor_branches_table', 10),
(22, '2018_03_03_152726_create_language_management_table', 11),
(23, '2018_03_03_170516_create_vendor_softdelete_table', 12),
(24, '2018_03_04_135416_create_registered_users_softdelete_table', 13),
(54, '2018_03_06_105049_create_email_templates_table', 14),
(60, '2018_03_07_131939_create_notifications_table', 15),
(61, '2018_03_08_125201_create_knet_payments_table', 15),
(62, '2018_03_08_125225_create_cc_payments_table', 15),
(67, '2018_03_08_141258_create_payment_details_table', 16),
(68, '2018_03_08_150601_create_subscribers_package_details_table', 16),
(69, '2018_02_27_132814_create_trainers_table', 17),
(70, '2018_03_06_153225_create_transactions_table', 17),
(71, '2018_03_06_155711_create_transactions_softdelete_table', 18),
(72, '2018_03_04_160013_create_trainer_softdelete_table', 19);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name_en`, `name_ar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Subscription /Renewal', 'Subscription /Renewal', 1, '2018-02-27 11:53:39', '2018-02-28 07:50:35'),
(2, 'Classes', 'Classes', 1, '2018-03-01 12:45:17', '2018-03-01 12:45:17'),
(3, 'Fitflow Membership', 'Fitflow Membership', 1, '2018-03-01 12:45:33', '2018-03-01 12:45:33'),
(4, 'E-store', 'E-store', 1, '2018-03-01 12:45:46', '2018-03-01 12:45:46');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `to` tinyint(1) DEFAULT NULL COMMENT 'All Users:0,Registered Users:1,Android Users:2,Iphone Users:3',
  `subject` text COLLATE utf8_unicode_ci,
  `subject_ar` text COLLATE utf8_unicode_ci,
  `message` text COLLATE utf8_unicode_ci,
  `message_ar` text COLLATE utf8_unicode_ci,
  `link` text COLLATE utf8_unicode_ci,
  `notification_date` date DEFAULT NULL,
  `sent_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `num_points` int(11) NOT NULL COMMENT 'Unlimited:0',
  `price` decimal(10,3) NOT NULL,
  `num_days` int(11) NOT NULL,
  `expired_notify_duration` int(11) NOT NULL,
  `description_en` text COLLATE utf8_unicode_ci,
  `description_ar` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name_en`, `name_ar`, `num_points`, `price`, `num_days`, `expired_notify_duration`, `description_en`, `description_ar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'premium', 'premium', 0, '10.000', 30, 7, '', '', 1, '2018-02-27 13:56:19', '2018-03-03 12:20:59'),
(2, 'Standard', 'Standard', 5, '2.500', 25, 10, '', '', 0, '2018-02-27 14:06:14', '2018-03-14 12:27:16');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `subscriber_id` int(10) UNSIGNED DEFAULT NULL,
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `module_id` int(10) UNSIGNED DEFAULT NULL,
  `vendor_id` int(10) UNSIGNED DEFAULT NULL,
  `trainer_id` int(10) UNSIGNED DEFAULT NULL,
  `payment_route` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `amount` decimal(10,3) NOT NULL,
  `post_date` date DEFAULT NULL,
  `result` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payid` bigint(20) UNSIGNED DEFAULT NULL,
  `card_type` tinyint(1) DEFAULT NULL COMMENT 'KNET:1,CC:2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payment_details`
--

INSERT INTO `payment_details` (`id`, `subscriber_id`, `package_id`, `module_id`, `vendor_id`, `trainer_id`, `payment_route`, `reference_id`, `amount`, `post_date`, `result`, `payid`, `card_type`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 2, NULL, NULL, 1, '50.000', '2018-03-11', 'Captured', 1, 1, '2018-03-10 21:00:00', '2018-03-10 21:00:00'),
(2, 2, 1, 1, 2, NULL, '2', 22, '80.000', '2018-01-15', 'approved', 2, 1, '2018-01-14 21:00:00', '2018-01-14 21:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `groupname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `groupname`, `permissions`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Supervisor', '["users-view","users-create","users-edit","users-delete","master-view","master-create","master-edit","master-delete","dashboard-view"]', 1, '2018-02-05 18:00:00', '2018-02-15 02:58:56'),
(2, 'Manager', '["users-create"]', 1, '2018-02-14 03:36:10', '2018-02-17 06:42:16'),
(3, 'users', '["master-view","master-create","master-edit","master-delete"]', 1, '2018-02-14 06:36:41', '2018-02-15 02:59:44'),
(4, 'Accountant', '["users-view"]', 1, '2018-02-15 03:00:14', '2018-02-15 03:00:14');

-- --------------------------------------------------------

--
-- Table structure for table `push_registration`
--

CREATE TABLE `push_registration` (
  `id` int(11) NOT NULL,
  `user_id` text COLLATE utf8_unicode_ci NOT NULL,
  `gcm_id` text COLLATE utf8_unicode_ci NOT NULL,
  `mobile_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `imei` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `push_report`
--

CREATE TABLE `push_report` (
  `id` int(11) NOT NULL,
  `push_id` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` text COLLATE utf8_unicode_ci NOT NULL,
  `gcm_status` text COLLATE utf8_unicode_ci NOT NULL,
  `new_registration_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` text COLLATE utf8_unicode_ci NOT NULL,
  `delivered_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `registered_users`
--

CREATE TABLE `registered_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `civilid` text COLLATE utf8_unicode_ci,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `profile_image` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `area_id` int(11) NOT NULL,
  `gender_id` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `registered_users`
--

INSERT INTO `registered_users` (`id`, `name`, `username`, `email`, `password`, `original_password`, `civilid`, `mobile`, `profile_image`, `status`, `remember_token`, `created_at`, `updated_at`, `area_id`, `gender_id`, `dob`, `deleted_at`) VALUES
(1, 'Noor', NULL, 'noor@g.com', '$2y$10$T0jByoAzzjNUdQBftMgbs.zqnzQIemnAiqDvyWpSHbxilykkFbLNC', '12345', NULL, '12345678', NULL, 1, NULL, '2018-02-28 10:26:55', '2018-03-08 09:26:09', 1, 1, '2004-02-26', NULL),
(2, 'abbas', NULL, 'abbas@gmail.com', '$2y$10$pdMGAIV1ZcH4P8cJJQorsu/fQTx7jpjJ1JP2yjjw7GejI1RXydrpO', '12345', NULL, '12345677', NULL, 1, NULL, '2018-03-01 08:29:29', '2018-03-11 10:54:39', 1, 1, '2009-02-11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscribers_package_details`
--

CREATE TABLE `subscribers_package_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `subscriber_id` int(10) UNSIGNED DEFAULT NULL,
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `module_id` int(10) UNSIGNED DEFAULT NULL,
  `vendor_id` int(10) UNSIGNED DEFAULT NULL,
  `trainer_id` int(10) UNSIGNED DEFAULT NULL,
  `payment_id` int(10) UNSIGNED DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8_unicode_ci,
  `description_ar` text COLLATE utf8_unicode_ci,
  `num_points` tinyint(1) DEFAULT NULL COMMENT 'Unlimited:0',
  `price` decimal(10,3) NOT NULL,
  `commission` decimal(10,3) NOT NULL,
  `num_days` int(11) DEFAULT NULL,
  `notification_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subscribers_package_details`
--

INSERT INTO `subscribers_package_details` (`id`, `subscriber_id`, `package_id`, `module_id`, `vendor_id`, `trainer_id`, `payment_id`, `name_en`, `name_ar`, `description_en`, `description_ar`, `num_points`, `price`, `commission`, `num_days`, `notification_date`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 2, NULL, 1, 'package1', 'package1', 'package1', 'package1', 2, '50.000', '10.000', 20, '2018-03-29', '2018-03-11', '2018-03-11', '2018-03-10 21:00:00', '2018-03-10 21:00:00'),
(2, 2, 1, 1, 2, NULL, 2, 'package3', 'package3', 'package3', 'package3', 3, '80.000', '20.000', 45, '2018-03-07', '2018-01-15', '2018-03-13', '2018-01-14 21:00:00', '2018-01-14 21:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `civilid` text COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commission` decimal(10,3) NOT NULL,
  `activities` text COLLATE utf8_unicode_ci,
  `profile_image` text COLLATE utf8_unicode_ci,
  `acc_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acc_num` bigint(20) DEFAULT NULL,
  `ibn_num` bigint(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `name`, `username`, `email`, `password`, `original_password`, `civilid`, `mobile`, `commission`, `activities`, `profile_image`, `acc_name`, `acc_num`, `ibn_num`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ashik', 'ashik', 'ashik@gmail.com', '$2y$10$bPpAaLt4Z3SxO.6edhnHlOpEDkf6h3szCBLYK4Ehz362.bOi3JlcG', '12345', '123456789123', '12345678', '10.000', '["1"]', '1521035616.jpg', '33', 442, 3244, 1, NULL, '2018-03-14 13:39:54', '2018-03-14 13:53:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `vendor_id` int(10) UNSIGNED DEFAULT NULL,
  `trainer_id` int(10) UNSIGNED DEFAULT NULL,
  `user_type` int(10) UNSIGNED NOT NULL COMMENT 'Vendor:1,Trainer:2',
  `amount` decimal(10,3) NOT NULL,
  `transferred_date` date DEFAULT NULL,
  `attachment` text COLLATE utf8_unicode_ci,
  `comment` text COLLATE utf8_unicode_ci,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acc_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acc_num` bigint(20) DEFAULT NULL,
  `ibn_num` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `vendor_id`, `trainer_id`, `user_type`, `amount`, `transferred_date`, `attachment`, `comment`, `email`, `name`, `mobile`, `acc_name`, `acc_num`, `ibn_num`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 2, NULL, 1, '4.000', '2018-01-02', '1520843004.jpg', '', 'hash@gmail.com', 'hashim', NULL, '123', 1234567891234567, 1234567891234567, '2018-03-12 08:23:24', '2018-03-12 08:25:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `civilid` text COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_role_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `original_password`, `civilid`, `mobile`, `user_role_id`, `permission_id`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Superadmin', 'admin', 'sagirhashim@gmail.com', '$2y$10$JcJgD7joP1aepRPE4ETyOO8a1dReljYnjAlSGqMrfFMETMLuuVWxW', '234567', '', '12345678', 1, 2, 1, 'HQXzTz61fzXZKmDkGKmZ11hexbE9yHe6Pbo61mdrG5L38HamMltScqiBOlqw', NULL, '2018-03-15 07:05:56'),
(2, 'narendra', 'narendra', 'n@gmail.com', '$2y$10$8tH4UhSOtshCpYX8c6F2GexjwQvtmNtIVVCoVHCfV5X30g4gV/3Pq', '12345', '', '', 2, 4, 1, 'q9Ml2biowtHvsAIptdbn8XVF8snbuXmdOOjFQn6jOejLmxXh531ow3Q2AO98', '2018-02-27 11:50:56', '2018-03-04 13:43:41'),
(3, 'salman', 'salman', 'salman@gmail.com', '$2y$10$llQFuPNYhJH.0n98ShGh0e3QfYiEGQ5bv45nA4blW8Cu7Hc1f5U3e', '12345', '', '', 2, 4, 1, NULL, '2018-02-28 08:27:13', '2018-03-04 13:41:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `name`, `status`) VALUES
(1, 'superadmin', 1),
(2, 'supervisor', 1),
(3, 'manager', 1),
(4, 'viewer', 0),
(5, 'Employee', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `civilid` text COLLATE utf8_unicode_ci,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acc_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `acc_num` bigint(11) NOT NULL,
  `ibn_num` bigint(11) NOT NULL,
  `commission` text COLLATE utf8_unicode_ci,
  `profile_image` text COLLATE utf8_unicode_ci,
  `modules` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `username`, `email`, `password`, `original_password`, `civilid`, `mobile`, `acc_name`, `acc_num`, `ibn_num`, `commission`, `profile_image`, `modules`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'hashim', 'hash', 'hash@gmail.com', '$2y$10$SRYbyUVC9/SF93Jawtoefe2nL6bwaohoNIqZt8y3v9ivE3x/BLeOq', '12345', NULL, NULL, '123', 1234567891234567, 1234567891234567, '{"1":"100","2":"20","3":"30","4":"40"}', '1520171917.jpg', '["2"]', 1, NULL, '2018-03-03 03:44:57', '2018-03-04 13:58:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_branches`
--

CREATE TABLE `vendor_branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `vendor_id` int(10) UNSIGNED NOT NULL,
  `gender_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_person_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_person_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `office_time` text COLLATE utf8_unicode_ci,
  `area` int(11) NOT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `latitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `longitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `contact` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `vendor_branches`
--

INSERT INTO `vendor_branches` (`id`, `vendor_id`, `gender_type`, `name_en`, `name_ar`, `contact_person_en`, `contact_person_ar`, `office_time`, `area`, `address`, `latitude`, `longitude`, `status`, `contact`, `created_at`, `updated_at`) VALUES
(2, 2, '1', 'Hawally', 'Hawally', 'imran', 'imran', '11AM -12 pm', 1, NULL, '29.37859', '47.99034', 1, '', '2018-03-04 10:47:47', '2018-03-04 10:47:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_modules`
--
ALTER TABLE `admin_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `areas_governorate_id_foreign` (`governorate_id`);

--
-- Indexes for table `backup_lists`
--
ALTER TABLE `backup_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cc_payments`
--
ALTER TABLE `cc_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cmspages`
--
ALTER TABLE `cmspages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contactus_email_unique` (`email`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gender_types`
--
ALTER TABLE `gender_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `governorates`
--
ALTER TABLE `governorates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `importdata_tables`
--
ALTER TABLE `importdata_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `imported_files`
--
ALTER TABLE `imported_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `knet_payments`
--
ALTER TABLE `knet_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language_management`
--
ALTER TABLE `language_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_activities`
--
ALTER TABLE `log_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_details_subscriber_id_index` (`subscriber_id`),
  ADD KEY `payment_details_package_id_index` (`package_id`),
  ADD KEY `payment_details_module_id_index` (`module_id`),
  ADD KEY `payment_details_vendor_id_index` (`vendor_id`),
  ADD KEY `payment_details_trainer_id_index` (`trainer_id`),
  ADD KEY `payment_details_payid_index` (`payid`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registered_users`
--
ALTER TABLE `registered_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registered_users_email_unique` (`email`),
  ADD UNIQUE KEY `registered_users_mobile_unique` (`mobile`);

--
-- Indexes for table `subscribers_package_details`
--
ALTER TABLE `subscribers_package_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscribers_package_details_subscriber_id_index` (`subscriber_id`),
  ADD KEY `subscribers_package_details_package_id_index` (`package_id`),
  ADD KEY `subscribers_package_details_module_id_index` (`module_id`),
  ADD KEY `subscribers_package_details_vendor_id_index` (`vendor_id`),
  ADD KEY `subscribers_package_details_trainer_id_index` (`trainer_id`),
  ADD KEY `subscribers_package_details_payment_id_index` (`payment_id`),
  ADD KEY `subscribers_package_details_notification_date_index` (`notification_date`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trainers_username_unique` (`username`),
  ADD UNIQUE KEY `trainers_email_unique` (`email`),
  ADD KEY `trainers_mobile_index` (`mobile`),
  ADD KEY `trainers_acc_num_index` (`acc_num`),
  ADD KEY `trainers_ibn_num_index` (`ibn_num`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_vendor_id_foreign` (`vendor_id`),
  ADD KEY `transactions_trainer_id_foreign` (`trainer_id`),
  ADD KEY `transactions_mobile_index` (`mobile`),
  ADD KEY `transactions_acc_name_index` (`acc_name`),
  ADD KEY `transactions_acc_num_index` (`acc_num`),
  ADD KEY `transactions_ibn_num_index` (`ibn_num`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_username_unique` (`username`),
  ADD UNIQUE KEY `vendors_email_unique` (`email`),
  ADD KEY `vendors_mobile_index` (`mobile`);

--
-- Indexes for table `vendor_branches`
--
ALTER TABLE `vendor_branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_branches_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendor_branches_gender_type_index` (`gender_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `admin_modules`
--
ALTER TABLE `admin_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `backup_lists`
--
ALTER TABLE `backup_lists`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `cc_payments`
--
ALTER TABLE `cc_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cmspages`
--
ALTER TABLE `cmspages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `gender_types`
--
ALTER TABLE `gender_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `governorates`
--
ALTER TABLE `governorates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `importdata_tables`
--
ALTER TABLE `importdata_tables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `imported_files`
--
ALTER TABLE `imported_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `knet_payments`
--
ALTER TABLE `knet_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `language_management`
--
ALTER TABLE `language_management`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `log_activities`
--
ALTER TABLE `log_activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `registered_users`
--
ALTER TABLE `registered_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `subscribers_package_details`
--
ALTER TABLE `subscribers_package_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `vendor_branches`
--
ALTER TABLE `vendor_branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `areas_governorate_id_foreign` FOREIGN KEY (`governorate_id`) REFERENCES `governorates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD CONSTRAINT `payment_details_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`),
  ADD CONSTRAINT `payment_details_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `subscribers_package_details`
--
ALTER TABLE `subscribers_package_details`
  ADD CONSTRAINT `subscribers_package_details_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payment_details` (`id`),
  ADD CONSTRAINT `subscribers_package_details_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`),
  ADD CONSTRAINT `subscribers_package_details_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`),
  ADD CONSTRAINT `transactions_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `vendor_branches`
--
ALTER TABLE `vendor_branches`
  ADD CONSTRAINT `vendor_branches_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
