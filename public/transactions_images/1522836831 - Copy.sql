-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2018 at 09:56 AM
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
-- Table structure for table `vendor_admin_modules`
--

CREATE TABLE `vendor_admin_modules` (
  `id` int(11) NOT NULL,
  `vendor_module_id` int(11) NOT NULL,
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
-- Dumping data for table `vendor_admin_modules`
--

INSERT INTO `vendor_admin_modules` (`id`, `vendor_module_id`, `module`, `module_prefix`, `view`, `created`, `edit`, `deleted`, `upload`, `print`, `status`, `sort_order`) VALUES
(1, 1, 'users', 'users', 1, 1, 1, 1, 0, 0, 1, 1),
(2, 1, 'master records', 'master', 1, 1, 1, 1, 0, 0, 0, 2),
(3, 1, 'dashboard', 'dashboard', 1, 0, 0, 0, 0, 0, 1, 3),
(4, 1, 'permissions', 'permissions', 1, 1, 1, 1, 0, 0, 1, 4),
(5, 1, 'trainers', 'trainers', 1, 1, 1, 1, 0, 0, 0, 5),
(6, 1, 'logActivity', 'logActivity', 1, 0, 0, 0, 0, 0, 1, 6),
(7, 1, 'registeredUsers', 'registeredUsers', 1, 1, 1, 1, 0, 0, 0, 7),
(8, 1, 'cmsPages', 'cmsPages', 1, 0, 1, 0, 0, 0, 0, 8),
(9, 1, 'packages', 'packages', 1, 1, 1, 1, 0, 0, 1, 9),
(10, 1, 'contactus', 'contactus', 1, 1, 1, 1, 0, 0, 0, 10),
(11, 1, 'languageManagement', 'languageManagement', 1, 1, 1, 1, 0, 0, 0, 11),
(12, 1, 'incomeStatistics', 'incomeStatistics', 1, 0, 0, 0, 0, 0, 1, 12),
(13, 1, 'vendorPackages', 'vendorPackages', 1, 0, 0, 0, 0, 0, 0, 13),
(14, 1, 'classPackages', 'classPackages', 1, 0, 0, 0, 0, 0, 0, 14),
(15, 1, 'classes', 'classes', 1, 0, 0, 0, 0, 0, 0, 15),
(16, 1, 'archivedClasses', 'archivedClasses', 1, 0, 0, 0, 0, 0, 0, 16),
(17, 1, 'notifications', 'notifications', 1, 1, 1, 1, 0, 0, 0, 17),
(18, 1, 'transactions', 'transactions', 1, 1, 1, 1, 0, 0, 0, 18),
(19, 1, 'settings', 'settings', 0, 0, 0, 0, 0, 0, 0, 19),
(20, 1, 'importexportdata', 'importexportdata', 0, 0, 0, 0, 0, 0, 0, 20),
(21, 1, 'backup', 'backup', 0, 0, 0, 0, 0, 0, 0, 21),
(22, 1, 'trainerPackages', 'trainerPackages', 1, 0, 0, 0, 0, 0, 0, 22);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_admin_modules`
--
ALTER TABLE `vendor_admin_modules`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_admin_modules`
--
ALTER TABLE `vendor_admin_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
