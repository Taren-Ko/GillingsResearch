-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: db
-- ------------------------------------------------------
-- Server version	5.7.16

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
-- Table structure for table `AdminUser`
--

DROP TABLE IF EXISTS `AdminUser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdminUser` (
  `onyen` varchar(50) NOT NULL,
  `f_name` text NOT NULL,
  `l_name` text NOT NULL,
  PRIMARY KEY (`onyen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AdminUser`
--

LOCK TABLES `AdminUser` WRITE;
/*!40000 ALTER TABLE `AdminUser` DISABLE KEYS */;
/*!40000 ALTER TABLE `AdminUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Author`
--

DROP TABLE IF EXISTS `Author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Author` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` text NOT NULL,
  `l_name` text NOT NULL,
  `department` text,
  `is_unc_faculty` tinyint(1) NOT NULL,
  `degree_level` enum('ba','bs','mph','phd','md','mpa','ms') DEFAULT NULL COMMENT 'TODO go back and add more degrees, also account for fact that people have more than one degree',
  PRIMARY KEY (`author_id`),
  UNIQUE KEY `Author_author_id_uindex` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Author`
--

LOCK TABLES `Author` WRITE;
/*!40000 ALTER TABLE `Author` DISABLE KEYS */;
/*!40000 ALTER TABLE `Author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthorNonUNC`
--

DROP TABLE IF EXISTS `AuthorNonUNC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthorNonUNC` (
  `author_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  PRIMARY KEY (`author_id`,`institution_id`),
  KEY `Author_Non_UNC_Institution_institution_id_fk` (`institution_id`),
  CONSTRAINT `Author_Non_UNC_Author_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `Author` (`author_id`),
  CONSTRAINT `Author_Non_UNC_Institution_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `Institution` (`institution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthorNonUNC`
--

LOCK TABLES `AuthorNonUNC` WRITE;
/*!40000 ALTER TABLE `AuthorNonUNC` DISABLE KEYS */;
/*!40000 ALTER TABLE `AuthorNonUNC` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthorUNC`
--

DROP TABLE IF EXISTS `AuthorUNC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthorUNC` (
  `author_id` int(11) DEFAULT NULL,
  `bio_href` text NOT NULL,
  KEY `UNC_Author_Author_author_id_fk` (`author_id`),
  CONSTRAINT `UNC_Author_Author_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `Author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthorUNC`
--

LOCK TABLES `AuthorUNC` WRITE;
/*!40000 ALTER TABLE `AuthorUNC` DISABLE KEYS */;
/*!40000 ALTER TABLE `AuthorUNC` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category` (
  `category_name` text,
  `category_id` int(11) NOT NULL,
  `category_rules` int(11) DEFAULT NULL COMMENT 'TODO maybe use bag of words here? ',
  `categoryset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `Category_CategorySet_category_set_id_fk` (`categoryset_id`),
  CONSTRAINT `Category_CategorySet_category_set_id_fk` FOREIGN KEY (`categoryset_id`) REFERENCES `CategorySet` (`categoryset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
/*!40000 ALTER TABLE `Category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CategorySet`
--

DROP TABLE IF EXISTS `CategorySet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CategorySet` (
  `name` int(11) DEFAULT NULL,
  `categoryset_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`categoryset_id`),
  UNIQUE KEY `CategorySet_category_set_id_uindex` (`categoryset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CategorySet`
--

LOCK TABLES `CategorySet` WRITE;
/*!40000 ALTER TABLE `CategorySet` DISABLE KEYS */;
/*!40000 ALTER TABLE `CategorySet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Institution`
--

DROP TABLE IF EXISTS `Institution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Institution` (
  `long_name` text NOT NULL,
  `short_name` tinytext,
  `latitude` decimal(10,0) DEFAULT NULL,
  `longitude` decimal(10,0) DEFAULT NULL,
  `href` text,
  `institution_id` int(11) NOT NULL,
  PRIMARY KEY (`institution_id`),
  UNIQUE KEY `Institution_institution_id_uindex` (`institution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Institution`
--

LOCK TABLES `Institution` WRITE;
/*!40000 ALTER TABLE `Institution` DISABLE KEYS */;
/*!40000 ALTER TABLE `Institution` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Publication`
--

DROP TABLE IF EXISTS `Publication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publication` (
  `abstract` text,
  `title` text,
  `scopus_href` text,
  `scopus_id` text,
  `publication_id` int(11) NOT NULL AUTO_INCREMENT,
  `doi` text COMMENT 'Digital Object Identifier',
  PRIMARY KEY (`publication_id`),
  UNIQUE KEY `Publication_publication_id_uindex` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Publication`
--

LOCK TABLES `Publication` WRITE;
/*!40000 ALTER TABLE `Publication` DISABLE KEYS */;
/*!40000 ALTER TABLE `Publication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Publication_Authors`
--

DROP TABLE IF EXISTS `Publication_Authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publication_Authors` (
  `publication_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`publication_id`,`author_id`),
  KEY `Author_id_fk` (`author_id`),
  CONSTRAINT `Author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `Author` (`author_id`),
  CONSTRAINT `Publication_id_fk` FOREIGN KEY (`publication_id`) REFERENCES `Publication` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Publication_Authors`
--

LOCK TABLES `Publication_Authors` WRITE;
/*!40000 ALTER TABLE `Publication_Authors` DISABLE KEYS */;
/*!40000 ALTER TABLE `Publication_Authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Publication_CategorySets`
--

DROP TABLE IF EXISTS `Publication_CategorySets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publication_CategorySets` (
  `publication_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `categoryset_id` int(11) NOT NULL,
  PRIMARY KEY (`publication_id`,`categoryset_id`),
  KEY `Publication_CategorySets_CategorySet_category_set_id_fk` (`categoryset_id`),
  KEY `Publication_CategorySets_Category_category_set_id_fk` (`category_id`),
  CONSTRAINT `Publication_CategorySets_CategorySet_category_set_id_fk` FOREIGN KEY (`categoryset_id`) REFERENCES `CategorySet` (`categoryset_id`),
  CONSTRAINT `Publication_CategorySets_Category_category_set_id_fk` FOREIGN KEY (`category_id`) REFERENCES `Category` (`categoryset_id`),
  CONSTRAINT `Publication_CategorySets_Publication_publication_id_fk` FOREIGN KEY (`publication_id`) REFERENCES `Publication` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Publication_CategorySets`
--

LOCK TABLES `Publication_CategorySets` WRITE;
/*!40000 ALTER TABLE `Publication_CategorySets` DISABLE KEYS */;
/*!40000 ALTER TABLE `Publication_CategorySets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Publication_Keywords`
--

DROP TABLE IF EXISTS `Publication_Keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publication_Keywords` (
  `publication_id` int(11) NOT NULL,
  `keyword` varchar(50) NOT NULL,
  PRIMARY KEY (`publication_id`,`keyword`),
  CONSTRAINT `Publication_Keywords_Publication_publication_id_fk` FOREIGN KEY (`publication_id`) REFERENCES `Publication` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Publication_Keywords`
--

LOCK TABLES `Publication_Keywords` WRITE;
/*!40000 ALTER TABLE `Publication_Keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `Publication_Keywords` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-07 19:03:47
