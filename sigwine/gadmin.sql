/*
SQLyog Community v11.2 (32 bit)
MySQL - 5.0.45-community-nt : Database - gadmin
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`gadmin` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci */;

USE `gadmin`;

/*Table structure for table `user_master` */

DROP TABLE IF EXISTS `user_master`;

CREATE TABLE `user_master` (
  `u_id` int(10) NOT NULL auto_increment,
  `ur_id` int(10) default NULL,
  `user_name` varchar(255) default NULL,
  `user_pass` varchar(255) default NULL,
  `first_name` varchar(255) default NULL,
  `last_name` varchar(255) default NULL,
  `user_image` varchar(255) default NULL,
  `created_by` int(10) default '1',
  `created_on` datetime default NULL,
  `modified_by` int(10) default '1',
  `modified_on` datetime default NULL,
  `is_active` int(1) default '0',
  `is_delete` int(1) default '0',
  PRIMARY KEY  (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `user_master` */

insert  into `user_master`(`u_id`,`ur_id`,`user_name`,`user_pass`,`first_name`,`last_name`,`user_image`,`created_by`,`created_on`,`modified_by`,`modified_on`,`is_active`,`is_delete`) values (1,1,'admin','Y3JlZGVuY3lzLmNvbQ==','Admin','Admin',NULL,1,NULL,1,NULL,0,0);

/*Table structure for table `user_role` */

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `ur_id` int(10) NOT NULL auto_increment,
  `role_name` varchar(100) default NULL,
  `is_active` int(1) default '0',
  `is_delete` int(1) default '0',
  PRIMARY KEY  (`ur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `user_role` */

insert  into `user_role`(`ur_id`,`role_name`,`is_active`,`is_delete`) values (1,'super admin',0,0),(2,'admin',0,0),(3,'developer',0,0),(4,'tester',0,0);

/*Table structure for table `user_type` */

DROP TABLE IF EXISTS `user_type`;

CREATE TABLE `user_type` (
  `ut_id` int(10) NOT NULL auto_increment,
  `type_name` varchar(255) default NULL,
  PRIMARY KEY  (`ut_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `user_type` */

insert  into `user_type`(`ut_id`,`type_name`) values (1,'Web'),(2,'android'),(3,'ios');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
