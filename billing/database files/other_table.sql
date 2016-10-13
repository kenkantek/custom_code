/*
SQLyog Enterprise - MySQL GUI v6.5
MySQL - 5.1.30-community-log : Database - order1
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `contract` */

CREATE TABLE `contract` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `contract_number` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `contract` */

/*Table structure for table `contract_facility` */

CREATE TABLE `contract_facility` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `contract_id` bigint(20) NOT NULL,
  `facility_id` bigint(20) NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `unique` (`contract_id`,`facility_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `contract_facility` */

/*Table structure for table `contract_user` */

CREATE TABLE `contract_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `type` smallint(1) DEFAULT '0' COMMENT '1:admin, 0:user',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `contract_user` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;