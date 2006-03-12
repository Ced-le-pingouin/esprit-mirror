# MySQL dump 7.1
#
# Host: localhost    Database: ipfhainaut_dev
#--------------------------------------------------------
# Server version	3.23.58-log

#
# Table structure for table 'Activ'
#
CREATE TABLE Activ (
  IdActiv int(10) unsigned NOT NULL auto_increment,
  NomActiv varchar(80),
  DescrActiv varchar(200),
  DateDebActiv datetime,
  DateFinActiv datetime,
  ModaliteActiv tinyint(4),
  AfficherModaliteActiv tinyint(1) unsigned DEFAULT '0' NOT NULL,
  StatutActiv tinyint(4),
  AfficherStatutActiv tinyint(1) unsigned DEFAULT '0' NOT NULL,
  InscrSpontEquipeA tinyint(4),
  NbMaxDsEquipeA tinyint(4),
  IdRubrique int(10) unsigned DEFAULT '0' NOT NULL,
  IdUnite int(10) unsigned DEFAULT '0' NOT NULL,
  OrdreActiv int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdActiv)
);

#
# Table structure for table 'Axe'
#
CREATE TABLE Axe (
  IdAxe int(10) unsigned NOT NULL auto_increment,
  DescAxe varchar(100) DEFAULT '' NOT NULL,
  PRIMARY KEY (IdAxe)
);

#
# Table structure for table 'Chat'
#
CREATE TABLE Chat (
  IdChat int(10) unsigned NOT NULL auto_increment,
  NomChat varchar(255) DEFAULT '' NOT NULL,
  CouleurChat varchar(255) DEFAULT '' NOT NULL,
  ModaliteChat tinyint(3) unsigned DEFAULT '0' NOT NULL,
  EnregChat tinyint(3) unsigned DEFAULT '1' NOT NULL,
  OrdreChat tinyint(3) unsigned DEFAULT '0' NOT NULL,
  SalonPriveChat tinyint(3) unsigned DEFAULT '1' NOT NULL,
  IdRubrique int(10) unsigned DEFAULT '0' NOT NULL,
  IdSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdChat)
);

#
# Table structure for table 'DossierFormations'
#
CREATE TABLE DossierFormations (
  IdDossierForms int(10) unsigned NOT NULL auto_increment,
  NomDossierForms varchar(255) DEFAULT '' NOT NULL,
  PremierDossierForms enum('0','1') DEFAULT '0' NOT NULL,
  OrdreDossierForms int(10) unsigned DEFAULT '0' NOT NULL,
  VisibleDossierForms enum('0','1') DEFAULT '1' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdDossierForms)
);

#
# Table structure for table 'DossierFormations_Formation'
#
CREATE TABLE DossierFormations_Formation (
  IdDossierForms int(10) unsigned DEFAULT '0' NOT NULL,
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  OrdreForm int(10) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'Equipe'
#
CREATE TABLE Equipe (
  IdEquipe int(10) unsigned NOT NULL auto_increment,
  NomEquipe varchar(80),
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  IdMod int(10) unsigned DEFAULT '0' NOT NULL,
  IdRubrique int(10) unsigned DEFAULT '0' NOT NULL,
  IdActiv int(10) unsigned DEFAULT '0' NOT NULL,
  IdSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  OrdreEquipe int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdEquipe)
);

#
# Table structure for table 'Equipe_Membre'
#
CREATE TABLE Equipe_Membre (
  IdEquipe int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  OrdreEquipeMembre int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdEquipe (IdEquipe,IdPers)
);

#
# Table structure for table 'Evenement'
#
CREATE TABLE Evenement (
  IdEven int(10) unsigned NOT NULL auto_increment,
  IdTypeEven int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned,
  MomentEven datetime,
  SortiMomentEven datetime,
  IpEven varchar(40),
  MachineEven varchar(200),
  DonneesEven varchar(255),
  PRIMARY KEY (IdEven)
);

#
# Table structure for table 'Evenement_Detail'
#
CREATE TABLE Evenement_Detail (
  IdEven int(10) unsigned DEFAULT '0' NOT NULL,
  MomentEven datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  SortiMomentEven datetime,
  IdForm int(10) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'Formation'
#
CREATE TABLE Formation (
  IdForm int(10) unsigned NOT NULL auto_increment,
  NomForm varchar(80),
  DescrForm text,
  DateDebForm datetime,
  DateFinForm datetime,
  StatutForm tinyint(4),
  InscrSpontForm tinyint(4),
  InscrAutoModules tinyint(4) DEFAULT '0',
  InscrSpontEquipeF tinyint(4),
  NbMaxDsEquipeF tinyint(4),
  SuffixeTxt varchar(8),
  OrdreForm int(11) unsigned DEFAULT '0' NOT NULL,
  TypeForm smallint(5) unsigned DEFAULT '0' NOT NULL,
  VisiteurAutoriser enum('0','1') DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdForm)
);

#
# Table structure for table 'Formation_Concepteur'
#
CREATE TABLE Formation_Concepteur (
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdForm (IdForm,IdPers)
);

#
# Table structure for table 'Formation_Inscrit'
#
CREATE TABLE Formation_Inscrit (
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdForm (IdForm,IdPers)
);

#
# Table structure for table 'Formation_Resp'
#
CREATE TABLE Formation_Resp (
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdForm (IdForm,IdPers)
);

#
# Table structure for table 'Formation_Tuteur'
#
CREATE TABLE Formation_Tuteur (
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdForm (IdForm,IdPers)
);

#
# Table structure for table 'Formulaire'
#
CREATE TABLE Formulaire (
  IdForm int(10) unsigned NOT NULL auto_increment,
  Nom varchar(100),
  Commentaire text,
  ActiverScores tinyint(1) unsigned DEFAULT '0' NOT NULL,
  ScoreBonParDefaut float DEFAULT '1' NOT NULL,
  ScoreMauvaisParDefaut float DEFAULT '0' NOT NULL,
  ScoreNeutreParDefaut float DEFAULT '0' NOT NULL,
  ActiverAxes tinyint(1) unsigned DEFAULT '0' NOT NULL,
  Titre varchar(100),
  Encadrer tinyint(1) DEFAULT '0' NOT NULL,
  Largeur int(10) unsigned DEFAULT '0' NOT NULL,
  TypeLarg enum('N','P') DEFAULT 'P' NOT NULL,
  InterElem int(10) DEFAULT '0' NOT NULL,
  InterEnonRep int(10) DEFAULT '0' NOT NULL,
  RemplirTout tinyint(1) unsigned DEFAULT '0' NOT NULL,
  Statut tinyint(1) DEFAULT '0' NOT NULL,
  Type enum('public','prive') DEFAULT 'prive' NOT NULL,
  IdPers int(10) DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdForm)
);

#
# Table structure for table 'FormulaireComplete'
#
CREATE TABLE FormulaireComplete (
  IdFC int(10) unsigned NOT NULL auto_increment,
  TitreFC varchar(255) DEFAULT '' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  DateFC datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdFC)
);

#
# Table structure for table 'FormulaireComplete_Evaluation'
#
CREATE TABLE FormulaireComplete_Evaluation (
  IdFCSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  DateEval datetime,
  AppreciationEval varchar(80),
  CommentaireEval text,
  PRIMARY KEY (IdFCSousActiv,IdPers)
);

#
# Table structure for table 'FormulaireComplete_SousActiv'
#
CREATE TABLE FormulaireComplete_SousActiv (
  IdFCSousActiv int(10) unsigned NOT NULL auto_increment,
  IdFC int(10) unsigned DEFAULT '0' NOT NULL,
  IdSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  StatutFormSousActiv tinyint(4) unsigned DEFAULT '2' NOT NULL,
  IdDest int(10) unsigned DEFAULT '0' NOT NULL,
  IdFormSousActivSource int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdFCSousActiv)
);

#
# Table structure for table 'Formulaire_Axe'
#
CREATE TABLE Formulaire_Axe (
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  IdAxe int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdForm,IdAxe)
);

#
# Table structure for table 'Forum'
#
CREATE TABLE Forum (
  IdForum int(10) unsigned NOT NULL auto_increment,
  NomForum varchar(255) DEFAULT '' NOT NULL,
  DateForum datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ModaliteForum enum('0','2','3','4','5') DEFAULT '3' NOT NULL,
  StatutForum smallint(5) unsigned DEFAULT '0' NOT NULL,
  AccessibleVisiteursForum enum('0','1') DEFAULT '1' NOT NULL,
  OrdreForum tinyint(3) unsigned DEFAULT '0' NOT NULL,
  IdForumParent int(10) unsigned DEFAULT '0' NOT NULL,
  IdMod int(10) unsigned DEFAULT '0' NOT NULL,
  IdRubrique int(10) unsigned DEFAULT '0' NOT NULL,
  IdSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdForum)
);

#
# Table structure for table 'ForumPrefs'
#
CREATE TABLE ForumPrefs (
  IdForumPrefs int(10) unsigned NOT NULL auto_increment,
  CopieCourriel enum('0','1') DEFAULT '0' NOT NULL,
  IdForum int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdForumPrefs)
);

#
# Table structure for table 'ForumPrefs_CopieCourrielEquipe'
#
CREATE TABLE ForumPrefs_CopieCourrielEquipe (
  IdForumPrefs int(10) unsigned DEFAULT '0' NOT NULL,
  IdEquipe int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdSujetForum (IdForumPrefs,IdEquipe)
);

#
# Table structure for table 'Glossaire'
#
CREATE TABLE Glossaire (
  IdGlossaire int(10) unsigned NOT NULL auto_increment,
  TitreGlossaire varchar(100) DEFAULT '' NOT NULL,
  TexteGlossaire text DEFAULT '' NOT NULL,
  IdForm int(11) DEFAULT '0' NOT NULL,
  IdMod int(11) DEFAULT '0' NOT NULL,
  IdRubrique int(11) DEFAULT '0' NOT NULL,
  IdActiv int(11) DEFAULT '0' NOT NULL,
  IdSousActiv int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdGlossaire)
);

#
# Table structure for table 'Intitule'
#
CREATE TABLE Intitule (
  IdIntitule int(10) unsigned NOT NULL auto_increment,
  NomIntitule varchar(255) DEFAULT '' NOT NULL,
  TypeIntitule tinyint(3) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdIntitule)
);

#
# Table structure for table 'MPSeparateur'
#
CREATE TABLE MPSeparateur (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  LargeurMPS int(10) unsigned,
  TypeLargMPS enum('N','P'),
  AlignMPS enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'MPTexte'
#
CREATE TABLE MPTexte (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  TexteMPT text,
  AlignMPT enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'MessageForum'
#
CREATE TABLE MessageForum (
  IdMessageForum int(10) unsigned NOT NULL auto_increment,
  TexteMessageForum text DEFAULT '' NOT NULL,
  DateMessageForum datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  IdSujetForum int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdMessageForum)
);

#
# Table structure for table 'MessageForum_Equipe'
#
CREATE TABLE MessageForum_Equipe (
  IdMessageForum int(10) unsigned DEFAULT '0' NOT NULL,
  IdEquipe int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdMessageForum (IdMessageForum,IdEquipe)
);

#
# Table structure for table 'MessageForum_Ressource'
#
CREATE TABLE MessageForum_Ressource (
  IdMessageForum int(10) unsigned DEFAULT '0' NOT NULL,
  IdRes int(10) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'Module'
#
CREATE TABLE Module (
  IdMod int(10) unsigned NOT NULL auto_increment,
  NomMod varchar(80),
  DescrMod text,
  DateDebMod datetime,
  DateFinMod datetime,
  StatutMod tinyint(4),
  InscrSpontEquipeM tinyint(4),
  NbMaxDsEquipeM tinyint(4),
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  OrdreMod int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  IdIntitule int(10) unsigned DEFAULT '0' NOT NULL,
  NumDepartIntitule tinyint(3) unsigned DEFAULT '1' NOT NULL,
  PRIMARY KEY (IdMod)
);

#
# Table structure for table 'Module_Concepteur'
#
CREATE TABLE Module_Concepteur (
  IdMod int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE Concepteur (IdMod,IdPers)
);

#
# Table structure for table 'Module_Inscrit'
#
CREATE TABLE Module_Inscrit (
  IdMod int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE Inscrit (IdMod,IdPers)
);

#
# Table structure for table 'Module_Rubrique'
#
CREATE TABLE Module_Rubrique (
  IdRubrique int(10) unsigned NOT NULL auto_increment,
  IdMod int(10) unsigned DEFAULT '0' NOT NULL,
  TypeRubrique tinyint(3) unsigned DEFAULT '0' NOT NULL,
  DescrRubrique text DEFAULT '' NOT NULL,
  DonneesRubrique varchar(255),
  OrdreRubrique tinyint(3) unsigned DEFAULT '1' NOT NULL,
  NomRubrique varchar(255),
  StatutRubrique tinyint(3) unsigned DEFAULT '0' NOT NULL,
  TypeMenuUnite tinyint(3) unsigned DEFAULT '0' NOT NULL,
  NumeroActivUnite tinyint(3) unsigned DEFAULT '0' NOT NULL,
  IdIntitule int(10) unsigned DEFAULT '0' NOT NULL,
  NumDepartIntitule tinyint(3) unsigned DEFAULT '1' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdRubrique)
);

#
# Table structure for table 'Module_Tuteur'
#
CREATE TABLE Module_Tuteur (
  IdMod int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdMod (IdMod,IdPers)
);

#
# Table structure for table 'ObjetFormulaire'
#
CREATE TABLE ObjetFormulaire (
  IdObjForm int(10) unsigned NOT NULL auto_increment,
  IdTypeObj int(10) unsigned DEFAULT '0' NOT NULL,
  IdForm int(10) unsigned DEFAULT '0' NOT NULL,
  OrdreObjForm tinyint(3) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'Permission'
#
CREATE TABLE Permission (
  IdPermission int(10) unsigned NOT NULL auto_increment,
  NomPermis varchar(60) DEFAULT '' NOT NULL,
  DescrPermis varchar(255) DEFAULT '' NOT NULL,
  PRIMARY KEY (IdPermission),
  UNIQUE NomPermis (NomPermis)
);

#
# Table structure for table 'Personne'
#
CREATE TABLE Personne (
  IdPers int(10) unsigned NOT NULL auto_increment,
  Nom varchar(30) DEFAULT 'Sans nom' NOT NULL,
  Prenom varchar(30) DEFAULT 'Sans prénom' NOT NULL,
  Pseudo varchar(30),
  DateNaiss date,
  Sexe enum('F','M'),
  Adresse varchar(200),
  NumTel varchar(20),
  Email varchar(80),
  UrlPerso varchar(100),
  Mdp varchar(80),
  PRIMARY KEY (IdPers),
  UNIQUE Pseudo (Pseudo)
);

#
# Table structure for table 'Projet'
#
CREATE TABLE Projet (
  NomProj varchar(80),
  Email varchar(255) DEFAULT '' NOT NULL,
  NumPortAwareness varchar(5) DEFAULT '' NOT NULL,
  NumPortChat varchar(5),
  UrlAccueil varchar(100),
  AvertissementLogin text DEFAULT '' NOT NULL
);

#
# Table structure for table 'Projet_Admin'
#
CREATE TABLE Projet_Admin (
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdPers)
);

#
# Table structure for table 'Projet_Concepteur'
#
CREATE TABLE Projet_Concepteur (
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdPers)
);

#
# Table structure for table 'Projet_Resp'
#
CREATE TABLE Projet_Resp (
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdPers)
);

#
# Table structure for table 'QCocher'
#
CREATE TABLE QCocher (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  EnonQC text,
  AlignEnonQC enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  AlignRepQC enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  TxtAvQC varchar(255),
  TxtApQC varchar(255),
  DispQC enum('Hor','Ver') DEFAULT 'Ver' NOT NULL,
  NbRepMaxQC tinyint(3) unsigned DEFAULT '99' NOT NULL,
  MessMaxQC varchar(255),
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'QListeDeroul'
#
CREATE TABLE QListeDeroul (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  EnonQLD text,
  AlignEnonQLD enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  AlignRepQLD enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  TxtAvQLD varchar(255),
  TxtApQLD varchar(255),
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'QNombre'
#
CREATE TABLE QNombre (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  EnonQN text,
  AlignEnonQN enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  AlignRepQN enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  TxtAvQN varchar(255),
  TxtApQN varchar(255),
  NbMinQN bigint(20) DEFAULT '0' NOT NULL,
  NbMaxQN bigint(20) DEFAULT '9999999999' NOT NULL,
  MultiQN float DEFAULT '1' NOT NULL,
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'QRadio'
#
CREATE TABLE QRadio (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  EnonQR text,
  AlignEnonQR enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  AlignRepQR enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  TxtAvQR varchar(255),
  TxtApQR varchar(255),
  DispQR enum('Hor','Ver') DEFAULT 'Ver' NOT NULL,
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'QTexteCourt'
#
CREATE TABLE QTexteCourt (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  EnonQTC text,
  AlignEnonQTC enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  AlignRepQTC enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  TxtAvQTC varchar(255),
  TxtApQTC varchar(255),
  LargeurQTC tinyint(3) unsigned DEFAULT '30' NOT NULL,
  MaxCarQTC tinyint(3) unsigned DEFAULT '30' NOT NULL,
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'QTexteLong'
#
CREATE TABLE QTexteLong (
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  EnonQTL text,
  AlignEnonQTL enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  AlignRepQTL enum('left','right','center','justify') DEFAULT 'left' NOT NULL,
  LargeurQTL tinyint(3) unsigned DEFAULT '50' NOT NULL,
  HauteurQTL tinyint(3) unsigned DEFAULT '10' NOT NULL,
  PRIMARY KEY (IdObjForm)
);

#
# Table structure for table 'Reponse'
#
CREATE TABLE Reponse (
  IdReponse int(10) unsigned NOT NULL auto_increment,
  TexteReponse varchar(255),
  OrdreReponse tinyint(3) unsigned DEFAULT '0' NOT NULL,
  FeedbackReponse text DEFAULT '' NOT NULL,
  CorrectionReponse enum('v','x','-') DEFAULT '-' NOT NULL,
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdReponse)
);

#
# Table structure for table 'ReponseCar'
#
CREATE TABLE ReponseCar (
  IdFC int(10) unsigned DEFAULT '0' NOT NULL,
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  Valeur varchar(255),
  PRIMARY KEY (IdFC,IdObjForm)
);

#
# Table structure for table 'ReponseEntier'
#
CREATE TABLE ReponseEntier (
  IdFC int(10) unsigned DEFAULT '0' NOT NULL,
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  IdReponse int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdFC,IdObjForm,IdReponse)
);

#
# Table structure for table 'ReponseFlottant'
#
CREATE TABLE ReponseFlottant (
  IdFC int(10) unsigned DEFAULT '0' NOT NULL,
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  Valeur float,
  PRIMARY KEY (IdFC,IdObjForm)
);

#
# Table structure for table 'ReponseTexte'
#
CREATE TABLE ReponseTexte (
  IdFC int(10) unsigned DEFAULT '0' NOT NULL,
  IdObjForm int(10) unsigned DEFAULT '0' NOT NULL,
  Valeur text,
  PRIMARY KEY (IdFC,IdObjForm)
);

#
# Table structure for table 'Reponse_Axe'
#
CREATE TABLE Reponse_Axe (
  IdReponse int(10) unsigned DEFAULT '0' NOT NULL,
  IdAxe int(10) unsigned DEFAULT '0' NOT NULL,
  Poids float DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdReponse,IdAxe)
);

#
# Table structure for table 'Ressource'
#
CREATE TABLE Ressource (
  IdRes int(10) unsigned NOT NULL auto_increment,
  NomRes varchar(80),
  DescrRes text,
  DateRes datetime,
  AuteurRes varchar(80),
  UrlRes varchar(100),
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  IdFormat int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdRes)
);

#
# Table structure for table 'Ressource_SousActiv'
#
CREATE TABLE Ressource_SousActiv (
  IdResSousActiv int(10) unsigned NOT NULL auto_increment,
  IdSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  IdRes int(10) unsigned DEFAULT '0' NOT NULL,
  StatutResSousActiv tinyint(4),
  IdDest int(10) unsigned DEFAULT '0' NOT NULL,
  IdResSousActivSource int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdResSousActiv)
);

#
# Table structure for table 'Ressource_SousActiv_Evaluation'
#
CREATE TABLE Ressource_SousActiv_Evaluation (
  IdResSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  DateEval datetime,
  AppreciationEval varchar(80),
  CommentaireEval text,
  PRIMARY KEY (IdResSousActiv,IdPers)
);

#
# Table structure for table 'Ressource_SousActiv_FichierEvaluation'
#
CREATE TABLE Ressource_SousActiv_FichierEvaluation (
  IdResSousActiv int(10) DEFAULT '0' NOT NULL,
  IdRes int(10) DEFAULT '0' NOT NULL,
  UNIQUE IdResSousActiv (IdResSousActiv)
);

#
# Table structure for table 'Ressource_SousActiv_Vote'
#
CREATE TABLE Ressource_SousActiv_Vote (
  IdResSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  KEY Idx (IdResSousActiv,IdPers)
);

#
# Table structure for table 'SousActiv'
#
CREATE TABLE SousActiv (
  IdSousActiv int(10) unsigned NOT NULL auto_increment,
  NomSousActiv varchar(80),
  DonneesSousActiv varchar(255),
  DescrSousActiv text,
  DateDebSousActiv datetime,
  DateFinSousActiv datetime,
  StatutSousActiv tinyint(4),
  VotesMinSousActiv tinyint(4),
  IdTypeSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  PremierePageSousActiv enum('0','1') DEFAULT '0' NOT NULL,
  IdActiv int(10) unsigned DEFAULT '0' NOT NULL,
  OrdreSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  InfoBulleSousActiv varchar(128) DEFAULT '' NOT NULL,
  ModaliteSousActiv tinyint(4) DEFAULT '1' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdSousActiv)
);

#
# Table structure for table 'SousActivInvisible'
#
CREATE TABLE SousActivInvisible (
  IdSousActiv int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'SousActiv_Ressource_SousActiv'
#
CREATE TABLE SousActiv_Ressource_SousActiv (
  IdSousActiv int(10) DEFAULT '0' NOT NULL,
  IdResSousActiv int(10) DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdSousActiv,IdResSousActiv)
);

#
# Table structure for table 'SousActiv_SousActiv'
#
CREATE TABLE SousActiv_SousActiv (
  IdSousActiv int(10) DEFAULT '0' NOT NULL,
  IdSousActivRef int(10) DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdSousActiv,IdSousActivRef)
);

#
# Table structure for table 'Statut_Permission'
#
CREATE TABLE Statut_Permission (
  IdPermission int(10) unsigned DEFAULT '0' NOT NULL,
  IdStatut int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdPermission,IdStatut)
);

#
# Table structure for table 'SujetForum'
#
CREATE TABLE SujetForum (
  IdSujetForum int(10) unsigned NOT NULL auto_increment,
  TitreSujetForum varchar(255) DEFAULT '' NOT NULL,
  DateSujetForum datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ModaliteSujetForum enum('0','3','2') DEFAULT '0' NOT NULL,
  StatutSujetForum smallint(5) unsigned DEFAULT '0' NOT NULL,
  AccessibleVisiteursSujetForum enum('0','1') DEFAULT '1' NOT NULL,
  IdForum int(10) unsigned DEFAULT '0' NOT NULL,
  IdPers int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdSujetForum)
);

#
# Table structure for table 'SujetForum_Equipe'
#
CREATE TABLE SujetForum_Equipe (
  IdSujetForum int(10) unsigned DEFAULT '0' NOT NULL,
  IdEquipe int(10) unsigned DEFAULT '0' NOT NULL,
  UNIQUE IdSujetForum (IdSujetForum,IdEquipe)
);

#
# Table structure for table 'TypeObjetForm'
#
CREATE TABLE TypeObjetForm (
  IdTypeObj int(10) unsigned NOT NULL auto_increment,
  NomTypeObj varchar(25) DEFAULT '' NOT NULL,
  DescTypeObj varchar(100) DEFAULT '' NOT NULL,
  DescCourteTypeObj varchar(50) DEFAULT '' NOT NULL,
  PRIMARY KEY (IdTypeObj)
);

#
# Table structure for table 'TypeSousActiv'
#
CREATE TABLE TypeSousActiv (
  IdTypeSousActiv int(10) unsigned NOT NULL auto_increment,
  NomTypeSousActiv varchar(80),
  PRIMARY KEY (IdTypeSousActiv)
);

#
# Table structure for table 'TypeStatutPers'
#
CREATE TABLE TypeStatutPers (
  IdStatut int(10) unsigned NOT NULL auto_increment,
  NomMasculinStatut varchar(60) DEFAULT '' NOT NULL,
  NomFemininStatut varchar(60) DEFAULT '' NOT NULL,
  TxtStatut varchar(60) DEFAULT '' NOT NULL,
  PRIMARY KEY (IdStatut)
);

#
# Table structure for table 'i18n'
#
CREATE TABLE i18n (
  IdTxt int(10) unsigned NOT NULL auto_increment,
  ConstTxt varchar(32),
  PRIMARY KEY (IdTxt)
);

#
# Table structure for table 'i18n_fr'
#
CREATE TABLE i18n_fr (
  IdTxt int(10) unsigned DEFAULT '0' NOT NULL,
  TraductionTxt text,
  TooltipTxt varchar(80),
  DescrTxt text DEFAULT '' NOT NULL,
  PRIMARY KEY (IdTxt)
);

