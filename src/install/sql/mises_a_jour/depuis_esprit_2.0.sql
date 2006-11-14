--
-- Modifications apportées à la DB depuis Esprit v2.0
--

ALTER TABLE `TypeObjetForm` DROP `DescCourteTypeObj`;

INSERT INTO `TypeObjetForm` VALUES (1, 'QTexteLong', 'Question ouverte de type « texte long »');
INSERT INTO `TypeObjetForm` VALUES (2, 'QTexteCourt', 'Question ouverte de type « texte court »');
INSERT INTO `TypeObjetForm` VALUES (3, 'QNombre', 'Question semi-ouverte de type « nombre »');
INSERT INTO `TypeObjetForm` VALUES (4, 'QListeDeroul', 'Question fermée de type « liste déroulante »');
INSERT INTO `TypeObjetForm` VALUES (5, 'QRadio', 'Question fermée de type « radio »');
INSERT INTO `TypeObjetForm` VALUES (6, 'QCocher', 'Question fermée de type « case à cocher »');
INSERT INTO `TypeObjetForm` VALUES (7, 'MPTexte', 'Elément de mise en page de type « texte »');
INSERT INTO `TypeObjetForm` VALUES (8, 'MPSeparateur', 'Elément de mise en page de type « ligne de séparation »');


-- ajouts d'index et clés primaires, effectués le 11-09-2006, pour la r129
ALTER TABLE Activ
    ADD INDEX IdRubrique (IdRubrique);


ALTER TABLE Chat
    ADD INDEX IdRubrique (IdRubrique),
    ADD INDEX IdSousActiv (IdSousActiv);


ALTER TABLE DossierFormations
    ADD INDEX IdPers (IdPers);


ALTER TABLE DossierFormations_Formation
    ADD PRIMARY KEY (IdDossierForms, IdForm);


ALTER TABLE Equipe
    ADD INDEX IdForm (IdForm),
    ADD INDEX IdMod (IdMod),
    ADD INDEX IdRubrique (IdRubrique),
    ADD INDEX IdActiv (IdActiv),
    ADD INDEX IdSousActiv (IdSousActiv);


ALTER TABLE Equipe_Membre
    DROP INDEX IdEquipe,
    ADD PRIMARY KEY (IdEquipe, IdPers);


ALTER TABLE Evenement
    ADD INDEX IdTypeEven (IdTypeEven),
    ADD INDEX IdPers (IdPers);


ALTER TABLE Evenement_Detail
    ADD INDEX IdEven (IdEven),
    ADD INDEX IdForm (IdForm);


ALTER TABLE Formation
    ADD INDEX IdPers (IdPers);


ALTER TABLE Formation_Concepteur
    DROP INDEX IdForm,
    ADD PRIMARY KEY (IdForm, IdPers);


ALTER TABLE Formation_Inscrit
    DROP INDEX IdForm,
    ADD PRIMARY KEY (IdForm, IdPers);


ALTER TABLE Formation_Resp
    DROP INDEX IdForm,
    ADD PRIMARY KEY (IdForm, IdPers);


ALTER TABLE Formation_Tuteur
    DROP INDEX IdForm,
    ADD PRIMARY KEY (IdForm, IdPers);


ALTER TABLE Formulaire
    ADD INDEX IdPers (IdPers);


ALTER TABLE FormulaireComplete
    ADD INDEX IdPers (IdPers),
    ADD INDEX IdForm (IdForm);


ALTER TABLE FormulaireComplete_SousActiv
    ADD UNIQUE IdFC (IdFC, IdSousActiv),
    ADD INDEX IdDest (IdDest),
    ADD INDEX IdFormSousActivSource (IdFormSousActivSource);


ALTER TABLE Forum
    ADD INDEX IdForumParent (IdForumParent),
    ADD INDEX IdMod (IdMod),
    ADD INDEX IdRubrique (IdRubrique),
    ADD INDEX IdSousActiv (IdSousActiv),
    ADD INDEX IdPers (IdPers);


ALTER TABLE ForumPrefs
    ADD UNIQUE IdForum (IdForum, IdPers);


ALTER TABLE ForumPrefs_CopieCourrielEquipe
    DROP INDEX IdSujetForum,
    ADD PRIMARY KEY (IdForumPrefs, IdEquipe);


ALTER TABLE Glossaire
    ADD INDEX IdForm (IdForm),
    ADD INDEX IdMod (IdMod),
    ADD INDEX IdRubrique (IdRubrique),
    ADD INDEX IdActiv (IdActiv),
    ADD INDEX IdSousActiv (IdSousActiv);


ALTER TABLE MessageForum
    ADD INDEX IdSujetForum (IdSujetForum),
    ADD INDEX IdPers (IdPers);


ALTER TABLE MessageForum_Equipe
    DROP INDEX IdMessageForum,
    ADD PRIMARY KEY (IdMessageForum, IdEquipe);


ALTER TABLE MessageForum_Ressource
    ADD PRIMARY KEY (IdMessageForum, IdRes);


ALTER TABLE Module
    ADD INDEX IdForm (IdForm),
    ADD INDEX IdPers (IdPers),
    ADD INDEX IdIntitule (IdIntitule);


ALTER TABLE Module_Concepteur
    DROP INDEX Concepteur,
    ADD PRIMARY KEY (IdMod, IdPers);


ALTER TABLE Module_Inscrit
    DROP INDEX Inscrit,
    ADD PRIMARY KEY (IdMod, IdPers);


ALTER TABLE Module_Rubrique
    ADD INDEX IdMod (IdMod),
    ADD INDEX IdPers (IdPers),
    ADD INDEX IdIntitule (IdIntitule);


ALTER TABLE Module_Tuteur
    DROP INDEX IdMod,
    ADD PRIMARY KEY (IdMod, IdPers);


ALTER TABLE ObjetFormulaire
    ADD INDEX IdTypeObj (IdTypeObj),
    ADD INDEX IdForm (IdForm);


ALTER TABLE Projet
    MODIFY NomProj varchar(80) NOT NULL DEFAULT '' COMMENT '',
    ADD PRIMARY KEY (NomProj);


ALTER TABLE Reponse
    ADD INDEX IdObjForm (IdObjForm);


ALTER TABLE Ressource
    ADD INDEX IdPers (IdPers),
    ADD INDEX IdFormat (IdFormat);


ALTER TABLE Ressource_SousActiv
    ADD UNIQUE IdSousActiv (IdSousActiv, IdRes),
    ADD INDEX IdDest (IdDest),
    ADD INDEX IdResSousActivSource (IdResSousActivSource);


ALTER TABLE Ressource_SousActiv_FichierEvaluation
    DROP INDEX IdResSousActiv,
    ADD PRIMARY KEY (IdResSousActiv, IdRes);


ALTER TABLE Ressource_SousActiv_Vote
    DROP INDEX Idx,
    ADD INDEX IdResSousActiv (IdResSousActiv, IdPers);


ALTER TABLE SousActiv
    ADD INDEX IdTypeSousActiv (IdTypeSousActiv),
    ADD INDEX IdActiv (IdActiv),
    ADD INDEX IdPers (IdPers);


ALTER TABLE SousActivInvisible
    ADD PRIMARY KEY (IdSousActiv, IdPers);


ALTER TABLE SujetForum
    ADD INDEX IdForum (IdForum),
    ADD INDEX IdPers (IdPers);


ALTER TABLE SujetForum_Equipe
    DROP INDEX IdSujetForum,
    ADD PRIMARY KEY (IdSujetForum, IdEquipe);


-- Modification pour l'auto-correction des activités en ligne, effectué le 27-10-2006, pour la r194
ALTER TABLE Reponse 
	RENAME PropositionReponse


ALTER TABLE PropositionReponse
	DROP `FeedbackReponse`


ALTER TABLE PropositionReponse
	DROP `CorrectionReponse`


ALTER TABLE `PropositionReponse` 
	CHANGE `IdReponse` `IdPropRep` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT


ALTER TABLE `PropositionReponse` 
	CHANGE `TexteReponse` `TextePropRep` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL 


ALTER TABLE `PropositionReponse` 
	CHANGE `OrdreReponse` `OrdrePropRep` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'


ALTER TABLE `PropositionReponse` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'


ALTER TABLE `PropositionReponse` 
	ADD `ScorePropRep` INT NOT NULL DEFAULT '0' AFTER `OrdrePropRep` 


ALTER TABLE `PropositionReponse` 
	ADD `FeedbackPropRep` TEXT NOT NULL AFTER `ScorePropRep` 


ALTER TABLE `Reponse_Axe` 
	CHANGE `IdReponse` `IdPropRep` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

-- Modification des activités en ligne, effectué le 31-10-2006, pour la r198
ALTER TABLE `ObjetFormulaire` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
	CHANGE `IdForm` `IdFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
	CHANGE `OrdreObjForm` `OrdreObjFormul` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `MPSeparateur` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `MPTexte` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `QCocher` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `QListeDeroul` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `QNombre` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `QRadio` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `QTexteCourt` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `QTexteLong` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `ReponseCar` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `ReponseEntier` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `ReponseFlottant` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `ReponseTexte` 
	CHANGE `IdObjForm` `IdObjFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

-- Modification des activités en ligne, effectué le 07-11-2006, pour la r209
ALTER TABLE `Formulaire` 
	CHANGE `IdForm` `IdFormul` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT 

ALTER TABLE `FormulaireComplete` 
	CHANGE `IdForm` `IdFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `Formulaire_Axe` 
	CHANGE `IdFormul` `IdFormul` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'


-- Modification des activités en ligne, effectué le 07-11-2006, pour la r210
ALTER TABLE `TypeObjetForm` 
	RENAME `TypeObjetFormul`
	
-- Modification des activités en ligne, effectué le 14-11-2006, pour la r218
ALTER TABLE `Formulaire` 
	ADD `AutoCorrection` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `Type`
