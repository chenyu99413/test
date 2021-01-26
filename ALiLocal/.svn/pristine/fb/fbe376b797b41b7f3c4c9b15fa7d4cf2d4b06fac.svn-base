-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 20, 2019 at 06:48 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

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
-- Table structure for table `tb_route_matchrules`
--

DROP TABLE IF EXISTS `tb_route_matchrules`;
CREATE TABLE `tb_route_matchrules` (
  `id` int(11) NOT NULL,
  `network_code` varchar(16) NOT NULL,
  `auto` tinyint(2) NOT NULL DEFAULT '0',
  `keyword` varchar(255) NOT NULL,
  `ali_code` varchar(32) NOT NULL,
  `cn_desc` text,
  `en_desc` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='轨迹匹配规则';

--
-- Dumping data for table `tb_route_matchrules`
--

INSERT INTO `tb_route_matchrules` (`id`, `network_code`, `auto`, `keyword`, `ali_code`, `cn_desc`, `en_desc`) VALUES
(1, 'UPS', 1, 'Processed for clearance', 'S_CLEARANCE_COMPLETE', NULL, NULL),
(2, 'UPS', 1, 'Clearance event', 'S_CLEARANCE_COMPLETE', NULL, NULL),
(3, 'UPS', 1, 'Arrival Scan', 'S_TH_IN', NULL, NULL),
(4, 'UPS', 1, 'Departure Scan', 'S_TH_OUT', NULL, NULL),
(5, 'UPS', 1, 'Out For Delivery*', 'S_DELIVERY_SCHEDULED', NULL, NULL),
(6, 'UPS', 1, 'Destination Scan', 'S_TH_IN_LAST', NULL, NULL),
(7, 'UPS', 1, 'DELIVERED', 'S_DELIVERY_SIGNED', NULL, NULL),
(8, 'UPS', 1, 'Import Scan', 'S_TH_IN', NULL, NULL),
(9, 'UPS', 1, 'Loaded on Delivery Vehicle', 'S_TH_IN_LAST', NULL, NULL),
(224, 'UPS', 0, '*delayed at export*', 'F_DELIVERY_5053', NULL, NULL),
(225, 'UPS', 0, '*The clearing agency is experiencing technical difficulties*', 'F_DELIVERY_5053', NULL, NULL),
(226, 'UPS', 0, '*A government agency delayed the package during the aviation security screening*', 'F_DELIVERY_5053', NULL, NULL),
(227, 'UPS', 0, '*A mechanical failure has caused a delay*', 'F_DELIVERY_5053', NULL, NULL),
(228, 'UPS', 0, '*Your package has been delayed due to incorrect tariff classification which must be resubmitted to the clearing agency*', 'F_CLEARANCE_5037', NULL, NULL),
(229, 'UPS', 0, '*Export documentation is incomplete or missing*', 'F_CLEARANCE_5037', NULL, NULL),
(230, 'UPS', 0, '*Your package may be delayed due to a required document inspection by the clearing agency*', 'F_CLEARANCE_5037', NULL, NULL),
(231, 'UPS', 0, '*Your package is delayed at export and is awaiting release from customs. / The package will be returned to the sender*', 'F_DELIVERY_5053', NULL, NULL),
(232, 'UPS', 0, '*valid ID*', 'F_CLEARANCE_5037', NULL, NULL),
(233, 'UPS', 0, '*Additional documentation is required for clearance*', 'F_CLEARANCE_5037', NULL, NULL),
(234, 'UPS', 0, '*Documentation required*', 'F_CLEARANCE_5037', NULL, NULL),
(235, 'UPS', 0, '*Power of attorney documentation*', 'F_CLEARANCE_5037', NULL, NULL),
(236, 'UPS', 0, '*provide warehouse entry details*', 'F_CLEARANCE_5037', NULL, NULL),
(237, 'UPS', 0, '*provide a unique customs identification number*', 'F_CLEARANCE_5037', NULL, NULL),
(238, 'UPS', 0, '*import requirements to clear the package*', 'F_CLEARANCE_5037', NULL, NULL),
(239, 'UPS', 0, '*missing clearance information*', 'F_CLEARANCE_5037', NULL, NULL),
(240, 'UPS', 0, '*clarification on invoice documentation*', 'F_CLEARANCE_5037', NULL, NULL),
(241, 'UPS', 0, '*Commodity description information must be validated*', 'F_CLEARANCE_5037', NULL, NULL),
(242, 'UPS', 0, '*missing commercial invoice*', 'F_CLEARANCE_5037', NULL, NULL),
(243, 'UPS', 0, '*import license*', 'F_CLEARANCE_5037', NULL, NULL),
(244, 'UPS', 0, '*merchandise description*', 'F_CLEARANCE_5037', NULL, NULL),
(245, 'UPS', 0, '*The receiver had no invoice information for this package*', 'F_CLEARANCE_5037', NULL, NULL),
(246, 'UPS', 0, '*The C.O.D. amount is missing*', 'F_DELIVERY_5053', NULL, NULL),
(247, 'UPS', 0, '*Additional information is needed from the importer*', 'F_CLEARANCE_5037', NULL, NULL),
(248, 'UPS', 0, '*requiring an invoice comparison*', 'F_CLEARANCE_5037', NULL, NULL),
(249, 'UPS', 0, '*FDA clearance and power of attorney required*', 'F_CLEARANCE_5037', NULL, NULL),
(250, 'UPS', 0, '*This package requires special entry.*', 'F_CLEARANCE_5037', NULL, NULL),
(251, 'UPS', 0, '*Government agency has placed a hold*', 'F_CLEARANCE_5037', NULL, NULL),
(252, 'UPS', 0, '*Inspection requested*', 'F_CLEARANCE_5037', NULL, NULL),
(253, 'UPS', 0, '*not match*', 'F_CLEARANCE_5037', NULL, NULL),
(254, 'UPS', 0, '*awaiting clearing agency review*', 'F_CLEARANCE_5037', NULL, NULL),
(255, 'UPS', 0, '*submitted documentation to a Government*', 'F_CLEARANCE_5037', NULL, NULL),
(256, 'UPS', 0, '*required document inspection*', 'F_CLEARANCE_5037', NULL, NULL),
(257, 'UPS', 0, '*x-ray inspection*', 'F_CLEARANCE_5037', NULL, NULL),
(258, 'UPS', 0, '*Your package has been delayed due to a Government regulatory agency hold*', 'F_CLEARANCE_5037', NULL, NULL),
(259, 'UPS', 0, '*is awaiting release from the clearing agency*', 'F_CLEARANCE_5037', NULL, NULL),
(260, 'UPS', 0, '*Environmental Protection Agency (EPA) hold*', 'F_CLEARANCE_5037', NULL, NULL),
(261, 'UPS', 0, '*export gateway hold*', 'F_CLEARANCE_5037', NULL, NULL),
(262, 'UPS', 0, '*an import gateway hold*', 'F_CLEARANCE_5037', NULL, NULL),
(263, 'UPS', 0, '*multiple packages shipping on the same day*', 'F_CLEARANCE_5037', NULL, NULL),
(264, 'UPS', 0, '*packages associated*', 'F_CLEARANCE_5037', NULL, NULL),
(265, 'UPS', 0, '*commodity or weight*', 'F_CLEARANCE_5037', NULL, NULL),
(266, 'UPS', 0, '*not in the importer\'s database*', 'F_CLEARANCE_5037', NULL, NULL),
(267, 'UPS', 0, '*held for payment of duty and tax*', 'F_DELIVERY_5050', NULL, NULL),
(268, 'UPS', 0, '*C.O.D. (ICOD) charges are due*', 'F_DELIVERY_5050', NULL, NULL),
(269, 'UPS', 0, '*Duties or taxes are due on this package*', 'F_DELIVERY_5050', NULL, NULL),
(270, 'UPS', 0, '*We are awaiting payment authorization from the receiver*', 'F_DELIVERY_5050', NULL, NULL),
(271, 'UPS', 0, '*agency is closed*', 'F_CLEARANCE_5037', NULL, NULL),
(272, 'UPS', 0, '*mechanical failure has delayed delivery*', 'F_DELIVERY_5053', NULL, NULL),
(273, 'UPS', 0, '*experiencing technical difficulties*', 'F_DELIVERY_5053', NULL, NULL),
(274, 'UPS', 0, '*requested by the receiver, we\'ll hold the package*', 'F_DELIVERY_5053', NULL, NULL),
(275, 'UPS', 0, '*formal entry at the import location*', 'F_CLEARANCE_5037', NULL, NULL),
(276, 'UPS', 0, '*not assigned a broker*', 'F_CLEARANCE_5037', NULL, NULL),
(277, 'UPS', 0, '*an alternate broker*', 'F_CLEARANCE_5037', NULL, NULL),
(278, 'UPS', 0, '*in-bond to a non-UPS off-site*', 'F_CLEARANCE_5037', NULL, NULL),
(279, 'UPS', 0, '*requested clearance by a non-UPS broker*', 'F_CLEARANCE_5037', NULL, NULL),
(280, 'UPS', 0, '*bond paperwork for alternate site clearance by a non-UPS broker*', 'F_CLEARANCE_5037', NULL, NULL),
(281, 'UPS', 0, '*held by brokerage for reasons beyond UPS\' control*', 'F_CLEARANCE_5037', NULL, NULL),
(282, 'UPS', 0, '*Your shipment was not processed by UPS brokerage*', 'F_CLEARANCE_5037', NULL, NULL),
(283, 'UPS', 0, '*FDA or Department of Agriculture*', 'F_CLEARANCE_5037', NULL, NULL),
(284, 'UPS', 0, '*contains a restricted commodity*', 'F_DELIVERY_5053', NULL, NULL),
(285, 'UPS', 0, '*The package contains articles that UPS prohibits for international shipping*', 'F_DELIVERY_5053', NULL, NULL),
(286, 'UPS', 0, '*incorrectly sorted*', 'F_DELIVERY_5053', NULL, NULL),
(287, 'UPS', 0, '*on the plane as scheduled*', 'F_DELIVERY_5053', NULL, NULL),
(288, 'UPS', 0, '*missed the scheduled transfer time*', 'F_DELIVERY_5053', NULL, NULL),
(289, 'UPS', 0, '*late UPS trailer arrival has delayed delivery.*', 'F_DELIVERY_5053', NULL, NULL),
(290, 'UPS', 0, '*late flight has caused a delay*', 'F_DELIVERY_5053', NULL, NULL),
(291, 'UPS', 0, '*A transportation accident has delayed delivery*', 'F_DELIVERY_5053', NULL, NULL),
(292, 'UPS', 0, '*Due to operating conditions, your package may be delayed.*', 'F_DELIVERY_5053', NULL, NULL),
(293, 'UPS', 0, '*overweight or oversized*', 'F_DELIVERY_5053', NULL, NULL),
(294, 'UPS', 0, '*The receiver was not available*', 'F_DELIVERY_5045', NULL, NULL),
(295, 'UPS', 0, '*business was closed*', 'F_DELIVERY_5053', NULL, NULL),
(296, 'UPS', 0, '*on vacation*', 'F_DELIVERY_5053', NULL, NULL),
(297, 'UPS', 0, '*labor dispute*', 'F_DELIVERY_5053', NULL, NULL),
(298, 'UPS', 0, '*holiday closures*', 'F_DELIVERY_5053', NULL, NULL),
(299, 'UPS', 0, '*security access*', 'F_DELIVERY_5053', NULL, NULL),
(300, 'UPS', 0, '*severe weather*', 'F_DELIVERY_5053', NULL, NULL),
(301, 'UPS', 0, '*Eurotunnel traffic disruption*', 'F_DELIVERY_5053', NULL, NULL),
(302, 'UPS', 0, '*Civil unrest*', 'F_DELIVERY_5053', NULL, NULL),
(303, 'UPS', 0, '*Customer not in*', 'F_DELIVERY_5045', NULL, NULL),
(304, 'UPS', 0, '*Customer was not available*', 'F_DELIVERY_5045', NULL, NULL),
(305, 'UPS', 0, '*Recent weather has caused delivery delays*', 'F_DELIVERY_5053', NULL, NULL),
(306, 'UPS', 0, '*held for pickup*', 'F_DELIVERY_5051', NULL, NULL),
(307, 'UPS', 0, '*The receiver has requested a delayed delivery*', 'F_DELIVERY_5053', NULL, NULL),
(308, 'UPS', 0, '*delivery during certain hours*', 'F_DELIVERY_5053', NULL, NULL),
(309, 'UPS', 0, '*hold the package for pickup*', 'F_DELIVERY_5051', NULL, NULL),
(310, 'UPS', 0, '*arranged to pick up*', 'F_DELIVERY_5051', NULL, NULL),
(311, 'UPS', 0, '*agreed to pickup the package*', 'F_DELIVERY_5051', NULL, NULL),
(312, 'UPS', 0, '*delivered to the selected UPS Access Point*', 'F_DELIVERY_5051', NULL, NULL),
(313, 'UPS', 0, '*deliver on the date requested*', 'F_DELIVERY_5053', NULL, NULL),
(314, 'UPS', 0, '*delivery change*', 'F_DELIVERY_5053', NULL, NULL),
(315, 'UPS', 0, '*change of delivery*', 'F_DELIVERY_5053', NULL, NULL),
(316, 'UPS', 0, '*delivery address has been updated*', 'F_DELIVERY_5053', NULL, NULL),
(317, 'UPS', 0, '*updated address*', 'F_DELIVERY_5053', NULL, NULL),
(318, 'UPS', 0, '*we have updated the address*', 'F_DELIVERY_5053', NULL, NULL),
(319, 'UPS', 0, '*Directions to the address are required to complete delivery*', 'F_DELIVERY_5053', NULL, NULL),
(320, 'UPS', 0, '*The receiver requested an alternate delivery address*', 'F_DELIVERY_5053', NULL, NULL),
(321, 'UPS', 0, '*updated the delivery information*', 'F_DELIVERY_5053', NULL, NULL),
(322, 'UPS', 0, '*We are adjusting delivery plans as quickly as possible*', 'F_DELIVERY_5053', NULL, NULL),
(323, 'UPS', 0, '*Your package was cleared after the scheduled transport departure*', 'F_DELIVERY_5053', NULL, NULL),
(324, 'UPS', 0, '*unable to collect funds*', 'F_DELIVERY_5053', NULL, NULL),
(325, 'UPS', 0, '*remote area*', 'F_DELIVERY_5053', NULL, NULL),
(326, 'UPS', 0, '*a new tracking number was assigned to this package*', 'F_CARRIER_PICKUP_RT_5035', NULL, NULL),
(327, 'UPS', 0, '*Delivered to UPS Access Point™*', 'F_DELIVERY_5051', NULL, NULL),
(328, 'UPS', 0, '*request to modify the delivery address*', 'F_DELIVERY_5053', NULL, NULL),
(329, 'UPS', 0, '*The receiver has moved*', 'F_DELIVERY_5043', NULL, NULL),
(330, 'UPS', 0, '*The company or receiver name is incorrect*', 'F_DELIVERY_5043', NULL, NULL),
(331, 'UPS', 0, '*The apartment number is either missing or incorrect*', 'F_DELIVERY_5043', NULL, NULL),
(332, 'UPS', 0, '*The address is incomplete*', 'F_DELIVERY_5043', NULL, NULL),
(333, 'UPS', 0, '*Delivery may be delayed due to an incomplete address*', 'F_DELIVERY_5043', NULL, NULL),
(334, 'UPS', 0, '*Address information is missing*', 'F_DELIVERY_5043', NULL, NULL),
(335, 'UPS', 0, '*Incomplete address information*', 'F_DELIVERY_5043', NULL, NULL),
(336, 'UPS', 0, '*Missing or incorrect apartment number*', 'F_DELIVERY_5043', NULL, NULL),
(337, 'UPS', 0, '*The city name in the address is incorrect*', 'F_DELIVERY_5043', NULL, NULL),
(338, 'UPS', 0, '*lost and found*', 'F_DELIVERY_5053', NULL, NULL),
(339, 'UPS', 0, '*maximum days*', 'F_DELIVERY_5053', NULL, NULL),
(340, 'UPS', 0, '*The return label was discarded*', 'F_DELIVERY_5053', NULL, NULL),
(341, 'UPS', 0, '*missed you on our final attempt*', 'F_DELIVERY_5045', NULL, NULL),
(342, 'UPS', 0, '*We\'re attempting to verify the package location*', 'F_DELIVERY_5053', NULL, NULL),
(343, 'UPS', 0, '*We\'ve begun an investigation to locate the package*', 'F_DELIVERY_5053', NULL, NULL),
(344, 'UPS', 0, '*All merchandise discarded*', 'F_DELIVERY_5053', NULL, NULL),
(345, 'UPS', 0, '*Merchandise is missing*', 'F_DELIVERY_5053', NULL, NULL),
(346, 'UPS', 0, '*The package has been damaged*', 'F_DELIVERY_5053', NULL, NULL),
(347, 'UPS', 0, '*damage has been reported*', 'F_DELIVERY_5053', NULL, NULL),
(348, 'UPS', 0, '*begun an investigation to locate the package*', 'F_DELIVERY_5053', NULL, NULL),
(349, 'UPS', 0, '*The receiver determined this product order is too expensive and refused the delivery*', 'F_DELIVERY_5046', NULL, NULL),
(350, 'UPS', 0, '*The receiver disputes or refuses to pay duty or taxes for the package*', 'F_DELIVERY_5046', NULL, NULL),
(351, 'UPS', 0, '*The receiver doesn\'t accept C.O.D.s and refused the delivery.*', 'F_DELIVERY_5046', NULL, NULL),
(352, 'UPS', 0, '*does not want the product and refused the delivery*', 'F_DELIVERY_5046', NULL, NULL),
(353, 'UPS', 0, '*The package was refused by the receiver and will be returned to the sender*', 'F_DELIVERY_5046', NULL, NULL),
(354, 'UPS', 0, '*The package was abandoned by the customer and will be surrendered to customs*', 'F_DELIVERY_5046', NULL, NULL),
(355, 'UPS', 0, '*The receiver has canceled the product order and refused delivery*', 'F_DELIVERY_5046', NULL, NULL),
(356, 'UPS', 0, '*The receiver refused the delivery*', 'F_DELIVERY_5046', NULL, NULL),
(357, 'UPS', 0, '*The receiver refused the package.*', 'F_DELIVERY_5046', NULL, NULL),
(358, 'UPS', 0, '*The receiver states the product was not ordered and has refused the delivery.*', 'F_DELIVERY_5046', NULL, NULL),
(359, 'UPS', 0, '*awaiting return to sender authorization*', 'F_DELIVERY_5053', NULL, NULL),
(360, 'UPS', 0, '*in the process of returning this package to the sender*', 'F_DELIVERY_5053', NULL, NULL),
(361, 'UPS', 0, '*returned to the sender*', 'F_DELIVERY_5047', NULL, NULL),
(362, 'UPS', 0, '*Returned to shipper*', 'F_DELIVERY_5047', NULL, NULL),
(363, 'UPS', 0, '*in the process of returning this package to the sender*', 'F_DELIVERY_5047', NULL, NULL),
(364, 'UPS', 0, '*abandoned by both the sender and receiver*', 'F_DELIVERY_5048', NULL, NULL),
(365, 'UPS', 0, '*We do not currently serve the destination address*', 'F_DELIVERY_5053', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=366;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
