/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `auth`
-- ----------------------------
DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_email` varchar(50) NOT NULL,
  `app_password` varchar(50) NOT NULL,
  `auth_type` smallint(1) NOT NULL,
  `disabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth
-- ----------------------------
INSERT INTO auth VALUES ('1', 'superadmin@bag.com.au', 'f865b53623b121fd34ee5426c792e5c33af8c227', '0', '0');

-- ----------------------------
-- Table structure for `configuration`
-- ----------------------------
DROP TABLE IF EXISTS `configuration`;
CREATE TABLE `configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_title` varchar(31) NOT NULL,
  `config_key` int(2) NOT NULL,
  `config_value` varchar(255) DEFAULT NULL,
  `config_unit` varchar(7) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key` (`config_key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of configuration
-- ----------------------------
INSERT INTO configuration VALUES ('1', 'BASE PRICE', '1', '7', '$', '2014-12-04 19:59:49', '2014-12-11 19:09:47');
INSERT INTO configuration VALUES ('2', 'CREDIT CARD FEE', '2', '2.5', '%', '2014-12-04 20:00:16', '2014-12-11 16:59:32');
INSERT INTO configuration VALUES ('3', 'DELIVERY SIGNATURE FEE', '3', '2', '$', '2014-12-04 20:00:32', '2014-12-11 18:39:04');
INSERT INTO configuration VALUES ('4', 'DELIVERY INSURE FEE', '4', '5', '$', '2014-12-11 18:34:57', '2014-12-11 19:14:36');
INSERT INTO configuration VALUES ('5', 'CANCELLATION PERCENTAGE', '5', '20', '%', '2014-12-11 18:35:40', '2014-12-11 19:11:57');
INSERT INTO configuration VALUES ('6', 'DELIVERY TIME', '6', '48', 'hours', '2014-12-11 18:36:44', '2014-12-11 18:36:44');
INSERT INTO configuration VALUES ('7', 'AWAITING TIME', '7', '24', 'hours', '2014-12-11 18:36:57', '2014-12-11 19:24:57');
INSERT INTO configuration VALUES ('8', 'ASSIGNED TIME', '8', '36', 'hours', '2014-12-11 18:37:20', '2014-12-11 18:37:20');
INSERT INTO configuration VALUES ('9', 'PICKED UP TIME', '9', '44', 'hours', '2014-12-11 18:38:19', '2014-12-11 18:38:19');
INSERT INTO configuration VALUES ('10', 'DELIVERED TIME', '10', '24', 'hours', '2014-12-11 18:38:35', '2014-12-11 18:43:41');
INSERT INTO configuration VALUES ('11', 'CANCELED TIME', '11', '0.25', 'hours', '2014-12-11 18:59:07', '2014-12-11 18:59:07');
INSERT INTO configuration VALUES ('12', 'ACCEPTED TIME', '12', '24', 'hours', '2014-12-11 18:59:38', '2014-12-11 19:28:04');
INSERT INTO configuration VALUES ('13', 'RATING MAX POINT', '13', '5', 'point', '2014-12-11 20:08:27', '2014-12-11 20:08:27');
INSERT INTO configuration VALUES ('14', 'REPORTED EMAIL', '14', null, 'email', '2014-12-12 14:02:26', '2014-12-12 14:02:26');
INSERT INTO configuration VALUES ('15', 'PAYPAL ID', '15', null, 'id', '2014-12-12 14:19:13', '2014-12-12 14:19:13');
INSERT INTO configuration VALUES ('16', 'PAYPAL SECRET', '16', null, 'secret', '2014-12-12 14:19:40', '2014-12-12 14:19:40');

-- ----------------------------
-- Table structure for `courier`
-- ----------------------------
DROP TABLE IF EXISTS `courier`;
CREATE TABLE `courier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id` int(11) NOT NULL,
  `is_company` tinyint(1) NOT NULL DEFAULT '1',
  `company_name` varchar(255) DEFAULT NULL,
  `firstname` varchar(125) DEFAULT NULL,
  `lastname` varchar(125) DEFAULT NULL,
  `abn` varchar(31) DEFAULT NULL,
  `contact_firstname` varchar(125) DEFAULT NULL,
  `contact_lastname` varchar(125) DEFAULT NULL,
  `email` varchar(63) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `alternative_email` varchar(63) DEFAULT NULL,
  `general_number` varchar(31) DEFAULT NULL,
  `contact_number` varchar(31) DEFAULT NULL,
  `mobile` varchar(31) NOT NULL,
  `fax` varchar(31) DEFAULT NULL,
  `address` varchar(63) NOT NULL,
  `suburb` varchar(63) DEFAULT NULL,
  `courier_state` varchar(7) DEFAULT NULL,
  `postcode` varchar(7) DEFAULT NULL,
  `bank_institution` varchar(31) NOT NULL,
  `bank_bsb` varchar(31) NOT NULL,
  `bank_account` varchar(31) NOT NULL,
  `preferred_region` varchar(63) DEFAULT NULL,
  `preferred_pickup_suburb_ids` varchar(255) DEFAULT NULL,
  `preferred_delivery_suburb_ids` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `utility_bill` varchar(255) DEFAULT NULL,
  `bank_statement` varchar(255) DEFAULT NULL,
  `photo_approved` tinyint(1) DEFAULT NULL,
  `utility_bill_approved` tinyint(1) DEFAULT NULL,
  `bank_statement_approved` tinyint(1) DEFAULT NULL,
  `head_office_approved` tinyint(1) DEFAULT NULL,
  `can_assign` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_name` (`company_name`),
  KEY `abn` (`abn`),
  KEY `contact_firstname` (`contact_firstname`),
  KEY `contact_lastname` (`contact_lastname`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`),
  KEY `created` (`created`),
  KEY `head_office_approved` (`head_office_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of courier
-- ----------------------------

-- ----------------------------
-- Table structure for `customer`
-- ----------------------------
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `firstname` varchar(125) DEFAULT NULL,
  `lastname` varchar(125) DEFAULT NULL,
  `abn` varchar(31) DEFAULT NULL,
  `contact_firstname` varchar(125) DEFAULT NULL,
  `contact_lastname` varchar(125) DEFAULT NULL,
  `email` varchar(63) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `alternative_email` varchar(63) DEFAULT NULL,
  `general_number` varchar(31) DEFAULT NULL,
  `contact_number` varchar(31) DEFAULT NULL,
  `mobile` varchar(31) NOT NULL,
  `fax` varchar(31) DEFAULT NULL,
  `address` varchar(63) NOT NULL,
  `suburb` varchar(63) DEFAULT NULL,
  `customer_state` varchar(7) DEFAULT NULL,
  `postcode` varchar(7) DEFAULT NULL,
  `card_type` tinyint(1) NOT NULL,
  `card_name` varchar(50) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `expiry_date` varchar(7) NOT NULL,
  `ccv` varchar(3) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `preferred_region` varchar(63) DEFAULT NULL,
  `preferred_pickup_address` varchar(63) DEFAULT NULL,
  `preferred_pickup_suburb` varchar(63) DEFAULT NULL,
  `preferred_pickup_state` varchar(7) DEFAULT NULL,
  `preferred_pickup_postcode` varchar(7) DEFAULT NULL,
  `preferred_delivery_address` varchar(63) DEFAULT NULL,
  `preferred_delivery_suburb` varchar(63) DEFAULT NULL,
  `preferred_delivery_state` varchar(7) DEFAULT NULL,
  `preferred_delivery_postcode` varchar(7) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_name` (`company_name`),
  KEY `firstname` (`firstname`),
  KEY `surname` (`lastname`),
  KEY `abn` (`abn`),
  KEY `contact_firstname` (`contact_firstname`),
  KEY `contact_lastname` (`contact_lastname`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customer
-- ----------------------------

-- ----------------------------
-- Table structure for `message`
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_type` tinyint(1) NOT NULL,
  `from_login_id` int(11) NOT NULL,
  `to_login_id` int(11) NOT NULL,
  `pickup_id` int(11) NOT NULL,
  `content` varchar(511) NOT NULL,
  `created` datetime NOT NULL,
  `read` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_type` (`message_type`),
  KEY `from_login_id` (`from_login_id`),
  KEY `to_login_id` (`to_login_id`),
  KEY `pickup_id` (`pickup_id`),
  KEY `content` (`content`(255)),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of message
-- ----------------------------

-- ----------------------------
-- Table structure for `pickup`
-- ----------------------------
DROP TABLE IF EXISTS `pickup`;
CREATE TABLE `pickup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `courier_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `awaiting_active_time` datetime NOT NULL,
  `assigned_active_time` datetime DEFAULT NULL,
  `picked_up_active_time` datetime DEFAULT NULL,
  `delivered_active_time` datetime DEFAULT NULL,
  `cancelled_active_time` datetime DEFAULT NULL,
  `accepted_active_time` datetime DEFAULT NULL,
  `rated_active_time` datetime DEFAULT NULL,
  `from_address` varchar(255) NOT NULL,
  `to_address` varchar(255) NOT NULL,
  `detail` varchar(511) DEFAULT NULL,
  `note_from_address` varchar(511) DEFAULT NULL,
  `note_to_address` varchar(511) DEFAULT NULL,
  `preferred_pickup_datetime` datetime DEFAULT NULL,
  `base_charge_fee` double(5,2) DEFAULT NULL,
  `delivery_signature_fee` double(5,2) DEFAULT NULL,
  `delivery_insure_fee` double(5,2) DEFAULT NULL,
  `credit_fee` double(5,2) DEFAULT NULL,
  `cancel_fee` double(5,2) DEFAULT NULL,
  `total_fee` double(5,2) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `courier_id` (`courier_id`),
  KEY `status` (`status`),
  KEY `from_address` (`from_address`),
  KEY `to_address` (`to_address`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pickup
-- ----------------------------

-- ----------------------------
-- Table structure for `pickup_rating`
-- ----------------------------
DROP TABLE IF EXISTS `pickup_rating`;
CREATE TABLE `pickup_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pickup_id` int(11) NOT NULL,
  `rating_id` int(11) NOT NULL,
  `rating_value` smallint(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pickup_id` (`pickup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pickup_rating
-- ----------------------------

-- ----------------------------
-- Table structure for `rating`
-- ----------------------------
DROP TABLE IF EXISTS `rating`;
CREATE TABLE `rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(511) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rating
-- ----------------------------

-- ----------------------------
-- Table structure for `staff`
-- ----------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id` int(11) NOT NULL,
  `email` varchar(63) NOT NULL,
  `role` smallint(1) NOT NULL,
  `area_ids` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of staff
-- ----------------------------
INSERT INTO staff VALUES ('1', '1', 'superadmin@bag.com.au', '7', '0', '2014-12-12 14:58:18', '2014-12-12 14:58:18');

-- ----------------------------
-- Table structure for `suburbs`
-- ----------------------------
DROP TABLE IF EXISTS `suburbs`;
CREATE TABLE `suburbs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(63) NOT NULL,
  `state` varchar(7) NOT NULL,
  `region` varchar(255) NOT NULL,
  `suburb` varchar(255) NOT NULL,
  `postcode` int(7) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of suburbs
-- ----------------------------
INSERT INTO suburbs VALUES ('1', 'Australia', 'NSW', 'Sydney', 'Sydney City', '2000', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('2', 'Australia', 'NSW', 'Sydney', 'Ultimo', '2007', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('3', 'Australia', 'NSW', 'Sydney', 'Chippendale', '2008', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('4', 'Australia', 'NSW', 'Sydney', 'Pyrmont', '2009', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('5', 'Australia', 'NSW', 'Sydney', 'Surry Hills', '2010', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('6', 'Australia', 'NSW', 'Sydney', 'Kings Cross', '2011', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('7', 'Australia', 'NSW', 'Sydney', 'Alexandria', '2015', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('8', 'Australia', 'NSW', 'Sydney', 'Redfern', '2016', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('9', 'Australia', 'NSW', 'Sydney', 'Waterloo', '2017', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('10', 'Australia', 'NSW', 'Sydney', 'Rosebery', '2018', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('11', 'Australia', 'NSW', 'Sydney', 'Botany', '2019', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('12', 'Australia', 'NSW', 'Sydney', 'Mascot', '2020', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('13', 'Australia', 'NSW', 'Sydney', 'Paddington', '2021', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('14', 'Australia', 'NSW', 'Sydney', 'Bondi Junction', '2022', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('15', 'Australia', 'NSW', 'Sydney', 'Bellevue Hill', '2023', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('16', 'Australia', 'NSW', 'Sydney', 'Waverley', '2024', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('17', 'Australia', 'NSW', 'Sydney', 'Woollahra', '2025', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('18', 'Australia', 'NSW', 'Sydney', 'Bondi', '2026', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('19', 'Australia', 'NSW', 'Sydney', 'Edgecliff', '2027', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('20', 'Australia', 'NSW', 'Sydney', 'Double Bay', '2028', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('21', 'Australia', 'NSW', 'Sydney', 'Rose Bay', '2029', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('22', 'Australia', 'NSW', 'Sydney', 'Vaucluse', '2030', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('23', 'Australia', 'NSW', 'Sydney', 'Randwick', '2031', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('24', 'Australia', 'NSW', 'Sydney', 'Kingsford', '2032', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('25', 'Australia', 'NSW', 'Sydney', 'Kensington', '2033', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('26', 'Australia', 'NSW', 'Sydney', 'Coogee', '2034', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('27', 'Australia', 'NSW', 'Sydney', 'Pagewood', '2035', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('28', 'Australia', 'NSW', 'Sydney', 'Matraville', '2036', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('29', 'Australia', 'NSW', 'Sydney', 'Glebe', '2037', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('30', 'Australia', 'NSW', 'Sydney', 'Annandale', '2038', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('31', 'Australia', 'NSW', 'Sydney', 'Rozelle', '2039', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('32', 'Australia', 'NSW', 'Sydney', 'Leichhardt', '2040', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('33', 'Australia', 'NSW', 'Sydney', 'Balmain', '2041', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('34', 'Australia', 'NSW', 'Sydney', 'Newtown', '2042', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('35', 'Australia', 'NSW', 'Sydney', 'Erskineville', '2043', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('36', 'Australia', 'NSW', 'Sydney', 'St Peters', '2044', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('37', 'Australia', 'NSW', 'Sydney', 'Haberfield', '2045', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('38', 'Australia', 'NSW', 'Sydney', 'Five Dock', '2046', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('39', 'Australia', 'NSW', 'Sydney', 'Drummoyne', '2047', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('40', 'Australia', 'NSW', 'Sydney', 'Stanmore', '2048', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('41', 'Australia', 'NSW', 'Sydney', 'Petersham', '2049', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('42', 'Australia', 'NSW', 'Sydney', 'Camperdown', '2050', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('43', 'Australia', 'NSW', 'Sydney', 'North Sydney', '2060', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('44', 'Australia', 'NSW', 'Sydney', 'Milsons Point', '2061', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('45', 'Australia', 'NSW', 'Sydney', 'Cammeray', '2062', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('46', 'Australia', 'NSW', 'Sydney', 'Northbridge', '2063', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('47', 'Australia', 'NSW', 'Sydney', 'Artarmon', '2064', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('48', 'Australia', 'NSW', 'Sydney', 'Crows Nest', '2065', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('49', 'Australia', 'NSW', 'Sydney', 'Lane Cove', '2066', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('50', 'Australia', 'NSW', 'Sydney', 'Chatswood', '2067', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('51', 'Australia', 'NSW', 'Sydney', 'Willoughby', '2068', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('52', 'Australia', 'NSW', 'Sydney', 'Roseville', '2069', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('53', 'Australia', 'NSW', 'Sydney', 'Lindfield', '2070', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('54', 'Australia', 'NSW', 'Sydney', 'Killara', '2071', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('55', 'Australia', 'NSW', 'Sydney', 'Gordon', '2072', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('56', 'Australia', 'NSW', 'Sydney', 'Pymble', '2073', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('57', 'Australia', 'NSW', 'Sydney', 'Turramurra', '2074', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('58', 'Australia', 'NSW', 'Sydney', 'St Ives', '2075', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('59', 'Australia', 'NSW', 'Sydney', 'Wahroona', '2076', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('60', 'Australia', 'NSW', 'Sydney', 'Hornsby', '2077', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('61', 'Australia', 'NSW', 'Sydney', 'Mount Colah', '2079', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('62', 'Australia', 'NSW', 'Sydney', 'Mount Kuring-gai', '2080', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('63', 'Australia', 'NSW', 'Sydney', 'Berowra Waters', '2082', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('64', 'Australia', 'NSW', 'Sydney', 'Terrey Hills', '2084', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('65', 'Australia', 'NSW', 'Sydney', 'Belrose', '2085', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('66', 'Australia', 'NSW', 'Sydney', 'Frenchs Forest', '2086', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('67', 'Australia', 'NSW', 'Sydney', 'Forestville', '2087', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('68', 'Australia', 'NSW', 'Sydney', 'Mosman', '2088', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('69', 'Australia', 'NSW', 'Sydney', 'Neutral Bay', '2089', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('70', 'Australia', 'NSW', 'Sydney', 'Cremorne', '2090', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('71', 'Australia', 'NSW', 'Sydney', 'Seaforth', '2092', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('72', 'Australia', 'NSW', 'Sydney', 'Balgowlah', '2093', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('73', 'Australia', 'NSW', 'Sydney', 'Manly', '2095', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('74', 'Australia', 'NSW', 'Sydney', 'Harbord', '2096', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('75', 'Australia', 'NSW', 'Sydney', 'Collaroy', '2097', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('76', 'Australia', 'NSW', 'Sydney', 'Dee Why', '2099', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('77', 'Australia', 'NSW', 'Sydney', 'Brookvale', '2100', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('78', 'Australia', 'NSW', 'Sydney', 'Narrabeen', '2101', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('79', 'Australia', 'NSW', 'Sydney', 'Warriewood', '2102', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('80', 'Australia', 'NSW', 'Sydney', 'Mona Vale', '2103', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('81', 'Australia', 'NSW', 'Sydney', 'Bayview', '2104', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('82', 'Australia', 'NSW', 'Sydney', 'Newport', '2106', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('83', 'Australia', 'NSW', 'Sydney', 'Avalon', '2107', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('84', 'Australia', 'NSW', 'Sydney', 'Hunters Hill', '2110', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('85', 'Australia', 'NSW', 'Sydney', 'Gladesville', '2111', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('86', 'Australia', 'NSW', 'Sydney', 'Ryde', '2112', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('87', 'Australia', 'NSW', 'Sydney', 'North Ryde', '2113', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('88', 'Australia', 'NSW', 'Sydney', 'Meadowbank', '2114', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('89', 'Australia', 'NSW', 'Sydney', 'Ermington', '2115', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('90', 'Australia', 'NSW', 'Sydney', 'Rydalmere', '2116', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('91', 'Australia', 'NSW', 'Sydney', 'Telopea', '2117', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('92', 'Australia', 'NSW', 'Sydney', 'Carlingford', '2118', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('93', 'Australia', 'NSW', 'Sydney', 'Beecroft', '2119', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('94', 'Australia', 'NSW', 'Sydney', 'Pennant Hills', '2120', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('95', 'Australia', 'NSW', 'Sydney', 'Epping', '2121', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('96', 'Australia', 'NSW', 'Sydney', 'Eastwood', '2122', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('97', 'Australia', 'NSW', 'Sydney', 'W. Pennant Hills', '2125', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('98', 'Australia', 'NSW', 'Sydney', 'Cherrybrook', '2126', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('99', 'Australia', 'NSW', 'Sydney', 'Homebush Bay', '2127', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('100', 'Australia', 'NSW', 'Sydney', 'Silverwater', '2128', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('101', 'Australia', 'NSW', 'Sydney', 'Sydney Markets', '2129', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('102', 'Australia', 'NSW', 'Sydney', 'Summer Hill', '2130', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('103', 'Australia', 'NSW', 'Sydney', 'Ashfield', '2131', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('104', 'Australia', 'NSW', 'Sydney', 'Croydon', '2132', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('105', 'Australia', 'NSW', 'Sydney', 'Croydon Park', '2133', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('106', 'Australia', 'NSW', 'Sydney', 'Burwood', '2134', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('107', 'Australia', 'NSW', 'Sydney', 'Strathfield', '2135', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('108', 'Australia', 'NSW', 'Sydney', 'Enfield', '2136', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('109', 'Australia', 'NSW', 'Sydney', 'Concord', '2137', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('110', 'Australia', 'NSW', 'Sydney', 'Rhodes', '2138', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('111', 'Australia', 'NSW', 'Sydney', 'Homebush', '2140', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('112', 'Australia', 'NSW', 'Sydney', 'Lidcombe', '2141', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('113', 'Australia', 'NSW', 'Sydney', 'Granville', '2142', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('114', 'Australia', 'NSW', 'Sydney', 'Regents Park', '2143', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('115', 'Australia', 'NSW', 'Sydney', 'Auburn', '2144', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('116', 'Australia', 'NSW', 'Sydney', 'Wentworthville', '2145', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('117', 'Australia', 'NSW', 'Sydney', 'Toongabbie', '2146', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('118', 'Australia', 'NSW', 'Sydney', 'Seven Hills', '2147', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('119', 'Australia', 'NSW', 'Sydney', 'Blacktown', '2148', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('120', 'Australia', 'NSW', 'Sydney', 'Parramatta', '2150', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('121', 'Australia', 'NSW', 'Sydney', 'North Rocks', '2151', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('122', 'Australia', 'NSW', 'Sydney', 'Northmead', '2152', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('123', 'Australia', 'NSW', 'Sydney', 'Baulkham Hills', '2153', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('124', 'Australia', 'NSW', 'Sydney', 'Castle Hill', '2154', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('125', 'Australia', 'NSW', 'Sydney', 'Kellyville', '2155', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('126', 'Australia', 'NSW', 'Sydney', 'Kenthurst', '2156', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('127', 'Australia', 'NSW', 'Sydney', 'Glenorie', '2157', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('128', 'Australia', 'NSW', 'Sydney', 'Dural', '2158', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('129', 'Australia', 'NSW', 'Sydney', 'Galston', '2159', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('130', 'Australia', 'NSW', 'Sydney', 'Merrylands', '2160', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('131', 'Australia', 'NSW', 'Sydney', 'Yennora', '2161', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('132', 'Australia', 'NSW', 'Sydney', 'Chester Hill', '2162', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('133', 'Australia', 'NSW', 'Sydney', 'Villawood', '2163', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('134', 'Australia', 'NSW', 'Sydney', 'Smithfield/W.Park', '2164', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('135', 'Australia', 'NSW', 'Sydney', 'Fairfield', '2165', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('136', 'Australia', 'NSW', 'Sydney', 'Lansvale', '2166', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('137', 'Australia', 'NSW', 'Sydney', 'Liverpool', '2170', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('138', 'Australia', 'NSW', 'Sydney', 'Hoxton Park', '2171', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('139', 'Australia', 'NSW', 'Sydney', 'St Johns Park', '2176', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('140', 'Australia', 'NSW', 'Sydney', 'Chullora', '2190', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('141', 'Australia', 'NSW', 'Sydney', 'Belfield', '2191', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('142', 'Australia', 'NSW', 'Sydney', 'Belmore', '2192', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('143', 'Australia', 'NSW', 'Sydney', 'Canterbury', '2193', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('144', 'Australia', 'NSW', 'Sydney', 'Campsie', '2194', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('145', 'Australia', 'NSW', 'Sydney', 'Lakemba', '2195', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('146', 'Australia', 'NSW', 'Sydney', 'Punchbowl', '2196', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('147', 'Australia', 'NSW', 'Sydney', 'Georges Hall', '2198', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('148', 'Australia', 'NSW', 'Sydney', 'Yagoona', '2199', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('149', 'Australia', 'NSW', 'Sydney', 'Bankstown', '2200', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('150', 'Australia', 'NSW', 'Sydney', 'Dulwich Hill', '2203', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('151', 'Australia', 'NSW', 'Sydney', 'Marrickville', '2204', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('152', 'Australia', 'NSW', 'Sydney', 'Arncliffe', '2205', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('153', 'Australia', 'NSW', 'Sydney', 'Earlwood', '2206', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('154', 'Australia', 'NSW', 'Sydney', 'Bexley', '2207', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('155', 'Australia', 'NSW', 'Sydney', 'Kingsgrove', '2208', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('156', 'Australia', 'NSW', 'Sydney', 'Beverly Hills', '2209', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('157', 'Australia', 'NSW', 'Sydney', 'Peakhurst', '2210', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('158', 'Australia', 'NSW', 'Sydney', 'Padstow', '2211', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('159', 'Australia', 'NSW', 'Sydney', 'Revesby', '2212', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('160', 'Australia', 'NSW', 'Sydney', 'East Hills', '2213', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('161', 'Australia', 'NSW', 'Sydney', 'Milperra', '2214', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('162', 'Australia', 'NSW', 'Sydney', 'Rockdale', '2216', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('163', 'Australia', 'NSW', 'Sydney', 'Kogarah', '2217', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('164', 'Australia', 'NSW', 'Sydney', 'Carlton', '2218', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('165', 'Australia', 'NSW', 'Sydney', 'Sans Souci', '2219', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('166', 'Australia', 'NSW', 'Sydney', 'Hurstville', '2220', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('167', 'Australia', 'NSW', 'Sydney', 'Blakehurst', '2221', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('168', 'Australia', 'NSW', 'Sydney', 'Penshurst', '2222', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('169', 'Australia', 'NSW', 'Sydney', 'Mortdale', '2223', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('170', 'Australia', 'NSW', 'Sydney', 'Sylvania', '2224', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('171', 'Australia', 'NSW', 'Sydney', 'Oyster Bay', '2225', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('172', 'Australia', 'NSW', 'Sydney', 'Jannali', '2226', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('173', 'Australia', 'NSW', 'Sydney', 'Gymea', '2227', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('174', 'Australia', 'NSW', 'Sydney', 'Miranda', '2228', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('175', 'Australia', 'NSW', 'Sydney', 'Caringbah', '2229', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('176', 'Australia', 'NSW', 'Sydney', 'Cronulla', '2230', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('177', 'Australia', 'NSW', 'Sydney', 'Kurnell', '2231', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('178', 'Australia', 'NSW', 'Sydney', 'Sutherland', '2232', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('179', 'Australia', 'NSW', 'Sydney', 'Heathcote', '2233', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('180', 'Australia', 'NSW', 'Sydney', 'Menai', '2234', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('181', 'Australia', 'NSW', 'Sydney', 'Campbelltown', '2560', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('182', 'Australia', 'NSW', 'Sydney', 'Ingleburn', '2565', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('183', 'Australia', 'NSW', 'Sydney', 'Minto', '2566', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('184', 'Australia', 'NSW', 'Sydney', 'Narellan', '2567', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('185', 'Australia', 'NSW', 'Sydney', 'Camden', '2570', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('186', 'Australia', 'NSW', 'Sydney', 'Mulgoa', '2745', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('187', 'Australia', 'NSW', 'Sydney', 'Kingswood', '2747', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('188', 'Australia', 'NSW', 'Sydney', 'Castlereagh', '2749', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('189', 'Australia', 'NSW', 'Sydney', 'Penrith', '2750', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('190', 'Australia', 'NSW', 'Sydney', 'Silverdale', '2752', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('191', 'Australia', 'NSW', 'Sydney', 'Richmond', '2753', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('192', 'Australia', 'NSW', 'Sydney', 'North Richmond', '2754', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('193', 'Australia', 'NSW', 'Sydney', 'Windsor', '2756', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('194', 'Australia', 'NSW', 'Sydney', 'Kurrajong', '2758', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('195', 'Australia', 'NSW', 'Sydney', 'Erskine Park', '2759', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('196', 'Australia', 'NSW', 'Sydney', 'St Marys', '2760', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('197', 'Australia', 'NSW', 'Sydney', 'Glendenning', '2761', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('198', 'Australia', 'NSW', 'Sydney', 'Quakers Hill', '2763', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('199', 'Australia', 'NSW', 'Sydney', 'Riverstone', '2765', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('200', 'Australia', 'NSW', 'Sydney', 'Rooty Hill', '2766', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('201', 'Australia', 'NSW', 'Sydney', 'Parklea', '2768', '2014-12-09 16:06:16', '2014-12-09 16:06:20');
INSERT INTO suburbs VALUES ('202', 'Australia', 'NSW', 'Sydney', 'Minchinbury', '2770', '2014-12-09 16:06:16', '2014-12-09 16:06:20');


/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;