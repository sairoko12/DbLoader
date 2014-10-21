/*
Navicat MySQL Data Transfer

Source Server         : LOCAL
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : testing

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2014-10-21 16:25:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for correos
-- ----------------------------
DROP TABLE IF EXISTS `correos`;
CREATE TABLE `correos` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of correos
-- ----------------------------
INSERT INTO `correos` VALUES ('1', 'thedaket@aol.com');
INSERT INTO `correos` VALUES ('2', 'sairoko16@gmail.com');

-- ----------------------------
-- Table structure for nombres
-- ----------------------------
DROP TABLE IF EXISTS `nombres`;
CREATE TABLE `nombres` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of nombres
-- ----------------------------
