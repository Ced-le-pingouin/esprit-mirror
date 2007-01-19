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
-- Dumping data for table `Accueil`
--


/*!40000 ALTER TABLE `Accueil` DISABLE KEYS */;
LOCK TABLES `Accueil` WRITE;
INSERT INTO `Accueil` VALUES (1,'texte','Bienvenue dans la plate-forme Esprit !\r\n\r\nEsprit est une plateforme de formation à distance qui permet aux étudiants de réaliser des activités d\'apprentissage, seuls ou en petits groupes, sous la supervision de tuteurs en ligne.\r\n',NULL,1,NULL,NULL,1,1,'2006-11-29','2006-11-29');
INSERT INTO `Accueil` VALUES (2,'breve','La version 2.1 d\'Esprit est disponible sur le site de développement \r\n[https://sourcesup.cru.fr/frs/?group_id=204 SourceSup].',NULL,1,'2006-11-29','2007-01-31',1,1,'2006-11-29','2006-11-29');
INSERT INTO `Accueil` VALUES (3,'breve','La version 2.2 d\'Esprit offrira une page d\'accueil améliorée !',NULL,1,'2006-11-30','2007-01-31',1,2,'2006-11-29','2006-11-29');
INSERT INTO `Accueil` VALUES (4,'lien','La plate-forme Esprit de Mons','http://ute2.umh.ac.be/esprit/login-index.php','popup',NULL,NULL,1,1,'2006-11-29','2006-11-29');
INSERT INTO `Accueil` VALUES (5,'lien','La plate-forme de test des développements','http://ute.umh.ac.be/esprit_dev/login.php','popup',NULL,NULL,1,2,'2006-11-29','2006-11-29');
UNLOCK TABLES;
/*!40000 ALTER TABLE `Accueil` ENABLE KEYS */;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

