INSERT INTO TypeSousActiv VALUES ( 13 , 'LIEN_HOTPOTATOES' );

CREATE TABLE Hotpotatoes (
  `IdHotpot` mediumint(8) unsigned NOT NULL auto_increment,
  `Titre` varchar(100) NOT NULL,
  `Fichier` varchar(255) NOT NULL,
  `Statut` enum('simple','multiple') NOT NULL,
  `Type` enum('cloze','cross','match','mix','quiz') NOT NULL,
  `IdPers` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`IdHotpot`)
) ;


CREATE TABLE Hotpotatoes_Score (
  `IdHotpotScore` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `IdHotpot` MEDIUMINT UNSIGNED NOT NULL ,
  `IdPers` INT UNSIGNED NOT NULL ,
  `Score` TINYINT NOT NULL ,
  `Detail` TEXT NOT NULL ,
  `DateDebut` TIMESTAMP NOT NULL DEFAULT 0 ,
  `DateFin` TIMESTAMP NOT NULL DEFAULT 0 ,
  `DateModif` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL
) ;
