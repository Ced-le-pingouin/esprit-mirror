
-- Changement du format de la table Projet

CREATE TABLE `ProjetNEW` (
  `Nom` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `Valeur` text collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`Nom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO ProjetNEW SET Nom='NomProj', Valeur=(SELECT NomProj FROM Projet);
INSERT INTO ProjetNEW SET Nom='Email', Valeur=(SELECT Email FROM Projet);
INSERT INTO ProjetNEW SET Nom='NumPortAwareness', Valeur=(SELECT NumPortAwareness FROM Projet);
INSERT INTO ProjetNEW SET Nom='NumPortChat', Valeur=(SELECT NumPortChat FROM Projet);
INSERT INTO ProjetNEW SET Nom='UrlAccueil', Valeur=(SELECT UrlAccueil FROM Projet);
INSERT INTO ProjetNEW SET Nom='Version', Valeur='2.2';

DROP TABLE Projet;

RENAME TABLE ProjetNEW TO Projet;