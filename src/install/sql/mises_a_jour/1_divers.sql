--
-- Modifications apportées à la DB depuis Esprit v2.1
--

-- Constante présente dans le code PHP, mais absente de la table de DB 
-- correspondante (voir r259 pour infos)
INSERT INTO TypeSousActiv VALUES (12,'LIEN_TABLEAU_DE_BORD');

-- Modification pour l'auto-correction des activités en ligne, effectué le 27-10-2006, pour la r194
ALTER TABLE Reponse 
	RENAME PropositionReponse;


ALTER TABLE PropositionReponse
	DROP `FeedbackReponse`;


ALTER TABLE PropositionReponse
	DROP `CorrectionReponse`;


ALTER TABLE `PropositionReponse` 
	CHANGE `IdReponse` `IdPropRep` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `PropositionReponse` 
	CHANGE `TexteReponse` `TextePropRep` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


ALTER TABLE `PropositionReponse` 
	CHANGE `OrdreReponse` `OrdrePropRep` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0';


ALTER TABLE `PropositionReponse` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';


ALTER TABLE `PropositionReponse` 
	ADD `ScorePropRep` INT NOT NULL DEFAULT '0' AFTER `OrdrePropRep`;


ALTER TABLE `PropositionReponse` 
	ADD `FeedbackPropRep` TEXT NOT NULL AFTER `ScorePropRep`;


ALTER TABLE `Reponse_Axe` 
	CHANGE `IdReponse` `IdPropRep` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

-- Modification des activités en ligne, effectué le 31-10-2006, pour la r198
ALTER TABLE `ObjetFormulaire` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
	CHANGE `IdForm` `IdFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
	CHANGE `OrdreObjForm` `OrdreObjFormul` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `MPSeparateur` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `MPTexte` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `QCocher` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `QListeDeroul` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `QNombre` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `QRadio` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `QTexteCourt` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `QTexteLong` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `ReponseCar` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `ReponseEntier` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `ReponseFlottant` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `ReponseTexte` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

-- Modification des activités en ligne, effectué le 07-11-2006, pour la r209
ALTER TABLE `Formulaire` 
	CHANGE `IdForm` `IdFormul` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `FormulaireComplete` 
	CHANGE `IdForm` `IdFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `Formulaire_Axe` 
	CHANGE `IdFormul` `IdFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

-- Modification des activités en ligne, effectué le 07-11-2006, pour la r210
ALTER TABLE `TypeObjetForm` 
	RENAME `TypeObjetFormul`;

-- Modification des activités en ligne, effectué le 14-11-2006, pour la r218
ALTER TABLE `Formulaire` 
	ADD `AutoCorrection` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `Type`;

-- Modification des activités en ligne, effectué le 22-11-2006, pour la r235
ALTER TABLE `ReponseEntier` 
	CHANGE `IdReponse` `IdPropRep` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

-- Modification des activités en ligne, effectué le 17-01-2007, pour la r309
ALTER TABLE `Formulaire` 
	ADD `MethodeCorrection` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `AutoCorrection`;