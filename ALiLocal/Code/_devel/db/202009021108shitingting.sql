/*
 Navicat Premium Data Transfer

 Source Server         : stt
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : aliexpress

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 02/09/2020 14:42:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tb_eus_timezone
-- ----------------------------
DROP TABLE IF EXISTS `tb_eus_timezone`;
CREATE TABLE `tb_eus_timezone`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_location` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '轨迹地点',
  `time_zone` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '时区',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 35 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '中美头程轨迹地点时区对照表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tb_eus_timezone
-- ----------------------------
INSERT INTO `tb_eus_timezone` VALUES (1, 'EWR', '-5', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (2, 'HKG', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (3, 'LAX', '-8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (4, 'ORD', '-6', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (5, 'SFO', '-8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (6, 'CVG', '-5', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (7, 'ATL', '-5', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (8, 'CAN', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (9, 'SZX', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (10, 'CSX', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (11, 'PVG', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (12, 'DLC', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (13, 'SHA', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (14, 'HNL', '-10', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (15, 'ICN', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (16, 'YTN', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (17, 'FUO', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (18, 'MFM', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (19, 'OAK', '-8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (20, 'JFK', '-5', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (21, 'CTU', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (22, 'DGM', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (23, 'LGB', '-8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (24, 'CGO', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (25, 'YIW', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (26, 'HGH', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (27, 'TPE', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (28, 'NRT', '9', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (29, 'PEK', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (30, 'IB Guangzhou warehouse', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (31, 'IB Chengdu warehouse', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (32, 'Shenzhen warehouse', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (33, 'Shenzhen (Baoan) warehouse', '8', 1599028840, 1599028840);
INSERT INTO `tb_eus_timezone` VALUES (34, 'IBFAR Yiwu warehouse', '8', 1599028840, 1599028840);

SET FOREIGN_KEY_CHECKS = 1;
