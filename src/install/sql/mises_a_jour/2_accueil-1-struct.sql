-- MySQL dump 10.9
--
-- Host: localhost    Database: esprit
-- ------------------------------------------------------
-- Server version	4.1.15-Debian_1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Accueil`
--

DROP TABLE IF EXISTS `Accueil`;
CREATE TABLE `Accueil` (
  `Id` smallint(5) unsigned NOT NULL auto_increment,
  `TypeContenu` enum('avert','texte','breve','lien') NOT NULL default 'texte',
  `Texte` text,
  `Lien` varchar(200) default NULL,
  `TypeLien` enum('new','page','popup','inactif') NOT NULL default 'popup',
  `DateDeb` date default NULL,
  `DateFin` date default NULL,
  `Visible` tinyint(1) NOT NULL default '1',
  `Ordre` smallint(6) default NULL,
  `DateCreation` date NOT NULL default '0000-00-00',
  `DateEdition` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

