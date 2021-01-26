-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 12, 2019 at 05:25 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `ali1688`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_route_matchrules`
--

CREATE TABLE `tb_route_matchrules` (
  `id` int(11) NOT NULL,
  `network_code` varchar(16) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `ali_code` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='轨迹匹配规则';

--
-- Dumping data for table `tb_route_matchrules`
--

INSERT INTO `tb_route_matchrules` (`id`, `network_code`, `keyword`, `ali_code`) VALUES
(1, 'UPS', 'Processed for clearance', 'S_CLEARANCE_COMPLETE'),
(2, 'UPS', 'Clearance event', 'S_CLEARANCE_COMPLETE'),
(3, 'UPS', 'Arrival Scan', 'S_TH_IN'),
(4, 'UPS', 'Departure Scan', 'S_TH_OUT'),
(5, 'UPS', 'Out For Delivery*', 'S_DELIVERY_SCHEDULED'),
(6, 'UPS', 'Destination Scan', 'S_TH_IN_LAST'),
(7, 'UPS', 'DELIVERED*', 'S_DELIVERY_SIGNED'),
(8, 'UPS', '*valid ID*', 'F_CLEARANCE_5037'),
(9, 'UPS', '*Additional documentation is required for clearance*', 'F_CLEARANCE_5037'),
(10, 'UPS', '*Documentation required*', 'F_CLEARANCE_5037'),
(11, 'UPS', '*Power of attorney documentation*', 'F_CLEARANCE_5037'),
(12, 'UPS', '*provide warehouse entry details*', 'F_CLEARANCE_5037'),
(13, 'UPS', '*provide a unique customs identification number*', 'F_CLEARANCE_5037'),
(14, 'UPS', '*import requirements to clear the package*', 'F_CLEARANCE_5037'),
(15, 'UPS', '*missing clearance information*', 'F_CLEARANCE_5037'),
(16, 'UPS', '*clarification on invoice documentation*', 'F_CLEARANCE_5037'),
(17, 'UPS', '*Commodity description information must be validated*', 'F_CLEARANCE_5037'),
(18, 'UPS', '*missing commercial invoice*', 'F_CLEARANCE_5037'),
(19, 'UPS', '*import license*', 'F_CLEARANCE_5037'),
(20, 'UPS', '*merchandise description*', 'F_CLEARANCE_5037'),
(21, 'UPS', '*The receiver had no invoice information for this package*', 'F_CLEARANCE_5037'),
(22, 'UPS', '*The C.O.D. amount is missing*', 'F_CLEARANCE_5037'),
(23, 'UPS', '*Additional information is needed from the importer*', 'F_CLEARANCE_5037'),
(24, 'UPS', '*requiring an invoice comparison*', 'F_CLEARANCE_5037'),
(25, 'UPS', '*FDA clearance and power of attorney required*', 'F_CLEARANCE_5037'),
(26, 'UPS', '*This package requires special entry.*', 'F_CLEARANCE_5037'),
(27, 'UPS', '*multiple packages shipping on the same day*', 'F_CLEARANCE_5037'),
(28, 'UPS', '*packages associated*', 'F_CLEARANCE_5037'),
(29, 'UPS', '*commodity or weight*', 'F_CLEARANCE_5037'),
(30, 'UPS', '*not in the importer\'s database*', 'F_CLEARANCE_5037'),
(31, 'UPS', '*held for payment of duty and tax*', 'F_CLEARANCE_5037'),
(32, 'UPS', '*C.O.D. (ICOD) charges are due*', 'F_CLEARANCE_5037'),
(33, 'UPS', '*Duties or taxes are due on this package*', 'F_CLEARANCE_5037'),
(34, 'UPS', '*We are awaiting payment authorization from the receiver*', 'F_CLEARANCE_5037'),
(35, 'UPS', '*agency is closed*', 'F_CLEARANCE_5037'),
(36, 'UPS', '*mechanical failure has delayed delivery*', 'F_CLEARANCE_5037'),
(37, 'UPS', '*experiencing technical difficulties*', 'F_CLEARANCE_5037'),
(38, 'UPS', '*requested by the receiver, we\'ll hold the package*', 'F_CLEARANCE_5037'),
(39, 'UPS', '*formal entry at the import location*', 'F_CLEARANCE_5037'),
(40, 'UPS', '*not assigned a broker*', 'F_CLEARANCE_5037'),
(41, 'UPS', '*an alternate broker*', 'F_CLEARANCE_5037'),
(42, 'UPS', '*in-bond to a non-UPS off-site*', 'F_CLEARANCE_5037'),
(43, 'UPS', '*requested clearance by a non-UPS broker*', 'F_CLEARANCE_5037'),
(44, 'UPS', '*bond paperwork for alternate site clearance by a non-UPS broker*', 'F_CLEARANCE_5037'),
(45, 'UPS', '*held by brokerage for reasons beyond UPS\' control*', 'F_CLEARANCE_5037'),
(46, 'UPS', '*Your shipment was not processed by UPS brokerage*', 'F_CLEARANCE_5037'),
(47, 'UPS', '*FDA or Department of Agriculture*', 'F_CLEARANCE_5037'),
(48, 'UPS', '*incorrectly sorted*', 'F_DELIVERY_5053'),
(49, 'UPS', '*on the plane as scheduled*', 'F_DELIVERY_5053'),
(50, 'UPS', '*missed the scheduled transfer time*', 'F_DELIVERY_5053'),
(51, 'UPS', '*late UPS trailer arrival has delayed delivery.*', 'F_DELIVERY_5053'),
(52, 'UPS', '*late flight has caused a delay*', 'F_DELIVERY_5053'),
(53, 'UPS', '*A transportation accident has delayed delivery*', 'F_DELIVERY_5053'),
(54, 'UPS', '*Due to operating conditions, your package may be delayed.*', 'F_DELIVERY_5053'),
(55, 'UPS', '*overweight or oversized*', 'F_DELIVERY_5053'),
(56, 'UPS', '*The receiver was not available*', 'F_DELIVERY_5045'),
(57, 'UPS', '*business was closed*', 'F_DELIVERY_5045'),
(58, 'UPS', '*on vacation*', 'F_DELIVERY_5045'),
(59, 'UPS', '*labor dispute*', 'F_DELIVERY_5045'),
(60, 'UPS', '*holiday closures*', 'F_DELIVERY_5045'),
(61, 'UPS', '*security access*', 'F_DELIVERY_5045'),
(62, 'UPS', '*severe weather*', 'F_DELIVERY_5045'),
(63, 'UPS', '*Eurotunnel traffic disruption*', 'F_DELIVERY_5045'),
(64, 'UPS', '*Civil unrest*', 'F_DELIVERY_5045'),
(65, 'UPS', '*Customer not in*', 'F_DELIVERY_5045'),
(66, 'UPS', '*Customer was not available*', 'F_DELIVERY_5045'),
(67, 'UPS', '*Recent weather has caused delivery delays*', 'F_DELIVERY_5045'),
(68, 'UPS', '*held for pickup*', 'F_DELIVERY_5051'),
(69, 'UPS', '*The receiver has requested a delayed delivery*', 'F_DELIVERY_5051'),
(70, 'UPS', '*delivery during certain hours*', 'F_DELIVERY_5051'),
(71, 'UPS', '*hold the package for pickup*', 'F_DELIVERY_5051'),
(72, 'UPS', '*arranged to pick up*', 'F_DELIVERY_5051'),
(73, 'UPS', '*agreed to pickup the package*', 'F_DELIVERY_5051'),
(74, 'UPS', '*delivered to the selected UPS Access Point*', 'F_DELIVERY_5051'),
(75, 'UPS', '*deliver on the date requested*', 'F_DELIVERY_5051'),
(76, 'UPS', '*delivery change*', 'F_CARRIER_PICKUP_RT_5035'),
(77, 'UPS', '*change of delivery*', 'F_CARRIER_PICKUP_RT_5035'),
(78, 'UPS', '*change the delivery*', 'F_CARRIER_PICKUP_RT_5035'),
(79, 'UPS', '*delivery address has been updated*', 'F_CARRIER_PICKUP_RT_5035'),
(80, 'UPS', '*updated address*', 'F_CARRIER_PICKUP_RT_5035'),
(81, 'UPS', '*we have updated the address*', 'F_CARRIER_PICKUP_RT_5035'),
(82, 'UPS', '*Directions to the address are required to complete delivery*', 'F_CARRIER_PICKUP_RT_5035'),
(83, 'UPS', '*The receiver requested an alternate delivery address*', 'F_CARRIER_PICKUP_RT_5035'),
(84, 'UPS', '*updated the delivery information*', 'F_CARRIER_PICKUP_RT_5035'),
(85, 'UPS', '*We are adjusting delivery plans as quickly as possible*', 'F_CARRIER_PICKUP_RT_5035'),
(86, 'UPS', '*Your package was cleared after the scheduled transport departure*', 'F_CARRIER_PICKUP_RT_5035'),
(87, 'UPS', '*unable to collect funds*', 'F_DELIVERY_5050'),
(88, 'UPS', '*a new tracking number was assigned to this package*', 'F_CARRIER_PICKUP_RT_5035'),
(89, 'UPS', '*Delivered to UPS Access Point™*', 'F_DELIVERY_5051'),
(90, 'UPS', '*request to modify the delivery address*', 'F_DELIVERY_5043'),
(91, 'UPS', '*The receiver has moved*', 'F_DELIVERY_5043'),
(92, 'UPS', '*The company or receiver name is incorrect*', 'F_DELIVERY_5043'),
(93, 'UPS', '*The apartment number is either missing or incorrect*', 'F_DELIVERY_5043'),
(94, 'UPS', '*The address is incomplete*', 'F_DELIVERY_5043'),
(95, 'UPS', '*Delivery may be delayed due to an incomplete address*', 'F_DELIVERY_5043'),
(96, 'UPS', '*Address information is missing*', 'F_DELIVERY_5043'),
(97, 'UPS', '*Incomplete address information*', 'F_DELIVERY_5043'),
(98, 'UPS', '*Missing or incorrect apartment number*', 'F_DELIVERY_5043'),
(99, 'UPS', '*The city name in the address is incorrect*', 'F_DELIVERY_5043'),
(100, 'UPS', '*missed you on our final attempt*', 'F_DELIVERY_5045'),
(101, 'UPS', '*The receiver determined this product order is too expensive and refused the delivery*', 'F_DELIVERY_5050'),
(102, 'UPS', '*The receiver disputes or refuses to pay duty or taxes for the package*', 'F_DELIVERY_5050'),
(103, 'UPS', '*The receiver doesn\'t accept C.O.D.s and refused the delivery.*', 'F_DELIVERY_5050'),
(104, 'UPS', '*does not want the product and refused the delivery*', 'F_DELIVERY_5046'),
(105, 'UPS', '*The package was refused by the receiver and will be returned to the sender*', 'F_DELIVERY_5046'),
(106, 'UPS', '*The package was abandoned by the customer and will be surrendered to customs*', 'F_DELIVERY_5046'),
(107, 'UPS', '*The receiver has canceled the product order and refused delivery*', 'F_DELIVERY_5046'),
(108, 'UPS', '*The receiver refused the delivery*', 'F_DELIVERY_5046'),
(109, 'UPS', '*The receiver refused the package.*', 'F_DELIVERY_5046'),
(110, 'UPS', '*The receiver states the product was not ordered and has refused the delivery.*', 'F_DELIVERY_5046'),
(111, 'UPS', '*returned to the sender*', 'F_DELIVERY_5047'),
(112, 'UPS', '*Returned to shipper*', 'F_DELIVERY_5047'),
(113, 'UPS', '*in the process of returning this package to the sender*', 'F_DELIVERY_5047'),
(114, 'UPS', '*abandoned by both the sender and receiver*', 'F_DELIVERY_5048'),
(115, 'UPS', '*We do not currently serve the destination address*', 'F_DELIVERY_5049');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_route_matchrules`
--
ALTER TABLE `tb_route_matchrules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `network_code` (`network_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_route_matchrules`
--
ALTER TABLE `tb_route_matchrules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;
COMMIT;
