-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 29, 2023 at 01:12 PM
-- Server version: 10.6.14-MariaDB
-- PHP Version: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wffbnbcg_dem982`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_core_menus`
--

CREATE TABLE `engine4_core_menus` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type` enum('standard','hidden','custom') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'standard',
  `title` varchar(64) NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT 999
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `engine4_core_menus`
--

INSERT INTO `engine4_core_menus` (`id`, `name`, `type`, `title`, `order`) VALUES
(1, 'core_main', 'standard', 'Main Navigation Menu', 1),
(2, 'core_mini', 'standard', 'Mini Navigation Menu', 2),
(3, 'core_footer', 'standard', 'Footer Menu', 3),
(4, 'core_sitemap', 'standard', 'Sitemap', 4),
(5, 'user_home', 'standard', 'Member Home Quick Links Menu', 999),
(6, 'user_profile', 'standard', 'Member Profile Options Menu', 999),
(7, 'user_edit', 'standard', 'Member Edit Profile Navigation Menu', 999),
(8, 'user_browse', 'standard', 'Member Browse Navigation Menu', 999),
(9, 'user_settings', 'standard', 'Member Settings Navigation Menu', 999),
(10, 'messages_main', 'standard', 'Messages Main Navigation Menu', 999),
(11, 'video_main', 'standard', 'Video Main Navigation Menu', 999),
(12, 'poll_main', 'standard', 'Poll Main Navigation Menu', 999),
(13, 'poll_quick', 'standard', 'Poll Quick Navigation Menu', 999),
(14, 'mobi_footer', 'standard', 'Mobile Footer Menu', 999),
(15, 'mobi_main', 'standard', 'Mobile Main Menu', 999),
(16, 'mobi_profile', 'standard', 'Mobile Profile Options Menu', 999),
(17, 'mobi_browse', 'standard', 'Mobile Browse Page Menu', 999),
(18, 'group_main', 'standard', 'Group Main Navigation Menu', 999),
(19, 'group_profile', 'standard', 'Group Profile Options Menu', 999),
(20, 'album_main', 'standard', 'Album Main Navigation Menu', 999),
(21, 'album_quick', 'standard', 'Album Quick Navigation Menu', 999),
(22, 'sitestoreproduct_main', 'standard', 'Stores - Store Main Navigation Menu', 999),
(23, 'sitestore_quick', 'standard', 'Stores - Store Quick Navigation Menu', 999),
(24, 'sitestore_gutter', 'standard', 'Stores - Store Profile Options Menu', 999),
(25, 'sitestoreproduct_gutter', 'standard', 'Products Profile Page Options Menu', 999),
(26, 'sitestoreproduct_dashboard', 'standard', 'Stores - Product Dashboard Menu', 999),
(27, 'sitestore_dashboard', 'standard', 'Stores - Store Dashboard Menu', 999),
(29, 'sitealbum_main', 'standard', 'Advanced Albums - Albums Main Navigation Menu', 999),
(30, 'sitealbum_quick', 'standard', 'Advanced Albums - Albums Quick Navigation Menu', 999),
(31, 'album_profile', 'standard', 'Advanced Albums - Albums Profile Options Menu', 999),
(32, 'core_social_sites', 'standard', 'Social Site Links Menu', 5),
(33, 'sesalbum_main', 'standard', 'SES Advanced Photos - Album Main Navigation Menu', 999),
(34, 'sesalbum_quick', 'standard', 'SES Advanced Photos - Album Quick Navigation Menu', 999);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `engine4_core_menus`
--
ALTER TABLE `engine4_core_menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `order` (`order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `engine4_core_menus`
--
ALTER TABLE `engine4_core_menus`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
