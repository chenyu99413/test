-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 12, 2019 at 03:39 PM
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
  `keyword` varchar(255) NOT NULL,
  `ali_code` varchar(32) NOT NULL,
  `cn_desc` text,
  `en_desc` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='轨迹匹配规则';

--
-- Dumping data for table `tb_route_matchrules`
--

INSERT INTO `tb_route_matchrules` (`id`, `network_code`, `keyword`, `ali_code`, `cn_desc`, `en_desc`) VALUES
(1, 'UPS', 'Processed for clearance', 'S_CLEARANCE_COMPLETE', NULL, NULL),
(2, 'UPS', 'Clearance event', 'S_CLEARANCE_COMPLETE', NULL, NULL),
(3, 'UPS', 'Arrival Scan', 'S_TH_IN', NULL, NULL),
(4, 'UPS', 'Departure Scan', 'S_TH_OUT', NULL, NULL),
(5, 'UPS', 'Out For Delivery*', 'S_DELIVERY_SCHEDULED', NULL, NULL),
(6, 'UPS', 'Destination Scan', 'S_TH_IN_LAST', NULL, NULL),
(7, 'UPS', 'DELIVERED*', 'S_DELIVERY_SIGNED', NULL, NULL),
(116, 'UPS', '*valid ID*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(117, 'UPS', '*Additional documentation is required for clearance*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(118, 'UPS', '*Documentation required*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(119, 'UPS', '*Power of attorney documentation*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(120, 'UPS', '*provide warehouse entry details*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(121, 'UPS', '*provide a unique customs identification number*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(122, 'UPS', '*import requirements to clear the package*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(123, 'UPS', '*missing clearance information*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(124, 'UPS', '*clarification on invoice documentation*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(125, 'UPS', '*Commodity description information must be validated*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(126, 'UPS', '*missing commercial invoice*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(127, 'UPS', '*import license*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(128, 'UPS', '*merchandise description*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(129, 'UPS', '*The receiver had no invoice information for this package*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(130, 'UPS', '*The C.O.D. amount is missing*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(131, 'UPS', '*Additional information is needed from the importer*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(132, 'UPS', '*requiring an invoice comparison*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(133, 'UPS', '*FDA clearance and power of attorney required*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(134, 'UPS', '*This package requires special entry.*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)', 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).'),
(135, 'UPS', '*multiple packages shipping on the same day*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求同批次货物集中清关', 'Import clearance delay: Inspection, customs requires that multiple packages shipping on the same day to the same importer be combined into one shipment.'),
(136, 'UPS', '*packages associated*', 'F_CLEARANCE_5037', '进口清关延迟:查验,海关要求同批次货物集中清关', 'Import clearance delay: Inspection, customs requires that multiple packages shipping on the same day to the same importer be combined into one shipment.'),
(137, 'UPS', '*commodity or weight*', 'F_CLEARANCE_5037', '进口清关延迟:申报信息（如HS，价格等）有问题', 'Import clearance delay: Declaration information issue (HS code, price, etc).'),
(138, 'UPS', '*not in the importer\'s database*', 'F_CLEARANCE_5037', '进口清关延迟:进口商资质问题，在联系进口商', 'Import clearance delay: Importer qualification problem, contact the importer.'),
(139, 'UPS', '*held for payment of duty and tax*', 'F_CLEARANCE_5037', '进口清关延迟:进口税费支付确认中', 'Import clearance delay: In progress of import tax fees confirmation.'),
(140, 'UPS', '*C.O.D. (ICOD) charges are due*', 'F_CLEARANCE_5037', '进口清关延迟:进口税费支付确认中', 'Import clearance delay: In progress of import tax fees confirmation.'),
(141, 'UPS', '*Duties or taxes are due on this package*', 'F_CLEARANCE_5037', '进口清关延迟:进口税费支付确认中', 'Import clearance delay: In progress of import tax fees confirmation.'),
(142, 'UPS', '*We are awaiting payment authorization from the receiver*', 'F_CLEARANCE_5037', '进口清关延迟:进口税费支付确认中', 'Import clearance delay: In progress of import tax fees confirmation.'),
(143, 'UPS', '*agency is closed*', 'F_CLEARANCE_5037', '进口清关延迟:设备故障/清关机构非工作状态', 'Import clearance delay: Mechanical failure/customs clearance mechanism out of working state.'),
(144, 'UPS', '*mechanical failure has delayed delivery*', 'F_CLEARANCE_5037', '进口清关延迟:设备故障/清关机构非工作状态', 'Import clearance delay: Mechanical failure/customs clearance mechanism out of working state.'),
(145, 'UPS', '*experiencing technical difficulties*', 'F_CLEARANCE_5037', '进口清关延迟:设备故障/清关机构非工作状态', 'Import clearance delay: Mechanical failure/customs clearance mechanism out of working state.'),
(146, 'UPS', '*requested by the receiver, we\'ll hold the package*', 'F_CLEARANCE_5037', '进口清关延迟:收件方要求暂扣，等其通知', 'Import clearance delay: The receiver requires temporary hold for pick up, awaiting for notice.'),
(147, 'UPS', '*formal entry at the import location*', 'F_CLEARANCE_5037', '进口清关延迟:海关要求以一般贸易方式清关', 'Import clearance delay: Customs require formal entry clearance.'),
(148, 'UPS', '*not assigned a broker*', 'F_CLEARANCE_5037', '进口清关延迟:进口方无指定清关代理人', 'Import clearance delay: The importer has not assigned a broker.'),
(149, 'UPS', '*an alternate broker*', 'F_CLEARANCE_5037', '进口清关延迟:已向备用清关行提交文件，待放行', 'Import clearance delay: We have given documentation to an alternate broker and are awaiting clearance.'),
(150, 'UPS', '*in-bond to a non-UPS off-site*', 'F_CLEARANCE_5037', '进口清关延迟:第三方清关行在处理中', 'Import clearance delay: Third party customs clearance is in progress.'),
(151, 'UPS', '*requested clearance by a non-UPS broker*', 'F_CLEARANCE_5037', '进口清关延迟:第三方清关行在处理中', 'Import clearance delay: Third party customs clearance is in progress.'),
(152, 'UPS', '*bond paperwork for alternate site clearance by a non-UPS broker*', 'F_CLEARANCE_5037', '进口清关延迟:第三方清关行在处理中', 'Import clearance delay: Third party customs clearance is in progress.'),
(153, 'UPS', '*held by brokerage for reasons beyond UPS\' control*', 'F_CLEARANCE_5037', '进口清关延迟:第三方清关行在处理中', 'Import clearance delay: Third party customs clearance is in progress.'),
(154, 'UPS', '*Your shipment was not processed by UPS brokerage*', 'F_CLEARANCE_5037', '进口清关延迟:第三方清关行在处理中', 'Import clearance delay: Third party customs clearance is in progress.'),
(155, 'UPS', '*FDA or Department of Agriculture*', 'F_CLEARANCE_5037', '进口清关延迟:FDA或USDA申报', 'Import clearance delay: Being processed for submission to the FDA or USDA.'),
(156, 'UPS', '*incorrectly sorted*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(157, 'UPS', '*on the plane as scheduled*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(158, 'UPS', '*missed the scheduled transfer time*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(159, 'UPS', '*late UPS trailer arrival has delayed delivery.*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(160, 'UPS', '*late flight has caused a delay*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(161, 'UPS', '*A transportation accident has delayed delivery*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(162, 'UPS', '*Due to operating conditions, your package may be delayed.*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(163, 'UPS', '*overweight or oversized*', 'F_DELIVERY_5053', '中转延迟:转运延误', 'Transit delay: We\'ve missed the scheduled transfer time.'),
(164, 'UPS', '*The receiver was not available*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(165, 'UPS', '*business was closed*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(166, 'UPS', '*on vacation*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(167, 'UPS', '*labor dispute*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(168, 'UPS', '*holiday closures*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(169, 'UPS', '*security access*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(170, 'UPS', '*severe weather*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(171, 'UPS', '*Eurotunnel traffic disruption*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(172, 'UPS', '*Civil unrest*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(173, 'UPS', '*Customer not in*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(174, 'UPS', '*Customer was not available*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(175, 'UPS', '*Recent weather has caused delivery delays*', 'F_DELIVERY_5045', '派送延迟:收件方不在或派送受限(假期/罢工/条件限制)，将重派', 'Delivery delay: The receiver is not avaiable or the delivery is restricted (on vacation, labor dispute, security access, etc), will make reattempt.'),
(176, 'UPS', '*held for pickup*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(177, 'UPS', '*The receiver has requested a delayed delivery*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(178, 'UPS', '*delivery during certain hours*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(179, 'UPS', '*hold the package for pickup*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(180, 'UPS', '*arranged to pick up*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(181, 'UPS', '*agreed to pickup the package*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(182, 'UPS', '*delivered to the selected UPS Access Point*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(183, 'UPS', '*deliver on the date requested*', 'F_DELIVERY_5051', '派送延迟:收件方要求暂扣、延迟派送或自提', 'Delivery delay: The receiver requires temporary hold for pick up, delayed delivery or self-pick up.'),
(184, 'UPS', '*delivery change*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(185, 'UPS', '*change of delivery*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(186, 'UPS', '*change the delivery*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(187, 'UPS', '*delivery address has been updated*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(188, 'UPS', '*updated address*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(189, 'UPS', '*we have updated the address*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(190, 'UPS', '*Directions to the address are required to complete delivery*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(191, 'UPS', '*The receiver requested an alternate delivery address*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(192, 'UPS', '*updated the delivery information*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(193, 'UPS', '*We are adjusting delivery plans as quickly as possible*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(194, 'UPS', '*Your package was cleared after the scheduled transport departure*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:已更新派送信息和计划，将重派', 'Delivery delay: A delivery change for this package is in progress, will make reattempt.'),
(195, 'UPS', '*unable to collect funds*', 'F_DELIVERY_5050', '派送延迟:无法收集相关费用，将重派', 'Delivery delay: Related fees cannot be collected, will make reattempt.'),
(196, 'UPS', '*a new tracking number was assigned to this package*', 'F_CARRIER_PICKUP_RT_5035', '派送延迟:运单号更新，将重派', 'Delivery delay: A new tracking number was assigned to this package.'),
(197, 'UPS', '*Delivered to UPS Access Point™*', 'F_DELIVERY_5051', '派送延迟:派送到自提点', 'Delivery delay: Delivered to Carrier Access Point.'),
(198, 'UPS', '*request to modify the delivery address*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(199, 'UPS', '*The receiver has moved*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(200, 'UPS', '*The company or receiver name is incorrect*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(201, 'UPS', '*The apartment number is either missing or incorrect*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(202, 'UPS', '*The address is incomplete*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(203, 'UPS', '*Delivery may be delayed due to an incomplete address*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(204, 'UPS', '*Address information is missing*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(205, 'UPS', '*Incomplete address information*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(206, 'UPS', '*Missing or incorrect apartment number*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(207, 'UPS', '*The city name in the address is incorrect*', 'F_DELIVERY_5043', '派送异常:收件地址信息（街道/公寓号/门牌号等）不正确或不可用，在解决中', 'Delivery failure: Address information (street/apartment/house number, etc.) is incorrect or unavailable, We\'re attempting to update it.'),
(208, 'UPS', '*missed you on our final attempt*', 'F_DELIVERY_5045', '派送异常:多次无法投递，在联系中', 'Delivery failure: Delivery failed multiple times, contact the receiver.'),
(209, 'UPS', '*The receiver determined this product order is too expensive and refused the delivery*', 'F_DELIVERY_5050', '派送异常:收件方拒绝相关费用（ C.O.D./税费/运费），将联系发件方处理', 'Delivery failure: The receiver rejects the charges (C.O.D./tax/freight), will contact the shipper.'),
(210, 'UPS', '*The receiver disputes or refuses to pay duty or taxes for the package*', 'F_DELIVERY_5050', '派送异常:收件方拒绝相关费用（ C.O.D./税费/运费），将联系发件方处理', 'Delivery failure: The receiver rejects the charges (C.O.D./tax/freight), will contact the shipper.'),
(211, 'UPS', '*The receiver doesn\'t accept C.O.D.s and refused the delivery.*', 'F_DELIVERY_5050', '派送异常:收件方拒绝相关费用（ C.O.D./税费/运费），将联系发件方处理', 'Delivery failure: The receiver rejects the charges (C.O.D./tax/freight), will contact the shipper.'),
(212, 'UPS', '*does not want the product and refused the delivery*', 'F_DELIVERY_5046', '派送异常:收件方拒收', 'Delivery failure: The receiver does not want the package and refused the delivery.'),
(213, 'UPS', '*The package was refused by the receiver and will be returned to the sender*', 'F_DELIVERY_5046', '派送异常:收件方拒收', 'Delivery failure: The receiver does not want the package and refused the delivery.'),
(214, 'UPS', '*The package was abandoned by the customer and will be surrendered to customs*', 'F_DELIVERY_5046', '派送异常:收件方拒收', 'Delivery failure: The receiver does not want the package and refused the delivery.'),
(215, 'UPS', '*The receiver has canceled the product order and refused delivery*', 'F_DELIVERY_5046', '派送异常:收件方拒收', 'Delivery failure: The receiver does not want the package and refused the delivery.'),
(216, 'UPS', '*The receiver refused the delivery*', 'F_DELIVERY_5046', '派送异常:收件方拒收', 'Delivery failure: The receiver does not want the package and refused the delivery.'),
(217, 'UPS', '*The receiver refused the package.*', 'F_DELIVERY_5046', '派送异常:收件方拒收', 'Delivery failure: The receiver does not want the package and refused the delivery.'),
(218, 'UPS', '*The receiver states the product was not ordered and has refused the delivery.*', 'F_DELIVERY_5046', '派送异常:收件方拒收', 'Delivery failure: The receiver does not want the package and refused the delivery.'),
(219, 'UPS', '*returned to the sender*', 'F_DELIVERY_5047', '派送异常:退运给发件方', 'Delivery failure: Returning to the shipper.'),
(220, 'UPS', '*Returned to shipper*', 'F_DELIVERY_5047', '派送异常:退运给发件方', 'Delivery failure: Returning to the shipper.'),
(221, 'UPS', '*in the process of returning this package to the sender*', 'F_DELIVERY_5047', '派送异常:退运给发件方', 'Delivery failure: Returning to the shipper.'),
(222, 'UPS', '*abandoned by both the sender and receiver*', 'F_DELIVERY_5048', '派送异常:收发件方弃件、销毁', 'Delivery failure: The package was abandoned by both the sender and receiver.'),
(223, 'UPS', '*We do not currently serve the destination address*', 'F_DELIVERY_5049', '派送异常:收件地址不在服务范围之内', 'Delivery failure: We do not currently serve this special destination address, will transferred to a local agent for delivery.');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
