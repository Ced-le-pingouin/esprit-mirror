-- phpMyAdmin SQL Dump
-- version 2.8.0.3
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lundi 11 Septembre 2006 à 15:07
-- Version du serveur: 4.1.18
-- Version de PHP: 4.4.2
-- 
-- Base de données: `esprit`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `Activ`
-- 

CREATE TABLE `Activ` (
  `IdActiv` int(10) unsigned NOT NULL auto_increment,
  `NomActiv` varchar(80) collate utf8_unicode_ci default NULL,
  `DescrActiv` varchar(200) collate utf8_unicode_ci default NULL,
  `DateDebActiv` datetime default NULL,
  `DateFinActiv` datetime default NULL,
  `ModaliteActiv` tinyint(4) default NULL,
  `AfficherModaliteActiv` tinyint(1) unsigned NOT NULL default '0',
  `StatutActiv` tinyint(4) default NULL,
  `AfficherStatutActiv` tinyint(1) unsigned NOT NULL default '0',
  `InscrSpontEquipeA` tinyint(4) default NULL,
  `NbMaxDsEquipeA` tinyint(4) default NULL,
  `IdRubrique` int(10) unsigned NOT NULL default '0',
  `IdUnite` int(10) unsigned NOT NULL default '0',
  `OrdreActiv` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdActiv`),
  KEY `IdRubrique` (`IdRubrique`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Axe`
-- 

CREATE TABLE `Axe` (
  `IdAxe` int(10) unsigned NOT NULL auto_increment,
  `DescAxe` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`IdAxe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Chat`
-- 

CREATE TABLE `Chat` (
  `IdChat` int(10) unsigned NOT NULL auto_increment,
  `NomChat` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `CouleurChat` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `ModaliteChat` tinyint(3) unsigned NOT NULL default '0',
  `EnregChat` tinyint(3) unsigned NOT NULL default '1',
  `OrdreChat` tinyint(3) unsigned NOT NULL default '0',
  `SalonPriveChat` tinyint(3) unsigned NOT NULL default '1',
  `IdRubrique` int(10) unsigned NOT NULL default '0',
  `IdSousActiv` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdChat`),
  KEY `IdRubrique` (`IdRubrique`),
  KEY `IdSousActiv` (`IdSousActiv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `DossierFormations`
-- 

CREATE TABLE `DossierFormations` (
  `IdDossierForms` int(10) unsigned NOT NULL auto_increment,
  `NomDossierForms` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `PremierDossierForms` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  `OrdreDossierForms` int(10) unsigned NOT NULL default '0',
  `VisibleDossierForms` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdDossierForms`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `DossierFormations_Formation`
-- 

CREATE TABLE `DossierFormations_Formation` (
  `IdDossierForms` int(10) unsigned NOT NULL default '0',
  `IdForm` int(10) unsigned NOT NULL default '0',
  `OrdreForm` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdDossierForms`,`IdForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Equipe`
-- 

CREATE TABLE `Equipe` (
  `IdEquipe` int(10) unsigned NOT NULL auto_increment,
  `NomEquipe` varchar(80) collate utf8_unicode_ci default NULL,
  `IdForm` int(10) unsigned NOT NULL default '0',
  `IdMod` int(10) unsigned NOT NULL default '0',
  `IdRubrique` int(10) unsigned NOT NULL default '0',
  `IdActiv` int(10) unsigned NOT NULL default '0',
  `IdSousActiv` int(10) unsigned NOT NULL default '0',
  `OrdreEquipe` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdEquipe`),
  KEY `IdForm` (`IdForm`),
  KEY `IdMod` (`IdMod`),
  KEY `IdRubrique` (`IdRubrique`),
  KEY `IdActiv` (`IdActiv`),
  KEY `IdSousActiv` (`IdSousActiv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Equipe_Membre`
-- 

CREATE TABLE `Equipe_Membre` (
  `IdEquipe` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  `OrdreEquipeMembre` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdEquipe`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Evenement`
-- 

CREATE TABLE `Evenement` (
  `IdEven` int(10) unsigned NOT NULL auto_increment,
  `IdTypeEven` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned default NULL,
  `MomentEven` datetime default NULL,
  `SortiMomentEven` datetime default NULL,
  `IpEven` varchar(40) collate utf8_unicode_ci default NULL,
  `MachineEven` varchar(200) collate utf8_unicode_ci default NULL,
  `DonneesEven` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`IdEven`),
  KEY `IdTypeEven` (`IdTypeEven`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Evenement_Detail`
-- 

CREATE TABLE `Evenement_Detail` (
  `IdEven` int(10) unsigned NOT NULL default '0',
  `MomentEven` datetime NOT NULL default '0000-00-00 00:00:00',
  `SortiMomentEven` datetime default NULL,
  `IdForm` int(10) unsigned NOT NULL default '0',
  KEY `IdEven` (`IdEven`),
  KEY `IdForm` (`IdForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Formation`
-- 

CREATE TABLE `Formation` (
  `IdForm` int(10) unsigned NOT NULL auto_increment,
  `NomForm` varchar(80) collate utf8_unicode_ci default NULL,
  `DescrForm` text collate utf8_unicode_ci,
  `DateDebForm` datetime default NULL,
  `DateFinForm` datetime default NULL,
  `StatutForm` tinyint(4) default NULL,
  `InscrSpontForm` tinyint(4) default NULL,
  `InscrAutoModules` tinyint(4) default '0',
  `InscrSpontEquipeF` tinyint(4) default NULL,
  `NbMaxDsEquipeF` tinyint(4) default NULL,
  `SuffixeTxt` varchar(8) collate utf8_unicode_ci default NULL,
  `OrdreForm` int(11) unsigned NOT NULL default '0',
  `TypeForm` smallint(5) unsigned NOT NULL default '0',
  `VisiteurAutoriser` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForm`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Formation_Concepteur`
-- 

CREATE TABLE `Formation_Concepteur` (
  `IdForm` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForm`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Formation_Inscrit`
-- 

CREATE TABLE `Formation_Inscrit` (
  `IdForm` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForm`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Formation_Resp`
-- 

CREATE TABLE `Formation_Resp` (
  `IdForm` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForm`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Formation_Tuteur`
-- 

CREATE TABLE `Formation_Tuteur` (
  `IdForm` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForm`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Formulaire`
-- 

CREATE TABLE `Formulaire` (
  `IdForm` int(10) unsigned NOT NULL auto_increment,
  `Nom` varchar(100) collate utf8_unicode_ci default NULL,
  `Commentaire` text collate utf8_unicode_ci,
  `ActiverScores` tinyint(1) unsigned NOT NULL default '0',
  `ScoreBonParDefaut` float NOT NULL default '1',
  `ScoreMauvaisParDefaut` float NOT NULL default '0',
  `ScoreNeutreParDefaut` float NOT NULL default '0',
  `ActiverAxes` tinyint(1) unsigned NOT NULL default '0',
  `Titre` varchar(100) collate utf8_unicode_ci default NULL,
  `Encadrer` tinyint(1) NOT NULL default '0',
  `Largeur` int(10) unsigned NOT NULL default '0',
  `TypeLarg` enum('N','P') collate utf8_unicode_ci NOT NULL default 'P',
  `InterElem` int(10) NOT NULL default '0',
  `InterEnonRep` int(10) NOT NULL default '0',
  `RemplirTout` tinyint(1) unsigned NOT NULL default '0',
  `Statut` tinyint(1) NOT NULL default '0',
  `Type` enum('public','prive') collate utf8_unicode_ci NOT NULL default 'prive',
  `IdPers` int(10) NOT NULL default '0',
  PRIMARY KEY  (`IdForm`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `FormulaireComplete`
-- 

CREATE TABLE `FormulaireComplete` (
  `IdFC` int(10) unsigned NOT NULL auto_increment,
  `TitreFC` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `IdPers` int(10) unsigned NOT NULL default '0',
  `DateFC` datetime NOT NULL default '0000-00-00 00:00:00',
  `IdForm` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdFC`),
  KEY `IdPers` (`IdPers`),
  KEY `IdForm` (`IdForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `FormulaireComplete_Evaluation`
-- 

CREATE TABLE `FormulaireComplete_Evaluation` (
  `IdFCSousActiv` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  `DateEval` datetime default NULL,
  `AppreciationEval` varchar(80) collate utf8_unicode_ci default NULL,
  `CommentaireEval` text collate utf8_unicode_ci,
  PRIMARY KEY  (`IdFCSousActiv`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `FormulaireComplete_SousActiv`
-- 

CREATE TABLE `FormulaireComplete_SousActiv` (
  `IdFCSousActiv` int(10) unsigned NOT NULL auto_increment,
  `IdFC` int(10) unsigned NOT NULL default '0',
  `IdSousActiv` int(10) unsigned NOT NULL default '0',
  `StatutFormSousActiv` tinyint(4) unsigned NOT NULL default '2',
  `IdDest` int(10) unsigned NOT NULL default '0',
  `IdFormSousActivSource` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdFCSousActiv`),
  UNIQUE KEY `IdFC` (`IdFC`,`IdSousActiv`),
  KEY `IdDest` (`IdDest`),
  KEY `IdFormSousActivSource` (`IdFormSousActivSource`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Formulaire_Axe`
-- 

CREATE TABLE `Formulaire_Axe` (
  `IdForm` int(10) unsigned NOT NULL default '0',
  `IdAxe` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForm`,`IdAxe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Forum`
-- 

CREATE TABLE `Forum` (
  `IdForum` int(10) unsigned NOT NULL auto_increment,
  `NomForum` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `DateForum` datetime NOT NULL default '0000-00-00 00:00:00',
  `ModaliteForum` enum('0','2','3','4','5') collate utf8_unicode_ci NOT NULL default '3',
  `StatutForum` smallint(5) unsigned NOT NULL default '0',
  `AccessibleVisiteursForum` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `OrdreForum` tinyint(3) unsigned NOT NULL default '0',
  `IdForumParent` int(10) unsigned NOT NULL default '0',
  `IdMod` int(10) unsigned NOT NULL default '0',
  `IdRubrique` int(10) unsigned NOT NULL default '0',
  `IdSousActiv` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForum`),
  KEY `IdForumParent` (`IdForumParent`),
  KEY `IdMod` (`IdMod`),
  KEY `IdRubrique` (`IdRubrique`),
  KEY `IdSousActiv` (`IdSousActiv`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ForumPrefs`
-- 

CREATE TABLE `ForumPrefs` (
  `IdForumPrefs` int(10) unsigned NOT NULL auto_increment,
  `CopieCourriel` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  `IdForum` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForumPrefs`),
  UNIQUE KEY `IdForum` (`IdForum`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ForumPrefs_CopieCourrielEquipe`
-- 

CREATE TABLE `ForumPrefs_CopieCourrielEquipe` (
  `IdForumPrefs` int(10) unsigned NOT NULL default '0',
  `IdEquipe` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdForumPrefs`,`IdEquipe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Glossaire`
-- 

CREATE TABLE `Glossaire` (
  `IdGlossaire` int(10) unsigned NOT NULL auto_increment,
  `TitreGlossaire` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `TexteGlossaire` text collate utf8_unicode_ci NOT NULL,
  `IdForm` int(11) NOT NULL default '0',
  `IdMod` int(11) NOT NULL default '0',
  `IdRubrique` int(11) NOT NULL default '0',
  `IdActiv` int(11) NOT NULL default '0',
  `IdSousActiv` int(11) NOT NULL default '0',
  PRIMARY KEY  (`IdGlossaire`),
  KEY `IdForm` (`IdForm`),
  KEY `IdMod` (`IdMod`),
  KEY `IdRubrique` (`IdRubrique`),
  KEY `IdActiv` (`IdActiv`),
  KEY `IdSousActiv` (`IdSousActiv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Intitule`
-- 

CREATE TABLE `Intitule` (
  `IdIntitule` int(10) unsigned NOT NULL auto_increment,
  `NomIntitule` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `TypeIntitule` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdIntitule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `MPSeparateur`
-- 

CREATE TABLE `MPSeparateur` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `LargeurMPS` int(10) unsigned default NULL,
  `TypeLargMPS` enum('N','P') collate utf8_unicode_ci default NULL,
  `AlignMPS` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `MPTexte`
-- 

CREATE TABLE `MPTexte` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `TexteMPT` text collate utf8_unicode_ci,
  `AlignMPT` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `MessageForum`
-- 

CREATE TABLE `MessageForum` (
  `IdMessageForum` int(10) unsigned NOT NULL auto_increment,
  `TexteMessageForum` text collate utf8_unicode_ci NOT NULL,
  `DateMessageForum` datetime NOT NULL default '0000-00-00 00:00:00',
  `IdSujetForum` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMessageForum`),
  KEY `IdSujetForum` (`IdSujetForum`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `MessageForum_Equipe`
-- 

CREATE TABLE `MessageForum_Equipe` (
  `IdMessageForum` int(10) unsigned NOT NULL default '0',
  `IdEquipe` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMessageForum`,`IdEquipe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `MessageForum_Ressource`
-- 

CREATE TABLE `MessageForum_Ressource` (
  `IdMessageForum` int(10) unsigned NOT NULL default '0',
  `IdRes` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMessageForum`,`IdRes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Module`
-- 

CREATE TABLE `Module` (
  `IdMod` int(10) unsigned NOT NULL auto_increment,
  `NomMod` varchar(80) collate utf8_unicode_ci default NULL,
  `DescrMod` text collate utf8_unicode_ci,
  `DateDebMod` datetime default NULL,
  `DateFinMod` datetime default NULL,
  `StatutMod` tinyint(4) default NULL,
  `InscrSpontEquipeM` tinyint(4) default NULL,
  `NbMaxDsEquipeM` tinyint(4) default NULL,
  `IdForm` int(10) unsigned NOT NULL default '0',
  `OrdreMod` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  `IdIntitule` int(10) unsigned NOT NULL default '0',
  `NumDepartIntitule` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`IdMod`),
  KEY `IdForm` (`IdForm`),
  KEY `IdPers` (`IdPers`),
  KEY `IdIntitule` (`IdIntitule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Module_Concepteur`
-- 

CREATE TABLE `Module_Concepteur` (
  `IdMod` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMod`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Module_Inscrit`
-- 

CREATE TABLE `Module_Inscrit` (
  `IdMod` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMod`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Module_Rubrique`
-- 

CREATE TABLE `Module_Rubrique` (
  `IdRubrique` int(10) unsigned NOT NULL auto_increment,
  `IdMod` int(10) unsigned NOT NULL default '0',
  `TypeRubrique` tinyint(3) unsigned NOT NULL default '0',
  `DescrRubrique` text collate utf8_unicode_ci NOT NULL,
  `DonneesRubrique` varchar(255) collate utf8_unicode_ci default NULL,
  `OrdreRubrique` tinyint(3) unsigned NOT NULL default '1',
  `NomRubrique` varchar(255) collate utf8_unicode_ci default NULL,
  `StatutRubrique` tinyint(3) unsigned NOT NULL default '0',
  `TypeMenuUnite` tinyint(3) unsigned NOT NULL default '0',
  `NumeroActivUnite` tinyint(3) unsigned NOT NULL default '0',
  `IdIntitule` int(10) unsigned NOT NULL default '0',
  `NumDepartIntitule` tinyint(3) unsigned NOT NULL default '1',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRubrique`),
  KEY `IdMod` (`IdMod`),
  KEY `IdPers` (`IdPers`),
  KEY `IdIntitule` (`IdIntitule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Module_Tuteur`
-- 

CREATE TABLE `Module_Tuteur` (
  `IdMod` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMod`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ObjetFormulaire`
-- 

CREATE TABLE `ObjetFormulaire` (
  `IdObjForm` int(10) unsigned NOT NULL auto_increment,
  `IdTypeObj` int(10) unsigned NOT NULL default '0',
  `IdForm` int(10) unsigned NOT NULL default '0',
  `OrdreObjForm` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdObjForm`),
  KEY `IdTypeObj` (`IdTypeObj`),
  KEY `IdForm` (`IdForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Permission`
-- 

CREATE TABLE `Permission` (
  `IdPermission` int(10) unsigned NOT NULL auto_increment,
  `NomPermis` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `DescrPermis` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`IdPermission`),
  UNIQUE KEY `NomPermis` (`NomPermis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Personne`
-- 

CREATE TABLE `Personne` (
  `IdPers` int(10) unsigned NOT NULL auto_increment,
  `Nom` varchar(30) collate utf8_unicode_ci NOT NULL default 'Sans nom',
  `Prenom` varchar(30) collate utf8_unicode_ci NOT NULL default 'Sans prénom',
  `Pseudo` varchar(30) collate utf8_unicode_ci default NULL,
  `DateNaiss` date default NULL,
  `Sexe` enum('F','M') collate utf8_unicode_ci default NULL,
  `Adresse` varchar(200) collate utf8_unicode_ci default NULL,
  `NumTel` varchar(20) collate utf8_unicode_ci default NULL,
  `Email` varchar(80) collate utf8_unicode_ci default NULL,
  `UrlPerso` varchar(100) collate utf8_unicode_ci default NULL,
  `Mdp` varchar(80) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`IdPers`),
  UNIQUE KEY `Pseudo` (`Pseudo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Projet`
-- 

CREATE TABLE `Projet` (
  `NomProj` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `Email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `NumPortAwareness` varchar(5) collate utf8_unicode_ci NOT NULL default '',
  `NumPortChat` varchar(5) collate utf8_unicode_ci default NULL,
  `UrlAccueil` varchar(100) collate utf8_unicode_ci default NULL,
  `AvertissementLogin` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`NomProj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Projet_Admin`
-- 

CREATE TABLE `Projet_Admin` (
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Projet_Concepteur`
-- 

CREATE TABLE `Projet_Concepteur` (
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Projet_Resp`
-- 

CREATE TABLE `Projet_Resp` (
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `QCocher`
-- 

CREATE TABLE `QCocher` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `EnonQC` text collate utf8_unicode_ci,
  `AlignEnonQC` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `AlignRepQC` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `TxtAvQC` varchar(255) collate utf8_unicode_ci default NULL,
  `TxtApQC` varchar(255) collate utf8_unicode_ci default NULL,
  `DispQC` enum('Hor','Ver') collate utf8_unicode_ci NOT NULL default 'Ver',
  `NbRepMaxQC` tinyint(3) unsigned NOT NULL default '99',
  `MessMaxQC` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `QListeDeroul`
-- 

CREATE TABLE `QListeDeroul` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `EnonQLD` text collate utf8_unicode_ci,
  `AlignEnonQLD` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `AlignRepQLD` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `TxtAvQLD` varchar(255) collate utf8_unicode_ci default NULL,
  `TxtApQLD` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `QNombre`
-- 

CREATE TABLE `QNombre` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `EnonQN` text collate utf8_unicode_ci,
  `AlignEnonQN` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `AlignRepQN` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `TxtAvQN` varchar(255) collate utf8_unicode_ci default NULL,
  `TxtApQN` varchar(255) collate utf8_unicode_ci default NULL,
  `NbMinQN` bigint(20) NOT NULL default '0',
  `NbMaxQN` bigint(20) NOT NULL default '9999999999',
  `MultiQN` float NOT NULL default '1',
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `QRadio`
-- 

CREATE TABLE `QRadio` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `EnonQR` text collate utf8_unicode_ci,
  `AlignEnonQR` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `AlignRepQR` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `TxtAvQR` varchar(255) collate utf8_unicode_ci default NULL,
  `TxtApQR` varchar(255) collate utf8_unicode_ci default NULL,
  `DispQR` enum('Hor','Ver') collate utf8_unicode_ci NOT NULL default 'Ver',
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `QTexteCourt`
-- 

CREATE TABLE `QTexteCourt` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `EnonQTC` text collate utf8_unicode_ci,
  `AlignEnonQTC` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `AlignRepQTC` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `TxtAvQTC` varchar(255) collate utf8_unicode_ci default NULL,
  `TxtApQTC` varchar(255) collate utf8_unicode_ci default NULL,
  `LargeurQTC` tinyint(3) unsigned NOT NULL default '30',
  `MaxCarQTC` tinyint(3) unsigned NOT NULL default '30',
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `QTexteLong`
-- 

CREATE TABLE `QTexteLong` (
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `EnonQTL` text collate utf8_unicode_ci,
  `AlignEnonQTL` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `AlignRepQTL` enum('left','right','center','justify') collate utf8_unicode_ci NOT NULL default 'left',
  `LargeurQTL` tinyint(3) unsigned NOT NULL default '50',
  `HauteurQTL` tinyint(3) unsigned NOT NULL default '10',
  PRIMARY KEY  (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Reponse`
-- 

CREATE TABLE `Reponse` (
  `IdReponse` int(10) unsigned NOT NULL auto_increment,
  `TexteReponse` varchar(255) collate utf8_unicode_ci default NULL,
  `OrdreReponse` tinyint(3) unsigned NOT NULL default '0',
  `FeedbackReponse` text collate utf8_unicode_ci NOT NULL,
  `CorrectionReponse` enum('v','x','-') collate utf8_unicode_ci NOT NULL default '-',
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdReponse`),
  KEY `IdObjForm` (`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ReponseCar`
-- 

CREATE TABLE `ReponseCar` (
  `IdFC` int(10) unsigned NOT NULL default '0',
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `Valeur` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`IdFC`,`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ReponseEntier`
-- 

CREATE TABLE `ReponseEntier` (
  `IdFC` int(10) unsigned NOT NULL default '0',
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `IdReponse` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdFC`,`IdObjForm`,`IdReponse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ReponseFlottant`
-- 

CREATE TABLE `ReponseFlottant` (
  `IdFC` int(10) unsigned NOT NULL default '0',
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `Valeur` float default NULL,
  PRIMARY KEY  (`IdFC`,`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ReponseTexte`
-- 

CREATE TABLE `ReponseTexte` (
  `IdFC` int(10) unsigned NOT NULL default '0',
  `IdObjForm` int(10) unsigned NOT NULL default '0',
  `Valeur` text collate utf8_unicode_ci,
  PRIMARY KEY  (`IdFC`,`IdObjForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Reponse_Axe`
-- 

CREATE TABLE `Reponse_Axe` (
  `IdReponse` int(10) unsigned NOT NULL default '0',
  `IdAxe` int(10) unsigned NOT NULL default '0',
  `Poids` float NOT NULL default '0',
  PRIMARY KEY  (`IdReponse`,`IdAxe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Ressource`
-- 

CREATE TABLE `Ressource` (
  `IdRes` int(10) unsigned NOT NULL auto_increment,
  `NomRes` varchar(80) collate utf8_unicode_ci default NULL,
  `DescrRes` text collate utf8_unicode_ci,
  `DateRes` datetime default NULL,
  `AuteurRes` varchar(80) collate utf8_unicode_ci default NULL,
  `UrlRes` varchar(100) collate utf8_unicode_ci default NULL,
  `IdPers` int(10) unsigned NOT NULL default '0',
  `IdFormat` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRes`),
  KEY `IdPers` (`IdPers`),
  KEY `IdFormat` (`IdFormat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Ressource_SousActiv`
-- 

CREATE TABLE `Ressource_SousActiv` (
  `IdResSousActiv` int(10) unsigned NOT NULL auto_increment,
  `IdSousActiv` int(10) unsigned NOT NULL default '0',
  `IdRes` int(10) unsigned NOT NULL default '0',
  `StatutResSousActiv` tinyint(4) default NULL,
  `IdDest` int(10) unsigned NOT NULL default '0',
  `IdResSousActivSource` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdResSousActiv`),
  UNIQUE KEY `IdSousActiv` (`IdSousActiv`,`IdRes`),
  KEY `IdDest` (`IdDest`),
  KEY `IdResSousActivSource` (`IdResSousActivSource`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Ressource_SousActiv_Evaluation`
-- 

CREATE TABLE `Ressource_SousActiv_Evaluation` (
  `IdResSousActiv` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  `DateEval` datetime default NULL,
  `AppreciationEval` varchar(80) collate utf8_unicode_ci default NULL,
  `CommentaireEval` text collate utf8_unicode_ci,
  PRIMARY KEY  (`IdResSousActiv`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Ressource_SousActiv_FichierEvaluation`
-- 

CREATE TABLE `Ressource_SousActiv_FichierEvaluation` (
  `IdResSousActiv` int(10) NOT NULL default '0',
  `IdRes` int(10) NOT NULL default '0',
  PRIMARY KEY  (`IdResSousActiv`,`IdRes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Ressource_SousActiv_Vote`
-- 

CREATE TABLE `Ressource_SousActiv_Vote` (
  `IdResSousActiv` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  KEY `IdResSousActiv` (`IdResSousActiv`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `SousActiv`
-- 

CREATE TABLE `SousActiv` (
  `IdSousActiv` int(10) unsigned NOT NULL auto_increment,
  `NomSousActiv` varchar(80) collate utf8_unicode_ci default NULL,
  `DonneesSousActiv` varchar(255) collate utf8_unicode_ci default NULL,
  `DescrSousActiv` text collate utf8_unicode_ci,
  `DateDebSousActiv` datetime default NULL,
  `DateFinSousActiv` datetime default NULL,
  `StatutSousActiv` tinyint(4) default NULL,
  `VotesMinSousActiv` tinyint(4) default NULL,
  `IdTypeSousActiv` int(10) unsigned NOT NULL default '0',
  `PremierePageSousActiv` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  `IdActiv` int(10) unsigned NOT NULL default '0',
  `OrdreSousActiv` int(10) unsigned NOT NULL default '0',
  `InfoBulleSousActiv` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `ModaliteSousActiv` tinyint(4) NOT NULL default '1',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSousActiv`),
  KEY `IdTypeSousActiv` (`IdTypeSousActiv`),
  KEY `IdActiv` (`IdActiv`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `SousActivInvisible`
-- 

CREATE TABLE `SousActivInvisible` (
  `IdSousActiv` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSousActiv`,`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `SousActiv_Ressource_SousActiv`
-- 

CREATE TABLE `SousActiv_Ressource_SousActiv` (
  `IdSousActiv` int(10) NOT NULL default '0',
  `IdResSousActiv` int(10) NOT NULL default '0',
  PRIMARY KEY  (`IdSousActiv`,`IdResSousActiv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `SousActiv_SousActiv`
-- 

CREATE TABLE `SousActiv_SousActiv` (
  `IdSousActiv` int(10) NOT NULL default '0',
  `IdSousActivRef` int(10) NOT NULL default '0',
  PRIMARY KEY  (`IdSousActiv`,`IdSousActivRef`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `Statut_Permission`
-- 

CREATE TABLE `Statut_Permission` (
  `IdPermission` int(10) unsigned NOT NULL default '0',
  `IdStatut` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPermission`,`IdStatut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `SujetForum`
-- 

CREATE TABLE `SujetForum` (
  `IdSujetForum` int(10) unsigned NOT NULL auto_increment,
  `TitreSujetForum` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `DateSujetForum` datetime NOT NULL default '0000-00-00 00:00:00',
  `ModaliteSujetForum` enum('0','3','2') collate utf8_unicode_ci NOT NULL default '0',
  `StatutSujetForum` smallint(5) unsigned NOT NULL default '0',
  `AccessibleVisiteursSujetForum` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `IdForum` int(10) unsigned NOT NULL default '0',
  `IdPers` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSujetForum`),
  KEY `IdForum` (`IdForum`),
  KEY `IdPers` (`IdPers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `SujetForum_Equipe`
-- 

CREATE TABLE `SujetForum_Equipe` (
  `IdSujetForum` int(10) unsigned NOT NULL default '0',
  `IdEquipe` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSujetForum`,`IdEquipe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `TypeObjetForm`
-- 

CREATE TABLE `TypeObjetForm` (
  `IdTypeObj` int(10) unsigned NOT NULL auto_increment,
  `NomTypeObj` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `DescTypeObj` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`IdTypeObj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `TypeSousActiv`
-- 

CREATE TABLE `TypeSousActiv` (
  `IdTypeSousActiv` int(10) unsigned NOT NULL auto_increment,
  `NomTypeSousActiv` varchar(80) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`IdTypeSousActiv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `TypeStatutPers`
-- 

CREATE TABLE `TypeStatutPers` (
  `IdStatut` int(10) unsigned NOT NULL auto_increment,
  `NomMasculinStatut` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `NomFemininStatut` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `TxtStatut` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`IdStatut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `i18n`
-- 

CREATE TABLE `i18n` (
  `IdTxt` int(10) unsigned NOT NULL auto_increment,
  `ConstTxt` varchar(32) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`IdTxt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `i18n_fr`
-- 

CREATE TABLE `i18n_fr` (
  `IdTxt` int(10) unsigned NOT NULL default '0',
  `TraductionTxt` text collate utf8_unicode_ci,
  `TooltipTxt` varchar(80) collate utf8_unicode_ci default NULL,
  `DescrTxt` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`IdTxt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

