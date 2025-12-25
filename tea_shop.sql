/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 5.7.26 : Database - tea_shop
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tea_shop` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `tea_shop`;

/*Table structure for table `banners` */

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `image_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `banners` */

insert  into `banners`(`id`,`title`,`description`,`image_url`,`link_url`,`sort_order`,`is_active`,`created_at`) values 
(1,'春茶上市','新鲜春茶，品味春天的第一缕茶香','images/banners/spring-tea.jpg','products.php?category=1',1,1,'2025-11-07 12:08:33'),
(2,'茶具套装','精美茶具，提升品茶体验','images/banners/tea-set.jpg','products.php?category=7',2,1,'2025-11-07 12:08:33'),
(3,'普洱茶专场','陈年普洱，越陈越香','images/banners/puer-tea.jpg','products.php?category=4',3,1,'2025-11-07 12:08:33'),
(4,'会员专享','注册会员享专属优惠','images/banners/member.jpg','register.php',4,1,'2025-11-07 12:08:33');

/*Table structure for table `cart` */

DROP TABLE IF EXISTS `cart`;

CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cart` */

insert  into `cart`(`id`,`user_id`,`product_id`,`quantity`,`created_at`) values 
(1,2,1,2,'2025-11-07 14:10:53'),
(2,2,5,1,'2025-11-07 14:10:53'),
(3,3,8,3,'2025-11-07 14:10:53'),
(4,3,12,1,'2025-11-07 14:10:53'),
(5,4,3,2,'2025-11-07 14:10:53'),
(6,4,17,1,'2025-11-07 14:10:53'),
(7,5,1,1,'2025-11-07 14:10:53'),
(8,5,6,2,'2025-11-07 14:10:53');

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`name`,`description`,`created_at`) values 
(1,'绿茶','未经发酵的茶叶，色泽翠绿，口感清新爽口，富含茶多酚和维生素','2025-11-07 09:14:18'),
(2,'红茶','全发酵茶，汤色红艳，滋味醇厚甘甜，具有暖胃养生功效','2025-11-07 09:14:18'),
(3,'乌龙茶','半发酵茶，兼具绿茶清香和红茶醇厚，香气浓郁持久','2025-11-07 09:14:18'),
(4,'普洱茶','后发酵茶，陈香独特，滋味醇厚回甘，具有收藏价值','2025-11-07 09:14:18'),
(5,'白茶','微发酵茶，色泽银白，汤色淡黄，滋味清鲜爽口','2025-11-07 09:14:18'),
(6,'花茶','以茶叶为基础，配以各种花卉窨制而成，香气芬芳怡人','2025-11-07 09:14:18'),
(7,'茶具','各类茶道用具，包括茶壶、茶杯、茶盘等配件','2025-11-07 09:14:18');

/*Table structure for table `order_items` */

DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `order_items` */

insert  into `order_items`(`id`,`order_id`,`product_id`,`quantity`,`price`) values 
(1,1,1,1,680.00),
(2,1,9,1,560.00),
(3,1,21,2,180.00),
(4,2,13,1,1280.00),
(5,2,5,1,880.00),
(6,2,25,1,80.00),
(7,3,3,2,360.00),
(8,3,7,1,380.00),
(9,3,26,1,120.00),
(10,4,2,2,420.00),
(11,4,6,1,520.00),
(12,5,10,1,680.00),
(13,5,18,1,320.00),
(14,6,14,1,1800.00),
(15,6,22,1,420.00),
(16,6,27,2,180.00),
(17,7,4,2,280.00),
(18,7,20,1,180.00),
(19,8,8,1,320.00),
(20,8,16,1,280.00),
(21,9,23,2,680.00),
(22,9,20,2,256.00),
(28,11,5,1,880.00),
(29,11,3,1,360.00),
(30,11,1,1,680.00),
(31,12,8,1,256.00);

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') COLLATE utf8_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `orders` */

insert  into `orders`(`id`,`user_id`,`total_amount`,`status`,`created_at`) values 
(1,2,1580.00,'delivered','2025-11-07 14:10:39'),
(2,3,2240.00,'shipped','2025-11-07 14:10:39'),
(3,4,860.00,'confirmed','2025-11-07 14:10:39'),
(4,5,1320.00,'pending','2025-11-07 14:10:39'),
(5,6,980.00,'delivered','2025-11-07 14:10:39'),
(6,7,2560.00,'delivered','2025-11-07 14:10:39'),
(7,2,720.00,'delivered','2025-11-06 14:10:39'),
(8,3,540.00,'cancelled','2025-11-05 14:10:39'),
(9,1,1872.00,'confirmed','2025-11-05 14:10:39'),
(11,1,1920.00,'pending','2025-11-07 21:31:46'),
(12,1,256.00,'delivered','2025-11-08 08:51:41');

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `featured` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`name`,`description`,`price`,`stock`,`category_id`,`image_url`,`featured`,`created_at`) values 
(1,'西湖龙井明前特级','产自杭州西湖核心产区，明前采摘，一芽一叶，外形扁平光滑，色泽翠绿，香气清高持久，滋味鲜爽甘醇。',680.00,49,1,'images/products/longjing.jpg',1,'2025-11-07 14:10:27'),
(2,'碧螺春一级','江苏太湖特产，条索纤细，卷曲成螺，满身披毫，银绿隐翠，香气浓郁，滋味鲜醇甘厚。',420.00,80,1,'images/products/biluochun.jpg',1,'2025-11-07 14:10:27'),
(3,'黄山毛峰特级','安徽黄山名茶，外形微卷，状似雀舌，绿中泛黄，银毫显露，香气如兰，韵味深长。',360.00,59,1,'images/products/maofeng.jpg',1,'2025-11-07 14:10:27'),
(4,'六安瓜片','唯一无芽无梗的茶叶，单片生叶制成，形似瓜子，色泽宝绿，滋味鲜醇回甘。',224.00,45,1,'images/products/liuan.jpg',0,'2025-11-07 14:10:27'),
(5,'金骏眉特级','福建武夷山正山小种分支，金毫显露，条索紧细，汤色金黄，滋味甘爽。',880.00,29,2,'images/products/jinjunmei.jpg',1,'2025-11-07 14:10:27'),
(6,'正山小种','世界红茶鼻祖，松烟香，桂圆味，汤色红浓，滋味醇厚。',520.00,70,2,'images/products/zhengshan.jpg',1,'2025-11-07 14:10:27'),
(7,'祁门红茶','世界三大高香红茶之一，祁门香独特，滋味醇厚，回味隽永。',380.00,55,2,'images/products/qimen.jpg',1,'2025-11-07 14:10:27'),
(8,'滇红特级','云南大叶种制作，金毫显露，香气鲜浓，滋味浓强鲜爽。',256.00,64,2,'images/products/dianhong.jpg',0,'2025-11-07 14:10:27'),
(9,'铁观音特级','安溪铁观音，砂绿明显，沉重似铁，汤色金黄，香气馥郁持久。',560.00,40,3,'images/products/tieguanyin.jpg',1,'2025-11-07 14:10:27'),
(10,'大红袍','武夷岩茶之王，岩韵明显，香气浓郁，滋味醇厚，回甘持久。',1280.00,20,3,'images/products/dahongpao.jpg',1,'2025-11-07 14:10:27'),
(11,'凤凰单丛','广东潮州特产，天然花香浓郁，滋味浓醇鲜爽，润喉回甘。',680.00,35,3,'images/products/dancong.jpg',1,'2025-11-07 14:10:27'),
(12,'冻顶乌龙','台湾名茶，香气清雅，汤色蜜绿，滋味甘醇浓厚。',384.00,50,3,'images/products/dongding.jpg',0,'2025-11-07 14:10:27'),
(13,'老班章古树茶','普洱茶王，茶气刚烈，厚重醇香，霸气十足，回甘持久。',2200.00,0,4,'images/products/laobanzhang.jpg',1,'2025-11-07 14:10:27'),
(14,'冰岛古树生普','茶中皇后，冰糖甜明显，茶汤金黄透亮，滋味醇厚。',1800.00,0,4,'images/products/bingdao.jpg',1,'2025-11-07 14:10:27'),
(15,'勐海熟普','经典熟茶，陈香显著，汤色红浓明亮，滋味醇厚顺滑。',450.00,60,4,'images/products/menghai.jpg',1,'2025-11-07 14:10:27'),
(16,'易武正山','香扬水柔，回甘持久，蜜香浓郁，汤色金黄透亮。',544.00,25,4,'images/products/yiwu.jpg',0,'2025-11-07 14:10:27'),
(17,'白毫银针特级','白茶之王，全芽头制作，银装素裹，汤色浅黄，滋味清鲜爽口。',780.00,28,5,'images/products/yinzhen.jpg',1,'2025-11-07 14:10:27'),
(18,'白牡丹一级','一芽一二叶，绿叶夹银毫，形似花朵，汤色杏黄，滋味清醇。',420.00,45,5,'images/products/mudan.jpg',0,'2025-11-07 14:10:27'),
(19,'寿眉陈年','陈放三年，香气醇和，汤色橙黄，滋味醇厚带甜。',280.00,74,5,'images/products/shoumei.jpg',1,'2025-11-07 14:10:27'),
(20,'茉莉花茶特级','七窨一提，花香鲜灵持久，茶汤黄亮，滋味醇厚鲜爽。',256.00,75,6,'images/products/jasmine.jpg',1,'2025-11-07 14:10:27'),
(21,'玫瑰花茶','平阴重瓣红玫瑰，香气浓郁，美容养颜，舒缓情绪。',180.00,100,6,'images/products/rose.jpg',0,'2025-11-07 14:10:27'),
(22,'桂花乌龙','桂花与乌龙茶的完美结合，花香茶香交融，滋味独特。',260.00,65,6,'images/products/osmanthus.jpg',0,'2025-11-07 14:10:27'),
(23,'宜兴紫砂壶','原矿紫泥，手工制作，透气性好，泡茶不走味。',680.00,22,7,'images/products/zisha.jpg',1,'2025-11-07 14:10:27'),
(24,'景德镇白瓷茶具套装','高白瓷，釉面光滑，包含茶壶、公道杯、品茗杯六只。',336.00,39,7,'images/products/porcelain.jpg',1,'2025-11-07 14:10:27'),
(25,'竹制茶盘','天然竹材，防水处理，排水顺畅，美观实用。',180.00,59,7,'images/products/tea_tray.jpg',0,'2025-11-07 14:10:27'),
(26,'建盏茶杯','宋代建窑复原，釉色变幻，提升茶汤口感。',320.00,35,7,'images/products/jianzhan.jpg',0,'2025-11-07 14:10:27');

/*Table structure for table `reviews` */

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `reviews` */

insert  into `reviews`(`id`,`user_id`,`product_id`,`order_id`,`rating`,`comment`,`created_at`) values 
(1,2,1,1,5,'龙井茶品质非常好，香气清高，滋味鲜爽，确实是明前特级，物有所值！','2025-11-07 14:09:50'),
(2,2,9,1,4,'铁观音香气浓郁，回甘不错，就是价格稍贵了一些。','2025-11-07 14:10:10'),
(3,3,13,2,5,'大红袍不愧是岩茶之王，岩韵明显，茶气十足，喝完浑身舒畅！','2025-11-07 14:10:10'),
(4,3,5,2,4,'金骏眉品质不错，金毫明显，汤色金黄透亮，值得推荐。','2025-11-07 14:10:10'),
(5,6,10,5,5,'凤凰单丛的花香太迷人了，喝起来心情都变好了，会继续回购！','2025-11-07 14:10:10'),
(6,6,18,5,4,'勐海熟普陈香明显，口感顺滑，性价比很高。','2025-11-07 14:10:10'),
(7,7,14,6,5,'冰岛古树茶的冰糖甜太惊艳了！茶汤金黄透亮，回甘持久，收藏价值高。','2025-11-07 14:10:10'),
(8,7,22,6,4,'景德镇茶具做工精细，白瓷很漂亮，泡茶效果不错。','2025-11-07 14:10:10'),
(9,2,4,7,4,'六安瓜片口感独特，无芽无梗的设计很特别，值得一试。','2025-11-07 14:10:10'),
(10,2,20,7,5,'玫瑰花茶香气浓郁，泡出来颜色很漂亮，女朋友很喜欢。','2025-11-07 14:10:10'),
(11,4,3,3,5,'黄山毛峰品质很好，兰花香明显，滋味鲜爽，物超所值！','2025-11-07 14:10:10'),
(12,5,6,4,4,'正山小种松烟香很特别，桂圆味明显，喜欢传统红茶的朋友不要错过。','2025-11-07 14:10:10'),
(13,6,11,5,5,'白毫银针品质上乘，银毫明显，汤色清澈，滋味清鲜，白茶首选！','2025-11-07 14:10:10'),
(14,7,15,6,4,'易武正山茶汤柔顺，蜜香浓郁，回甘持久，性价比不错。','2025-11-07 14:10:10'),
(19,1,8,12,5,'非常好 口感不错','2025-11-08 08:52:38');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8_unicode_ci DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`email`,`password`,`role`,`created_at`) values 
(1,'admin','admin@mingtea.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2025-11-07 14:09:12'),
(2,'茶艺师小王','wang@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','2025-11-07 14:09:15'),
(3,'爱喝茶的老李','li@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','2025-11-07 14:09:18'),
(4,'茶道爱好者','chadao@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','2025-11-07 14:09:20'),
(5,'绿茶控','green@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','2025-11-07 14:09:23'),
(6,'红茶达人','blacktea@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','2025-11-07 14:09:29'),
(7,'乌龙茶专家','oolong@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','2025-11-07 14:09:32'),
(8,'普洱收藏家','puer@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','2025-11-07 14:09:36');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
