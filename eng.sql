/*
SQLyog Ultimate v11.52 (64 bit)
MySQL - 5.5.40-0ubuntu0.14.04.1 : Database - eng
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`eng` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `eng`;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `key` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `settings` */

insert  into `settings`(`key`,`value`) values ('keyboard_type_number','7'),('keyboard_type_repeat_number','9'),('learn_words_number','10'),('mouse_type_number','5'),('select_word_number','3'),('show_word_number','1');

/*Table structure for table `words` */

DROP TABLE IF EXISTS `words`;

CREATE TABLE `words` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `en` varchar(100) NOT NULL,
  `ru` varchar(100) NOT NULL,
  `repeated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `state` enum('wait','on_learn','on_repeat','learned') NOT NULL DEFAULT 'wait',
  `on_repeat_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `en` (`en`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

/*Data for the table `words` */

insert  into `words`(`id`,`en`,`ru`,`repeated`,`state`,`on_repeat_at`) values (5,'14','2 × 7',0,'wait',NULL),(8,'6','3 × 2',0,'wait',NULL),(9,'9','3 × 3',0,'wait',NULL),(11,'15','3 × 5',0,'wait',NULL),(16,'8','4 × 2',0,'wait',NULL),(24,'10','5 × 2',0,'wait',NULL),(26,'20','5 × 4',0,'wait',NULL),(27,'25','5 × 5',0,'wait',NULL),(32,'12','6 × 2',0,'wait',NULL),(35,'30','6 × 5',0,'wait',NULL),(41,'21','7 × 3',0,'wait',NULL),(42,'28','7 × 4',0,'wait',NULL),(43,'35','7 × 5',0,'wait',NULL),(44,'42','7 × 6',0,'wait',NULL),(45,'49','7 × 7',0,'wait',NULL),(48,'16','8 × 2',0,'wait',NULL),(49,'24','8 × 3',0,'wait',NULL),(50,'32','8 × 4',0,'wait',NULL),(51,'40','8 × 5',0,'wait',NULL),(52,'48','8 × 6',0,'wait',NULL),(53,'56','8 × 7',0,'wait',NULL),(54,'64','8 × 8',0,'wait',NULL),(56,'18','9 × 2',0,'wait',NULL),(57,'27','9 × 3',0,'wait',NULL),(58,'36','9 × 4',0,'wait',NULL),(59,'45','9 × 5',0,'wait',NULL),(60,'54','9 × 6',0,'wait',NULL),(61,'63','9 × 7',0,'wait',NULL),(62,'72','9 × 8',0,'wait',NULL),(63,'81','9 × 9',0,'wait',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
