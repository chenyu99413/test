-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 23, 2019 at 04:24 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ali1688`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_routes`
--

CREATE TABLE `tb_routes` (
  `id` int(11) NOT NULL,
  `tracking_no` varchar(32) NOT NULL COMMENT '运单号',
  `md5` varchar(32) NOT NULL COMMENT 'md5(运单号+轨迹时间+轨迹地点+轨迹详情)',
  `time` int(11) NOT NULL COMMENT '轨迹时间',
  `location` varchar(32) NOT NULL COMMENT '轨迹地点',
  `description` text NOT NULL COMMENT '轨迹详情',
  `create_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道轨迹跟踪明细';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_routes`
--
ALTER TABLE `tb_routes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `md5` (`md5`) USING BTREE,
  ADD KEY `tracking_no` (`tracking_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_routes`
--
ALTER TABLE `tb_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
