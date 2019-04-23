-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2019 at 05:37 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mahir_floral`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_key`
--

CREATE TABLE IF NOT EXISTS `api_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=19 ;

--
-- Dumping data for table `api_key`
--

INSERT INTO `api_key` (`id`, `access_token`, `user_id`, `last_used`, `created`) VALUES
(1, 'WGavnGxMNFqtDZhJLHbF1mFBt3nVlcCKYZr0Se0E9hPYe6zEiZ', 1, '2019-03-28 06:50:47', '2019-03-28 06:50:00'),
(2, 'Bv7AH7X4s3uZPUsGjL3YPd0eTmxisNmvYCIHrphejgjWxNvbC4', 1, '2019-03-30 15:33:49', '2019-03-28 16:30:04'),
(3, 'smQygJ3UTByxFrrHqdi5wodVNWNVAk8kKxPPC1XE2K8wmcqFpk', 1, '2019-03-30 15:50:36', '2019-03-30 16:35:31'),
(4, 'smKcsnlToEx6s2honT4Tn3QabR5kZ9LXwhla15OxbUB9eBICTu', 3, '2019-03-30 15:47:56', '2019-03-30 16:38:38'),
(5, 'hHqwflTu0lsa3NlplhhxmTtp3eFmTn3fSXlor1NFyc5oIztqRI', 1, '2019-03-30 16:03:04', '2019-03-30 16:48:28'),
(6, 'D6uLPgxgKgvlBWkA9Ev5zdZqBZfItOgyS2u02vUGHyYG21FBOh', 4, '2019-03-30 15:49:25', '2019-03-30 16:49:17'),
(7, '3dsLrnFsXf8AMFSKEZj3N2s5UbJVef9btA9WQ3IO5XjRpbTHv3', 4, '2019-04-02 15:40:27', '2019-04-02 15:40:27'),
(8, 'EqWmjhOTXWu45taJDuAe54XpqOZC6NVG5EsXQrOhmLI05IoY0U', 1, '2019-04-12 14:47:01', '2019-04-12 15:39:33'),
(9, 'k0mmpxF85JiCbyPv4sn3yqvWZVlpfVtbDSYs1MsYK50VELd34h', 5, '2019-04-12 15:42:16', '2019-04-12 15:42:16'),
(10, 'xwzpouLOFf2ezBofQVgXG3DXPkYltAfHmbV7HXYcdfR26Uze6h', 5, '2019-04-12 14:47:46', '2019-04-12 15:47:45'),
(11, 'nYjs67aTI7lbVkipYAZazfUzV90bLDL7jy9YgeCPRx9hPNdqmR', 5, '2019-04-23 14:18:26', '2019-04-18 16:03:06'),
(12, 'WDQXzcuubQgP0pbAKfGQDcKdnCPLblV6NHukpyt2Lfni4C8J30', 3, '2019-04-18 20:23:51', '2019-04-18 16:49:47'),
(13, 'fpUfFWoISqGePrw9Z48ilWd2l80cYed8Hl4Gt3hbMr3vB88Xnc', 3, '2019-04-18 20:30:53', '2019-04-18 17:15:10'),
(14, 'xwa6jMW1JDNxusaKQ52ftRS7DCEY1IPTRZnuRMLPgWYwLOsEAe', 5, '2019-04-22 11:05:03', '2019-04-22 12:03:39'),
(15, 'wBkwOVz7xSJ92UUO9ZGPeC8q6KTj7P1I8Q7lthir057H2IIlsa', 5, '2019-04-23 15:07:44', '2019-04-23 15:07:44'),
(16, 'Pr7QVmkuplYkgZNGYoZpu9jQhlXPkzDH5WAkx1w18VEFRfIza4', 1, '2019-04-23 14:10:43', '2019-04-23 15:10:30'),
(17, 'UA20X3W74QQ10gdOWb14BWJTdmjTvOe8G8tetEug4efQede39q', 7, '2019-04-23 14:32:28', '2019-04-23 15:16:40'),
(18, 'E5zU2afTsiOA0G7Yh3vvnCPaT7qpTK8e0ei4jNLUp9SdnwtXPD', 5, '2019-04-23 14:25:19', '2019-04-23 15:17:32');

-- --------------------------------------------------------

--
-- Table structure for table `on_demand`
--

CREATE TABLE IF NOT EXISTS `on_demand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `demanded_quantity` int(11) NOT NULL,
  `priority` enum('high','medium','low','') NOT NULL,
  `demanded_shop` int(11) NOT NULL,
  `demanded_by` int(11) NOT NULL,
  `demanded_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `on_demand`
--

INSERT INTO `on_demand` (`id`, `product_id`, `demanded_quantity`, `priority`, `demanded_shop`, `demanded_by`, `demanded_date`) VALUES
(1, 1, 1, 'high', 2, 7, '2019-04-23 14:32:16'),
(2, 2, 1, 'high', 2, 7, '2019-04-23 14:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `raw_stock`
--

CREATE TABLE IF NOT EXISTS `raw_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `received_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `color` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `added_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ready_stock`
--

CREATE TABLE IF NOT EXISTS `ready_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `color` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `added_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ready_stock`
--

INSERT INTO `ready_stock` (`id`, `product_name`, `quantity`, `unit`, `price`, `date`, `color`, `comment`, `added_by`) VALUES
(1, 'Booking Shop', 3, '45', 12, '2019-04-18 16:23:33', '234', 'asd', 5),
(2, 'Booking Shop', 4, '45', 12, '2019-04-23 15:18:26', '234', 'asd', 5);

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE IF NOT EXISTS `shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loc_lat` double NOT NULL,
  `loc_long` double NOT NULL,
  `added_by` int(11) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`id`, `name`, `address`, `loc_lat`, `loc_long`, `added_by`, `creation_date`) VALUES
(1, 'Booking Shop', 'axyz', 45, 234, 0, '2019-03-28 06:50:42'),
(2, 'outlet 01', 'axyz', 45, 234, 0, '2019-03-30 16:36:03'),
(3, 'outlet 01', 'axyz', 45, 234, 0, '2019-03-30 16:50:36'),
(4, 'HQ', 'axyz', 45, 234, 0, '2019-04-12 15:40:19');

-- --------------------------------------------------------

--
-- Table structure for table `shop_stock`
--

CREATE TABLE IF NOT EXISTS `shop_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `delivery_status` int(11) NOT NULL,
  `delivery_by` int(11) NOT NULL,
  `received_by` int(11) DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `received_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `shop_stock`
--

INSERT INTO `shop_stock` (`id`, `shop_id`, `product_id`, `quantity`, `delivery_status`, `delivery_by`, `received_by`, `comment`, `delivery_date`, `received_date`) VALUES
(1, 2, 1, 1, 1, 5, 3, 'test', '2019-04-18 15:24:34', '2019-04-18 16:06:59');

-- --------------------------------------------------------

--
-- Table structure for table `sold_stock`
--

CREATE TABLE IF NOT EXISTS `sold_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `sell_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `seller_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sold_stock`
--

INSERT INTO `sold_stock` (`id`, `shop_stock_id`, `product_id`, `quantity`, `sell_date`, `comment`, `seller_by`) VALUES
(1, 1, 1, 2, '2019-04-18 16:15:45', 'no comment', 3),
(2, 1, 1, 2, '2019-04-18 16:20:52', 'no comment', 3),
(3, 1, 1, 2, '2019-04-18 20:30:48', 'no comment', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Raw Stock','Shop Stock') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = Online,0 = Offline',
  `added_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `salt`, `password`, `role`, `is_online`, `added_by`, `updated_by`, `creation_date`, `status`) VALUES
(1, '', 'admin@gmail.com', 'c39c3f4af59410814a30e3ea2f9882b022c671eaaef3acc7512fe89e57ebf0bfdbcd65bb686aba5f643e2963008787211b7131dcd1911859f480b7d6c252c527', '40c915eb86885ec5033397f4f668dfd270963564bae5f1c720cc195aac42428c045cf76c0cf39bc82e6e9b8d375e153d44bc5ca5890707a17aff7d3545056eaf', 'Admin', 1, NULL, NULL, '2019-04-23 15:10:30', 'Active'),
(2, 'Ali', 'al11i@gmail.com', 'e71dc4d31bad69e74affe071d4a4bbee08c16cf4cc4fb480e2293293eb4c6a210d502fa98224b9c659fd8b994b72c28ade45fb94d3f3766a1d626ffd2d5e1240', 'c30b22b7189371e7c22d4dd928186d3661969079e7e3c085bcb4170c348e097f69acd4f1ba4eb2590d322919369813b519e10dc953a324054606753ca742f682', 'Raw Stock', 0, 1, 1, '2019-03-28 06:50:47', 'Active'),
(3, 'Ali', 'staff01@gmail.com', '7d6722043bc64c290342d4b23c4fb6773f788826ec046d9f150b3a9fe40c5f632c537a6846c32a390695b8c8a677c0edc313a4152c6a1de08e88809f50b6c655', '281f8d339178c6a920a0aad2d5e06132f22acc734d2bbf9348151bde7fa8bfe5f7e028279ae7dd0f7fbd331679b4f474aea7b2b9808af865730cdc984809fbff', 'Shop Stock', 1, 1, 1, '2019-04-18 16:49:47', 'Active'),
(4, 'Ali', 'staff02@gmail.com', '8c08157632dabd4dd131735461ab2d000b812e58d604efbdcfca9abb15aad19c0055b508b0759b8a1053896defacc70206386d284ded6ee25947989e53cf2c12', 'b8119389f5c5e601909c2bbe0547f2ec6b007e3151ef8cfe3a843a376ed409ae3ad9def8f0e36b2340d31bcf5a206e656065466929d5c12f018e17337f580ffc', 'Shop Stock', 0, 1, 1, '2019-04-12 16:01:01', 'Active'),
(5, 'Mohit Ali', 'staff_raw@gmail.com', '06a8ccb648a2f74fd5631c4d40f861e171e30829f35c8cd1e8517307a776a5b0f7e1368a2deb0cda79e08d45db3c9c0e75c7a92daba48de11cbd1f59fc17338f', 'e556ca03f802067036fa6045025cd3894626252f174cc0460b61d440fdd89b8104780f5cc5a931e60c922e78ffc37fd12c37eabf25c1bc82c6444e2fecfc1537', 'Raw Stock', 1, 1, 1, '2019-04-18 16:03:06', 'Active'),
(6, 'Nuhas Humayun', 'staff_raw02@gmail.com', '2c24bdb77f90f720dc11be8365da523ce541bbd8f425fb06fa33e333fdb882c3cfafec8f0e6a78c6c2a3c0e2e8fb0b472a4c1b72d4fd95052c87eeca233a53e8', '6c685f16f02f97db1b09f60156e22e0e73cd95cdce61ca037775abbd96070872104e0604f83b3884274f161da5ea2509594fe2ae8fa31b0cb63e65f3fada48e9', 'Raw Stock', 0, 1, 1, '2019-04-12 14:47:01', 'Active'),
(7, 'Nuhas Humayun', 'staff_shop@gmail.com', '1bf17719225ae16e74e5c84e049ad53253e654870f95690b19bf42cb8f9a8853fa09bd74abf7423b4a3b430cd86968dd97b3e87e974ba24c4ab0c281f70d14a3', '9b5e78599dfbbec408104d85a4ef96f8ba98e433f3d984e0b5e36d73a32e2bb5c7518212e48ee967cd73caaf12864d0c2a4ffa0c79b07c3c3b85d79e27f03024', 'Shop Stock', 1, 1, 1, '2019-04-23 15:16:40', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `user_check`
--

CREATE TABLE IF NOT EXISTS `user_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `check_in` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `check_out` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user_check`
--

INSERT INTO `user_check` (`id`, `user_id`, `check_in`, `check_out`) VALUES
(1, 2, '2019-04-02 14:42:08', '2019-04-02 14:42:23'),
(2, 3, '2019-04-02 14:43:43', '2019-04-02 14:43:55'),
(3, 4, '2019-04-02 14:52:24', NULL),
(4, 5, '2019-04-12 15:00:30', '2019-04-12 15:01:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_shop`
--

CREATE TABLE IF NOT EXISTS `user_shop` (
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  KEY `FK_user_shop_user_id` (`user_id`),
  KEY `FK_user_shop_shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_shop`
--

INSERT INTO `user_shop` (`user_id`, `shop_id`) VALUES
(4, 2),
(5, 4),
(6, 4),
(7, 2);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_shop`
--
ALTER TABLE `user_shop`
  ADD CONSTRAINT `FK_user_shop_shop_id` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_user_shop_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
