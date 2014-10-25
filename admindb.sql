/*
Navicat MySQL Data Transfer

Source Server         : LOCAL
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : admindb

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2014-10-21 16:25:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for parametros
-- ----------------------------
DROP TABLE IF EXISTS `parametros`;
CREATE TABLE `parametros` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of parametros
-- ----------------------------
INSERT INTO `parametros` VALUES ('1', 'velocidad', '3.5');
INSERT INTO `parametros` VALUES ('2', 'altura', '12.0');
