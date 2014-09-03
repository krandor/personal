-- MySQL dump 10.13  Distrib 5.1.36, for Win32 (ia32)
--
-- Host: localhost    Database: sns_portfolios
-- ------------------------------------------------------
-- Server version	5.1.36-community-log

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

--
-- Table structure for table `lkup_category_id`
--

DROP TABLE IF EXISTS `lkup_category_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkup_category_id` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cddef` varchar(45) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lkup_category_id`
--

LOCK TABLES `lkup_category_id` WRITE;
/*!40000 ALTER TABLE `lkup_category_id` DISABLE KEYS */;
INSERT INTO `lkup_category_id` VALUES (1,'Software '),(2,'Graphical Artwork'),(3,'Sculpture'),(4,'Literature');
/*!40000 ALTER TABLE `lkup_category_id` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lkup_spec_id`
--

DROP TABLE IF EXISTS `lkup_spec_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkup_spec_id` (
  `spec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cddef` varchar(45) NOT NULL,
  PRIMARY KEY (`spec_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lkup_spec_id`
--

LOCK TABLES `lkup_spec_id` WRITE;
/*!40000 ALTER TABLE `lkup_spec_id` DISABLE KEYS */;
INSERT INTO `lkup_spec_id` VALUES (1,'Contribution'),(2,'Contribution Challenges'),(3,'Contribution Timeframe');
/*!40000 ALTER TABLE `lkup_spec_id` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `port_news`
--

DROP TABLE IF EXISTS `port_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `port_news` (
  `news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(45) NOT NULL,
  `body` text NOT NULL,
  `create_by` int(10) unsigned NOT NULL,
  `create_dt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `port_news`
--

LOCK TABLES `port_news` WRITE;
/*!40000 ALTER TABLE `port_news` DISABLE KEYS */;
INSERT INTO `port_news` VALUES (1,'Site is taking shap','I\'ve been working on this for about 2 weeks now. Some major functionality has already been built, but more is still to come.\r\n\r\nSo far what\'s been accomplished:\r\n1-Users can register\r\n2-Users can log in and out\r\n3-Users can add projects\r\n4-Users can edit projects\r\n5-Users can edit their main account info\r\n6-Portfolios can be viewed\r\n7-Users can view the projects they have added and those that they are associated with\r\n\r\nWhat still needs to be done:\r\n1-Search feature to pull back only those profiles with projects that meet your needs\r\n2-Users can edit their Bio\r\n3-Improve on edit pages (for both the users and for the projects)\r\n4-Association approval process (main project author must approve associations)\r\n5-Email notifications',1,1259181302);
/*!40000 ALTER TABLE `port_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `port_proj`
--

DROP TABLE IF EXISTS `port_proj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `port_proj` (
  `proj_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proj_name` varchar(80) NOT NULL,
  `cat_id` int(10) unsigned NOT NULL,
  `size` text NOT NULL,
  `components` text NOT NULL,
  `desc` text NOT NULL,
  `thumb_img` text,
  `create_dt` int(10) unsigned NOT NULL,
  `create_by` int(10) unsigned NOT NULL,
  `mod_dt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`proj_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `port_proj`
--

LOCK TABLES `port_proj` WRITE;
/*!40000 ALTER TABLE `port_proj` DISABLE KEYS */;
INSERT INTO `port_proj` VALUES (1,'Highlander CRM',1,'Approx. 25000 Lines of VB.Net','VB.Net,C#,MySQL, XML, Visual Studio 2003, Visual Studio 2005, Excel 2003, Outlook 2003, The Gimp, Paint.Net','Customer Relationship Management system with a MySQL backend. This had to allow for fast and efficient data manipulation. The system had to allow for mass emailing, complex searching/filtering custom surveys, email storing and extensibility. This project consisted of the creation of a solid backend capable of storing millions of rows of data and an application that allowed 100+ employees to retrieve and manipulate data.','images/projects/1/1.png',1258487929,1,1259602272),(2,'SNS Portfolios',1,'Approx. 2000 Lines of PHP','PHP, mySQL, HTML','A web system which allows users to create accounts, add projects they have worked on, and associate themselves with already existing projects to form an online portfolio of their work.','images/projects/2/2.jpg',1259594422,1,1259601576),(3,'cyberXMPP',1,'Approx. 600 Lines of C#','C#, XMPP','A simple Jabber/XMPP client which allows users to connect to any Jabber/XMPP server. Designed for use within an office environment spaning many offices or even buildings to aid in collaboration. A central Jabber/XMPP server such as Openfire is required, unless a public server is available.','images/projects/3/3.png',1259594953,1,1259604288),(4,'CyberTweets',1,'Approx. 1000 Lines of C#','C#, mySQL, Twitter API','A twitter client that allows users to search for keyterms, add users to lists, and then view user histories. All data is then stored in a mySQL schema.','images/projects/4/4.png',1259595999,1,1259604307),(5,'e107 RSS Reader Plugin',1,'Approx. 1000 Lines of PHP','PHP, e107 framework, mySQL','A plugin for the e107 CMS (http://www.e107.org). It allows users to add RSS feeds to display on their site.','images/projects/5/5.png',1259596166,1,1259604327);
/*!40000 ALTER TABLE `port_proj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `port_proj_imgs`
--

DROP TABLE IF EXISTS `port_proj_imgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `port_proj_imgs` (
  `proj_id` int(10) unsigned NOT NULL,
  `img_path` text NOT NULL,
  `img_alt` text,
  PRIMARY KEY (`proj_id`,`img_path`(100))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `port_proj_imgs`
--

LOCK TABLES `port_proj_imgs` WRITE;
/*!40000 ALTER TABLE `port_proj_imgs` DISABLE KEYS */;
/*!40000 ALTER TABLE `port_proj_imgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `port_proj_tags`
--

DROP TABLE IF EXISTS `port_proj_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `port_proj_tags` (
  `proj_id` int(10) unsigned NOT NULL,
  `tag` varchar(45) NOT NULL,
  PRIMARY KEY (`proj_id`,`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `port_proj_tags`
--

LOCK TABLES `port_proj_tags` WRITE;
/*!40000 ALTER TABLE `port_proj_tags` DISABLE KEYS */;
INSERT INTO `port_proj_tags` VALUES (1,'C#'),(1,'Custom Controls'),(1,'Excel 2003'),(1,'mySQL'),(1,'Outlook 2003'),(1,'Paint.NET'),(1,'The GIMP'),(1,'VB.Net'),(1,'Visual Studio'),(1,'XML'),(2,'HTML'),(2,'mySQL'),(2,'PHP'),(2,'Portfolios'),(2,'Web'),(3,'C#'),(3,'Jabber'),(3,'XMPP'),(4,'C#'),(4,'mySQL'),(4,'Twitter API'),(5,'e107'),(5,'e107 Plugin'),(5,'PHP');
/*!40000 ALTER TABLE `port_proj_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `port_user`
--

DROP TABLE IF EXISTS `port_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `port_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(80) NOT NULL,
  `passwd` text NOT NULL,
  `email_addr` varchar(90) NOT NULL,
  `user_f_name` varchar(80) NOT NULL,
  `user_l_name` varchar(80) NOT NULL,
  `img_path` text,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `create_dt` int(10) unsigned NOT NULL,
  `mod_dt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uni_name` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `port_user`
--

LOCK TABLES `port_user` WRITE;
/*!40000 ALTER TABLE `port_user` DISABLE KEYS */;
INSERT INTO `port_user` VALUES (1,'dancolomb','ccyFtxoulzzcM','daniel.colomb@gmail.com','Daniel','Colomb','images/users/1.jpg',16,1258581649,1259181302),(2,'zbarno','b7PZA84rp.arY','','Zachary','Barno','images/users/2.jpg',17,1258581890,0),(4,'krandor','4fkH/NvxrPnhA','krandor.delmarniol@gmail.com','Krandor','delMarniol','images/users/4.jpg',9,1258647764,1259181842);
/*!40000 ALTER TABLE `port_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `port_user_bio`
--

DROP TABLE IF EXISTS `port_user_bio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `port_user_bio` (
  `user_id` int(10) unsigned NOT NULL,
  `bio` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `port_user_bio`
--

LOCK TABLES `port_user_bio` WRITE;
/*!40000 ALTER TABLE `port_user_bio` DISABLE KEYS */;
INSERT INTO `port_user_bio` VALUES (1,'Over five years of computer related experience including the complete suite of Microsoft© products, networks, html, PHP, SQL, C++ and .Net. Currently maintain a Department of Defense Secret security clearance.');
/*!40000 ALTER TABLE `port_user_bio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_proj_assoc`
--

DROP TABLE IF EXISTS `user_proj_assoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_proj_assoc` (
  `user_id` int(10) unsigned NOT NULL,
  `proj_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`proj_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_proj_assoc`
--

LOCK TABLES `user_proj_assoc` WRITE;
/*!40000 ALTER TABLE `user_proj_assoc` DISABLE KEYS */;
INSERT INTO `user_proj_assoc` VALUES (1,1),(1,2),(1,3),(1,4),(1,5);
/*!40000 ALTER TABLE `user_proj_assoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_proj_spec`
--

DROP TABLE IF EXISTS `user_proj_spec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_proj_spec` (
  `user_id` int(10) unsigned NOT NULL,
  `proj_id` int(10) unsigned NOT NULL,
  `spec_id` int(10) unsigned NOT NULL,
  `fdval` text NOT NULL,
  PRIMARY KEY (`user_id`,`proj_id`,`spec_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_proj_spec`
--

LOCK TABLES `user_proj_spec` WRITE;
/*!40000 ALTER TABLE `user_proj_spec` DISABLE KEYS */;
INSERT INTO `user_proj_spec` VALUES (1,1,1,'Co-Developer, Co-Designer'),(1,1,3,'06/2007 - 11/2007'),(1,1,2,'Working within the confines of our networks high level of security. Developing for computers with limited rights. Using a desktop computer as our MySQL Server. ');
/*!40000 ALTER TABLE `user_proj_spec` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-11-30 13:23:07
