<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/**
 * @file	plate_forme.class.php
 * 
 * Contient la classe principale de la plate-forme, ainsi qu'une classe pour le moment inutilisée pour les "traductions"
 * 
 * @date	2001/09/06
 * 
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 * @author	Jérôme TOUZE
 * @author	Ludovic FLAMME
 */


/** @name Constantes - état d'identification utilisateur */
//@{
define("LOGIN_OK"				, 0);	/// Le login s'est déroulé sans problème												@enum LOGIN_OK
define("LOGIN_MDP_INCORRECT"	, 8);	/// Le mot de passe entré est invalide par rapport au pseudo							@enum LOGIN_MDP_INCORRECT
define("LOGIN_PAS_ENCORE_ID"	, 9);	/// L'utilisateur ne s'est pas encore identifié											@enum LOGIN_PAS_ENCORE_ID
define("LOGIN_PERSONNE_INCONNUE", 10);	/// L'utilisateur est inconnu (mauvais pseudo?)											@enum LOGIN_PERSONNE_INCONNUE
//@}

/** @name Constantes - éléments de la session enregistrée dans le cookie */
//@{
define("SESSION_DEBUT"				, 0);	/// Représente toujours la 1ère constante de session (0); utilisée dans les boucles	@enum SESSION_DEBUT
define("SESSION_PSEUDO"				, 0);	/// Pseudo de la personne															@enum SESSION_PSEUDO
define("SESSION_NOM"				, 1);	/// Nom de la personne																@enum SESSION_NOM
define("SESSION_PRENOM"				, 2);	/// Prénom de la personne															@enum SESSION_PRENOM
define("SESSION_MDP"				, 3);	/// Mot de passe de la personne														@enum SESSION_MDP
define("SESSION_STATUT_ABSOLU"		, 4);	/// Statut de l'utilisateur le plus important										@enum SESSION_STATUT_ABSOLU
define("SESSION_STATUT_UTILISATEUR"	, 5);	/// Statut que l'utilisateur a choisi												@enum SESSION_STATUT_UTILISATEUR
define("SESSION_FORM"				, 6);	/// Numéro de la formation courante													@enum SESSION_FORM
define("SESSION_MOD"				, 7);	/// Numéro du module/cours courant													@enum SESSION_MOD
define("SESSION_UNITE"				, 8);	/// Numéro de l'unité courante (plus utilisé)										@enum SESSION_UNITE
define("SESSION_ACTIV"				, 9);	/// Numéro de l'activité															@enum SESSION_ACTIV
define("SESSION_SOUSACTIV"			, 10);	/// Numéro de la sous-activité														@enum SESSION_SOUSACTIV
define("SESSION_TRI_COLONNE"		, 11);	/// Pour les écrans où l'on peut trier des tableaux, la colonne de tri principale	@enum SESSION_TRI_COLONNE
define("SESSION_TRI_DIRECTION"		, 12);	/// Toujours pour les mêmes écrans, tri croissant ou décroissant ?					@enum SESSION_TRI_DIRECTION
define("SESSION_UID"				, 13);	/// Numéro ID unique donné par la table 'Evenement'									@enum SESSION_UID
define("SESSION_DOSSIER_FORMS"		, 14);	/// Numéro du dossier de formations													@enum SESSION_DOSSIER_FORMS
define("SESSION_LANG"				, 15);	/// Langue de l'interface de l'utilisateur											@enum SESSION_LANG
define("SESSION_FIN"				, 14);	/// Devrait toujours être identique à la dernière constante de session; utilisée dans les boucles	@enum SESSION_FIN
//@}

/** @name Constantes - types d'événements (à logger) */
//@{
define("TYPE_EVEN_LOGIN_RATE"	, 1);	/// La tentative de login a echoué					@enum TYPE_EVEN_LOGIN_RATE
define("TYPE_EVEN_LOGIN_REUSSI"	, 2);	/// La tentative de login a réussi					@enum TYPE_EVEN_LOGIN_REUSSI
define("TYPE_EVEN_DECONNEXION"	, 3);	/// L'utilisateur s'est explicitement déconnecté	@enum TYPE_EVEN_DECONNEXION
//@}

/** @name Constantes - types de "liens", en fait les types de sous-activités possibles (dans la colonne de gauche d'une rubrique) */
//@{
define("LIEN_PAGE_HTML"				, 1);	/// Simple page HTML à afficher																		@enum LIEN_PAGE_HTML
define("LIEN_DOCUMENT_TELECHARGER"	, 2);	/// Lien vers un document à télécharger																@enum LIEN_DOCUMENT_TELECHARGER
define("LIEN_SITE_INTERNET"			, 3);	/// Lien externe vers un site web																	@enum LIEN_SITE_INTERNET
define("LIEN_CHAT"					, 4);	/// Salon de discussion / Chat																		@enum LIEN_CHAT
define("LIEN_FORUM"					, 5);	/// Forum																							@enum LIEN_FORUM
define("LIEN_GALERIE"				, 6);	/// Galerie, servant à mettre en avant des travaux sélectionnés d'un collecticiel précédent			@enum LIEN_GALERIE
define("LIEN_COLLECTICIEL"			, 7);	/// Collecticiel																					@enum LIEN_COLLECTICIEL
define("LIEN_UNITE"					, 8);	/// Unité d'apprentissage																			@enum LIEN_UNITE	@todo mieux expliquer différence Rubrique/Unité
define("LIEN_FORMULAIRE"			, 9);	/// Questionnaire = AEL (activité en ligne)															@enum LIEN_FORMULAIRE
define("LIEN_TEXTE_FORMATTE"		, 10);	/// Texte avec possibilité de mise en forme	réduite par balises										@enum LIEN_TEXTE_FORMATTE
define("LIEN_GLOSSAIRE"				, 11);	/// Glossaire																						@enum LIEN_GLOSSAIRE
define("LIEN_TABLEAU_DE_BORD"		, 12);	/// Tableau de bord, aperçu de l'avancée des travaux d'étudiants dans les sous-activités			@enum LIEN_TABLEAU_DE_BORD
//@}

/** @name Constantes - modalités d'affichage pour certains liens HTML de la plate-forme */
//@{
define("FRAME_CENTRALE_DIRECT"		, 1);	/// Affichage immédiat dans la frame centrale														@enum FRAME_CENTRALE_DIRECT
define("FRAME_CENTRALE_INDIRECT"	, 2);	/// Affichage d'une consigne préalable, contenant le lien, qui s'ouvrira dans la frame centrale		@enum FRAME_CENTRALE_INDIRECT
define("NOUVELLE_FENETRE_DIRECT"	, 3);	/// Affichage immédiat dans une nouvelle fenêtre de navigateur										@enum NOUVELLE_FENETRE_DIRECT
define("NOUVELLE_FENETRE_INDIRECT"	, 4);	/// Affichage d'une consigne préalable, contenant le lien, qui s'ouvrira dans une nouvelle fenêtre	@enum NOUVELLE_FENETRE_INDIRECT
define("MODE_LIEN_TELECHARGER"		, 5);	/// Force le téléchargement de la cible du lien														@enum MODE_LIEN_TELECHARGER
//@}

/** @name Constantes - éléments de "structure" de formation */
//@{
define("TYPE_INCONNU"		, 0);	/// Type inconnu		@enum TYPE_INCONNU
define("TYPE_FORMATION"		, 1);	/// Formation/Session	@enum TYPE_FORMATION
define("TYPE_MODULE"		, 2);	/// Module/Cours		@enum TYPE_MODULE
define("TYPE_RUBRIQUE"		, 3);	/// Rubrique			@enum TYPE_RUBRIQUE
define("TYPE_UNITE"			, 4);	/// Unité				@enum TYPE_UNITE
define("TYPE_ACTIVITE"		, 5);	/// Activité			@enum TYPE_ACTIVITE
define("TYPE_SOUS_ACTIVITE"	, 6);	/// Sous-activité		@enum TYPE_SOUS_ACTIVITE
//@}

/** @name Constantes - statuts/disponibilité des éléments de structure */
//@{
define("STATUT_FERME"			, 1);	/// Le lien est visible mais pas accessible															@enum STATUT_FERME
define("STATUT_OUVERT"			, 2);	/// Le lien est visible et accessible																@enum STATUT_OUVERT
define("STATUT_INVISIBLE"		, 3);	/// Le lien n'est pas affiché																		@enum STATUT_INVISIBLE
define("STATUT_ARCHIVE"			, 4);	/// Pas certain que cette constante est utilisée actuellement										@enum STATUT_ARCHIVE	@todo Confirmer cette description
define("STATUT_EFFACE"			, 5);	/// L'élément est effacé logiquement, un admin pourra le récupérer dans l'outil Corbeille			@enum STATUT_EFFACE
define("STATUT_IDEM_PARENT"		, 6);	/// Reprend le statut ouvert/fermé/etc de la structure parente										@enum STATUT_IDEM_PARENT
define("STATUT_LECTURE_SEULE"	, 7);	/// Le lien est visible, cliquable mais l'utilisateur ne pourra plus rien modifier					@enum STATUT_LECTURE_SEULE

//define("STATUT_USER",3);
//@}

/** @name Constantes - modalités individuelles ou par équipes pour certaines sous-activités */
//@{
define("MODALITE_IDEM_PARENT"				,0);	/// Reprend la modalité de l'élément parent																	@enum MODALITE_IDEM_PARENT
define("MODALITE_INDIVIDUEL"				,1);	/// Activité individuelle																					@enum MODALITE_INDIVIDUEL
define("MODALITE_PAR_EQUIPE"				,2);	/// Isolée			==> Les équipes ne voient pas les autres équipes										@enum MODALITE_PAR_EQUIPE
define("MODALITE_POUR_TOUS"					,3);	/// Tout le monde participe et voit la participation des autres, mais à titre individuel					@enum MODALITE_POUR_TOUS	@todo Confirmer cette description
define("MODALITE_PAR_EQUIPE_INTERCONNECTEE"	,4);	/// Interconnectée	==> Les équipes voient les autres équipes mais ne peuvent pas collaborer entre elles	@enum MODALITE_PAR_EQUIPE_INTERCONNECTEE	
define("MODALITE_PAR_EQUIPE_COLLABORANTE"	,5);	/// Collaborante	==> Les équipes voient les autres équipes et peuvent collaborer							@enum MODALITE_PAR_EQUIPE_COLLABORANTE
//@}

/** @name Constantes - types d'éléments dans les formulaires */
//@{
define("OBJFORM_QTEXTELONG"		, 1);	/// Boîte de texte multi-lignes										@enum OBJFORM_QTEXTELONG
define("OBJFORM_QTEXTECOURT"	, 2);	/// Boîte de texte mono-ligne										@enum OBJFORM_QTEXTECOURT
define("OBJFORM_QNOMBRE"		, 3);	/// Boîte de texte où seuls les nombres sont autorisés				@enum OBJFORM_QNOMBRE
define("OBJFORM_QLISTEDEROUL"	, 4);	/// Liste déroulante à choix unique									@enum OBJFORM_QLISTEDEROUL
define("OBJFORM_QRADIO"			, 5);	/// Ensemble de boutons radio à choix unique						@enum OBJFORM_QRADIO
define("OBJFORM_QCOCHER"		, 6);	/// Ensemble de cases à cocher à choix multiples					@enum OBJFORM_QCOCHER
define("OBJFORM_MPTEXTE"		, 7);	/// Texte; élément de mise en page pure, pas de réponse à donner	@enum OBJFORM_MPTEXTE
define("OBJFORM_MPSEPARATEUR"	, 8);	/// Ligne de séparation; élément de mise en page pure				@enum OBJFORM_MPSEPARATEUR
//@}

/** @name Constantes - modalités de soumission d'un formulaire au tuteur */
//@{
define("SOUMISSION_MANUELLE"	, 0);	/// L'étudiant devra encore soumettre le document au tuteur quand il aura rempli le formulaire	@enum SOUMISSION_MANUELLE
define("SOUMISSION_AUTOMATIQUE"	, 1);	/// Le formulaire est automatiquement soumis au tuteur dès que l'étudiant l'a complété			@enum SOUMISSION_AUTOMATIQUE
//@}

/** @name Constantes - tri à effectuer lorsque des affichages en colonnes sont présents */
//@{
define("PAS_TRI"		, 0);	/// Aucun tri ne doit avoir lieu	@enum PAS_TRI
define("TRI_CROISSANT"	, 1);	/// Tri par ordre croissant			@enum TRI_CROISSANT
define("TRI_DECROISSANT", 2);	/// Tri par ordre décroissant		@enum TRI_DECROISSANT
//@}

// ---------------------
// Déclaration des fichiers à inclure
// ---------------------
require_once(dir_code_lib("bdd_mysql.class.php"));		// Gérer la base de données

$sDirInclude = dir_include();
require_once("{$sDirInclude}theme.global.php");			// Thème de la plate-forme
require_once("{$sDirInclude}config.inc");				// Informations à propos de la base de données
unset($sDirInclude);

//require_once(dir_definition("i18n.def.php"));			// Internationalisation

$sDirDatabase = dir_database();
require_once("{$sDirDatabase}personne.tbl.php");
require_once("{$sDirDatabase}formation.tbl.php");
require_once("{$sDirDatabase}ressource.tbl.php");
require_once("{$sDirDatabase}forum.tbl.php");
require_once("{$sDirDatabase}intitule.tbl.php");
require_once("{$sDirDatabase}permission.tbl.php");
require_once("{$sDirDatabase}statut_permission.tbl.php");
require_once("{$sDirDatabase}statut_utilisateur.class.php");
unset($sDirDatabase);


/**
 * Classe permettant de récupérer des constantes 'texte' (consignes, messages, etc) dans la base de données
 */
class CConstantes
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $sTableI18N;		///< Nom de la table DB (i18n) qui contient toutes les constantes des textes traduisibles
	var $sTable;			///< Nom de la table DB (i18n_fr) qui contient toutes les traductions dans une langue donnée
	
	/**
	 * Constructeur. Initialise les paramètres de DB et de tables de l'objet
	 * 
	 * @param	v_oBdd		l'objet CBdd qui représente la connexion à la DB
	 * @param	v_sTable	le nom de la table correspondant à la langue voulue. C'est dans cette table que seront 
	 * 						récupérées les traductions
	 */
	function CConstantes(&$v_oBdd, $v_sTable)
	{
		$this->oBdd			= &$v_oBdd;
		$this->sTable		= $v_sTable;
		$this->sTableI18N	= "i18n";
	}
	
	/**
	 * Récupère un texte traduit sur base de son id
	 * 
	 * @param	v_iId				l'id du texte à chercher
	 * @param	v_bConversionHtml	si \c true, remplace les caractères qui le nécessitent par des entités HTML dans le 
	 * 								texte retourné
	 * 
	 * @return	le texte associé à l'Id, dans la langue voulue (définie dans le constructeur par un nom de table 
	 * 			correspondant dans la DB)
	 */
	function retTexte($v_iId, $v_bConversionHtml = TRUE)
	{
		if (is_numeric($v_iId))
		{
			$sRequeteSql = "SELECT * FROM {$this->sTable}"
				." WHERE IdTxt='{$v_iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($this->oBdd->retNbEnregsDsResult($hResult))
				$r_Enreg = $this->oBdd->retEnregSuiv($hResult);
			
			$this->oBdd->libererResult($hResult);
			
			if ($v_bConversionHtml)
				return mb_convert_encoding($r_Enreg->ContenuTxt,"HTML-ENTITIES","UTF-8");
			else
				return $r_Enreg->ContenuTxt;
		}
		else
			return "[$v_iId]";
	}
	
	/**
	 * Crée un fichier de constantes représentant les termes traduisibles
	 * 
	 * Cette fonction doit être appelée lorsque de nouveaux termes à traduire ont été ajoutés dans la DB, sinon leur id 
	 * sera inaccessible en PHP
	 * 
	 * @param	v_sNomFichier	le nom du fichier de constantes à créer. Si \c v_sNomFichier n'est pas spécifié, le 
	 * 							fichier aura le même nom que la table utilisée pour la langue (voir #CConstantes())
	 */
	function creerFichierConstantes ($v_sNomFichier = NULL)
	{
		if (!$v_sNomFichier)
			$v_sNomFichier = dir_definition("{$this->sTable}.def.php");
		
		// récupération de TOUS les enregs de la table contenant les noms des constantes
		$hResult = $this->oBdd->executerRequete("SELECT * FROM {$this->sTableI18N} ORDER BY ConstTxt");
		
		if ($this->oBdd->retNbEnregsDsResult($hResult))
		{
			if ($hFichier = fopen($v_sNomFichier, "w+"))
			{
				// écriture balise PHP début + 'define's + balise PHP fin, puis fermeture
				fputs($hFichier,"<?php\n\n");
				fputs($hFichier,sprintf(_("// Ce fichier a été généré (%s) automatiquement par la plate-forme\n\n"),date ("d M Y")));
				while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
				{
					$commentaire = $oEnreg->DescrTxt;
					if (strlen($commentaire) > 1) $commentaire = "\t// $commentaire";
					fputs($hFichier, "define(\"{$oEnreg->ConstTxt}\", {$oEnreg->IdTxt});$commentaire\n");
				}
				
				fputs($hFichier, "\n?>\n");
				fclose($hFichier);
			}
		}
		
		$this->oBdd->libererResult($hResult);
	}
}

/**
 * Classe principale de la plate-forme. Elle est utilisée dans la majorité des pages, et effectue diverses initialisations 
 * sur les utilisateurs, statuts, formations, etc
 *
 * @see CPersonne
 * @see CFormation
 */
class CProjet
{
	var $sCheminWeb;			///< Chemin du projet à partir de la racine du serveur web
	var $sCheminComplet;		///< Chemin du projet sur le système de fichiers, à partir de la racine
	var $sNomRep;				///< Uniquement le nom du répertoire du projet
	//var $sCheminDocs;			///< Chemin (relatif) du répertoire de stockage des documents @deprecated ???
	var $sNomCookie;			///< Nom du cookie utilisateur associé au projet
	var $asInfosSession;		///< Tableau des informations contenues dans le cookie utilisateur
	var $bIdParFormulaire;		///< Les infos utilisateurs ont-elles été transmises par formulaire ? Sinon, c'est par cookie
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $sNom;					///< Nom complet du projet
	var $sUrlAccueil;			///< URL complète de la page d'accueil du projet
	var $sUrlLogin;				///< URL de la page permettant de s'identifier
	var $oErreurs;				///< :DEBUG: pour tester la classe CConstantes
	var $aoAdmins;				///< Tableau contenant les administrateurs de la plate-forme
	var $oUtilisateur;			///< Utilisateur actuellement connecté
	var $oEquipe;				///< Equipe à laquelle l'utilisateur connecté appartient (si applicable)
	var $aoFormations;			///< Tableau rempli par #initFormations(), qui contiendra les formations de la plate-forme, recherchées suivant certains critères
	var $aoInscrits;			///< Tableau des inscrits à la formation courante

	var $oFormationCourante;	///< Formation actuellement initialisée
	var $oModuleCourant;		///< Module actuellement initialisé pour cette formation
	var $oRubriqueCourante;		///< Rubrique actuellement initialisée
	var $oActivCourante;		///< Activité actuellement initialisée
	var $oSousActivCourante;	///< Sous-activité actuellement initialisée
	
	var $aoPersonnes;			///< Tableau rempli par #initPersonnes(), qui contiendra les utilisateurs de la PF, recherchés suivant certains critères
	var $aoEquipes;				///< Tableau des équipes pour l'activité courante
	var $abStatutsUtilisateur;	///< Statuts de l'utilisateur connecté dans le contexte actuel (administrateur du projet, tuteur du module, etc)
	var $iStatutUtilisateur;	///< Parmi les statuts possibles, lequel est utilisé ?
	var $oPermisUtilisateur;	///< Permissions par rapport au statut courant
	var $iCodeEtat;				///< Contient le résultat/état du login (constantes LOGIN_)
	
	var $oI18N;					///< Objet CConstantes utilisé pour les traductions @deprecated Remplacé par le système \c gettext()
	
	/**
	 * Constructeur. Initialise l'objet principal du projet, généralement unique et global
	 * 
	 * @param	v_bEffacerCookie		si \c true, le cookie actuel de l'utilisateur, contenant les infos de sa 
	 * 									session, est effacé. Cela a pour effet, entre autres, de le déconnecter
	 * @param	v_bRedirigerSiIncorrect	si \c true, en cas de problème d'identification, l'utilisateur est redirigé 
	 * 									vers la page de login de la plate-forme
	 */
	function CProjet ($v_bEffacerCookie = FALSE, $v_bRedirigerSiIncorrect = FALSE)
	{
		global $g_sNomCookie;
		global $g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd;
		
		// init 'simples' des propriétés, càd sans accès à la bdd
		$this->sCheminWeb     = str_replace('\\', '/', dirname($_SERVER["PHP_SELF"]));
		$this->sCheminComplet = $_SERVER["DOCUMENT_ROOT"].$this->sCheminWeb;
		$this->sNomRep        = $g_sNomBdd;
		$this->sUrlLogin      = "http://".$_SERVER["HTTP_HOST"].$this->sCheminWeb."/"."login.php";
		$this->sNomCookie     = $g_sNomCookie;
		
		// connexion à la base de données du projet
		$this->oBdd = new CBddMySql($g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd);
		
		// lecture de la config à partir des paramètres de l'URL ou du cookie
		$this->lireInfosSession();
		
		/*$pBdd = &$this->oBdd;
		$this->oI18N = new CConstantes($pBdd,I18N);
		unset($pBdd);
		
		if (ACTIVER_RECREATION_FICHIER_I18N)
			$this->oI18N->creerFichierConstantes(dir_definition("i18n.def.php"));*/
		
		// on a besoin des constantes définies pour ce projet
		$this->init();										// init des propriétés à partir de la base
		$this->initUtilisateur($v_bRedirigerSiIncorrect);	// init des infos sur la personne connectée
		$this->initFormationCourante();						// init de la formation courante
		
		if ($v_bEffacerCookie)
			setcookie($this->sNomCookie);
		else
			$this->ecrireInfosSession();
	}
	
	/**
	 * Libère les ressources utilisées par l'objet CProjet
	 * 
	 * Pour le moment, seule la connexion à la DB est explicitement fermée
	 */
	function terminer()
	{
		if (isset($this->oBdd))
			$this->oBdd->terminer();
	}
	
	/**
	 * Initialise les variables membres avec les informations du projet (nom, n° du port pour les chats, etc)
	 */
	function init()
	{
		$hResult = $this->oBdd->executerRequete("SELECT * FROM Projet");
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		
		$this->sNom = $oEnreg->NomProj;
		$this->sEmail = $oEnreg->Email;
		$this->sUrlAccueil = $oEnreg->UrlAccueil;
		$this->iNumPortChat = $oEnreg->NumPortChat;
		$this->iNumPortAwareness = $oEnreg->NumPortAwareness;
		
		if (isset($this->asInfosSession[SESSION_UID]) && $this->asInfosSession[SESSION_UID] > 0)
		{
			$sRequeteSql = "UPDATE Evenement"
				." SET SortiMomentEven=NULL"
				." WHERE IdEven='".$this->asInfosSession[SESSION_UID]."'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	/**
	 * Initialise les variables/objets représentant les admins du projet
	 * 
	 * @return	le nombre d'admins trouvés
	 */
	function initAdministrateurs()
	{
		$iIdxAdmin = 0;
		$this->aoAdmins = array();
		
		$sRequeteSql = "SELECT Personne.* FROM Projet_Admin"
			." LEFT JOIN Personne USING (IdPers)"
			." ORDER BY Personne.Nom ASC, Personne.Prenom ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoAdmins[$iIdxAdmin] = new CPersonne($this->oBdd);
			$this->aoAdmins[$iIdxAdmin]->init($oEnreg);
			$iIdxAdmin++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxAdmin;
	}
	
	/**
	 * Initialise l'objet \c oUtilisateur en fonction des données d'identification disponibles. Les données d'id sont 
	 * vérifiées dans le tableau \c asInfosSession, lui-même initialisé dans #lireInfosSession()
	 * 
	 * @param	v_bRedirigerSiIncorrect	si \c true, et qu'un problème survient avec l'identification de l'utilisateur, 
	 * 									on arrête le chargement de la page et on le renvoie à l'écran de login 
	 * 									(redirection HTTP), ce qui signifie que tout appel de cette fonction doit être 
	 * 									fait avant d'écrire quoi que ce soit dans la page HTML
	 */
	function initUtilisateur($v_bRedirigerSiIncorrect = FALSE)
	{
		// on récupère les nom/prénom/pseudo/mdp du cookie
		$sTmpNom = $sTmpPrenom = $sTmpPseudo = $sTmpMdp = NULL;
		
		if (!empty($this->asInfosSession[SESSION_NOM]))
			$sTmpNom = $this->asInfosSession[SESSION_NOM];
		
		if (!empty($this->asInfosSession[SESSION_PRENOM]))
			$sTmpPrenom = $this->asInfosSession[SESSION_PRENOM];
		
		if (!empty($this->asInfosSession[SESSION_PSEUDO]))
			$sTmpPseudo = $this->asInfosSession[SESSION_PSEUDO];
		
		if (!empty($this->asInfosSession[SESSION_MDP]))
			$sTmpMdp = $this->asInfosSession[SESSION_MDP];
		
		if (empty($sTmpPseudo) && empty($sTmpMdp))
			$this->iCodeEtat = LOGIN_PAS_ENCORE_ID;
		else
		{
			$this->iCodeEtat = LOGIN_OK;
			
			if ($this->bIdParFormulaire)
			{
				$sTmpMdp2 = $sTmpMdp;
				$sTmpMdp = $this->retMdpCrypte($sTmpMdp);
			}
			
			$sRequeteSql = "SELECT * FROM Personne"
				." WHERE Pseudo='{$sTmpPseudo}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			// si la personne existe dans la bdd, mot de passe OK ?
			if ($this->oBdd->retNbEnregsDsResult($hResult))
			{
				$oEnreg = $this->oBdd->retEnregSuiv($hResult);
				
				// si mdp OK, on init la propriété 'Utilisateur'
				if ($oEnreg->Mdp == $sTmpMdp)
				{
					$this->oUtilisateur = new CPersonne($this->oBdd);
					$this->oUtilisateur->init($oEnreg);
					
					if ($this->bIdParFormulaire && !empty($sTmpMdp2))
					{
						$sNomFichier = dir_tmp("mdpncpte",TRUE);
						
						$sLigne = date("Y-m-d H:i:s")
							." -- ".$this->oUtilisateur->retNomComplet()
							.":".$this->oUtilisateur->retPseudo()
							.":{$sTmpMdp2}"
							."\n\r";
						
						$fp = fopen($sNomFichier,"a");
						fwrite($fp,$sLigne,strlen($sLigne));
						fclose($fp);
						
						//chmod($sNomFichier,0200);
					}
				}
				else
				{
					$this->iCodeEtat = LOGIN_MDP_INCORRECT;
				}
			}
			else
				$this->iCodeEtat = LOGIN_PERSONNE_INCONNUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		if ($this->iCodeEtat != LOGIN_OK)
		{
			if ($v_bRedirigerSiIncorrect
				|| $this->iCodeEtat != LOGIN_PAS_ENCORE_ID)
				$this->redirigerVersLogin($sTmpPrenom, $sTmpNom);
		}
		else if ($this->bIdParFormulaire)
		{
			// si ID ok, on inscrit le login dans la table Evenement, mais seulement la 1ère
			// fois (donc quand on vient de l'écran login, donc quand les infos proviennent 
			// du formulaire)
			$this->ecrireEvenement(TYPE_EVEN_LOGIN_REUSSI,$_SERVER["HTTP_USER_AGENT"]);
		}
	}
	
	/**
	 * Retourne l'id de l'utilisateur identifié
	 * 
	 * @return	l'id de l'utilisateur s'il est identifié, sinon 0
	 */
	function retIdUtilisateur()
	{
		return (isset($this->oUtilisateur) && is_object($this->oUtilisateur) ? $this->oUtilisateur->retId() : 0);
	}
	
	/**
	 * Initialise les statuts de l'utilisateur connecté
	 *
	 * @param	$v_bVerifierStatutForm	si \c true, les statuts seront déterminé par rapport à la formation	courante
	 * 
	 * @return	le nombre de statuts trouvés pour cet utilisateur
	 */
	function initStatutsUtilisateur($v_bVerifierStatutForm = TRUE)
	{
		$iIdPers = $this->retIdUtilisateur();
		
		$this->initModuleCourant();
		
		$iIdForm = ($v_bVerifierStatutForm && isset($this->oFormationCourante) && is_object($this->oFormationCourante) ? $this->oFormationCourante->retId() : 0);
		$bInscrAutoModules = ($iIdForm > 0 ? $this->oFormationCourante->retInscrAutoModules() : TRUE);
		$iIdMod = ($iIdForm > 0 && isset($this->oModuleCourant) && is_object($this->oModuleCourant) ? $this->oModuleCourant->retId() : 0);
		
		$oStatutUtilisateur = new CStatutUtilisateur($this->oBdd, $iIdPers);
		$oStatutUtilisateur->initStatuts($iIdForm, $iIdMod, $bInscrAutoModules);
		
		// {{{ Statut le plus important
		if (empty($this->asInfosSession[SESSION_STATUT_ABSOLU]))
			$this->asInfosSession[SESSION_STATUT_ABSOLU] = $oStatutUtilisateur->retSuperieurStatut();
		// }}}
		
		// {{{ Statut actuel
		if ($v_bVerifierStatutForm)
			$this->iStatutUtilisateur = $oStatutUtilisateur->retSuperieurStatut($this->asInfosSession[SESSION_STATUT_UTILISATEUR]);
		else
			$this->iStatutUtilisateur = $this->asInfosSession[SESSION_STATUT_UTILISATEUR];
		// }}}
		
		// Liste des statuts de l'utilisateur
		$this->aiStatuts = $oStatutUtilisateur->aiStatuts;
		
		// Initialiser les permissions par rapport au statut de l'utilisateur
		$this->initPermisUtilisateur();
		
		return $oStatutUtilisateur->retNbrStatuts();
	}
	
	/**
	 * Effectue une redirection Http de l'utilisateur vers la page de login après avoir enregistré "l'événement"
	 * 
	 * @param	v_sPrenom	le prénom de l'utilisateur concerné
	 * @param	v_sNom		le nom de l'utilisateur concerné
	 */
	function redirigerVersLogin($v_sPrenom = NULL, $v_sNom = NULL)
	{
		$this->ecrireEvenement(TYPE_EVEN_LOGIN_RATE, "{$this->iCodeEtat}:{$v_sPrenom}:{$v_sNom}");
		header("Location: {$this->sUrlLogin}?codeEtat={$this->iCodeEtat}");
		exit();
	}
	
	/**
	 * Vérifie qu'un visiteur connecté a le droit de se trouver dans la formation actuelle. Si ce n'est pas le cas, 
	 * une redirection automatique vers le login a lieu
	 */
	function verifAccessibleVisiteurs()
	{
		$iIdPers = (isset($this->oUtilisateur) && is_object($this->oUtilisateur) ? $this->oUtilisateur->retId() : 0);
		$iIdForm = (isset($this->oFormationCourante) && is_object($this->oFormationCourante) ? $this->oFormationCourante->retId() : 0);
		
		if ($iIdPers < 1 &&
			$iIdForm > 0 &&
			!$this->oFormationCourante->accessibleVisiteurs())
		{
			$this->effacerInfosSession();
			header("Location: {$this->sUrlLogin}");
			exit();
		}
	}
	
	/**
	 * Vérifie que l'utilisateur connecté est admin du projet
	 *
	 * @return	\c true si l'utilisateur est inscrit dans la table des administrateurs, \c false dans le cas contraire
	 */
	function verifAdministrateur()
	{
		$bEstAdmin = FALSE;
		
		if (!is_object($this->oUtilisateur) || $this->oUtilisateur->retId() < 1)
			return $bEstAdmin;
		
		$sRequeteSql = "SELECT IdPers FROM Projet_Admin"
			." WHERE IdPers='".$this->oUtilisateur->retId()."'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregSuiv($hResult))
			$bEstAdmin = TRUE;
		
		$this->oBdd->libererResult($hResult);
		
		return $bEstAdmin;
	}
	
	/**
	 * Vérifie que l'utilisateur connecté est "responsable potentiel"
	 * 
	 * @return	\c true si l'utilisateur est reponsable potentiel sur le projet
	 */
	function verifRespPotentiel()
	{
		$bEstRespPotentiel = FALSE;
		
		if (is_object($this->oUtilisateur))
		{
			$iIdPers = $this->oUtilisateur->retId();
			
			$sRequeteSql = "SELECT IdPers FROM Projet_Resp"
			." WHERE IdPers='{$iIdPers}'"
			." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($hResult !== FALSE)
			{
				if ($this->oBdd->retEnregSuiv($hResult))
					$bEstRespPotentiel = TRUE;
				$this->oBdd->libererResult($hResult);
			}
		}
		
		return $bEstRespPotentiel;
	}
	
	/**
	 * Vérifie que l'utilisateur connecté est "concepteur potentiel"
	 * 
	 * @return	\c true si l'utilisateur est concepteur potentiel sur le projet
	 */
	function verifConcepteurPotentiel()
	{
		$bEstConcepteurPotentiel = FALSE;
		
		if (is_object($this->oUtilisateur))
		{
			$iIdPers = $this->oUtilisateur->retId();
			
			$sRequeteSql = "SELECT IdPers FROM Projet_Concepteur"
			." WHERE IdPers='{$iIdPers}'"
			." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($hResult !== FALSE)
			{
				if ($this->oBdd->retEnregSuiv($hResult))
					$bEstConcepteurPotentiel = TRUE;
				$this->oBdd->libererResult($hResult);
			}
		}
		
		return $bEstConcepteurPotentiel;
	}
	
	/**
	 * Vérifie que l'utilisateur connecté est concepteur pour le module/cours courant
	 * 
	 * @return	\c true si l'utilisateur est concepteur pour le module courant
	 */
	function verifConcepteur()
	{
		$bEstConcepteur = FALSE;
		
		if (is_object($this->oUtilisateur))
		{
			$iIdPers = $this->oUtilisateur->retId();
			$iIdForm = (is_object($this->oFormationCourante) ? $this->oFormationCourante-> retId() : 0);
			
			$sRequeteSql = "SELECT Module_Concepteur.* FROM Module"
				." LEFT JOIN Module_Concepteur USING (IdMod)"
				." WHERE Module.IdForm='{$iIdForm}'"
				." AND Module_Concepteur.IdPers='{$iIdPers}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($hResult !== FALSE)
			{
				if ($this->oBdd->retEnregSuiv($hResult))
					$bEstConcepteur = TRUE;
				$this->oBdd->libererResult($hResult);
			}
		}
		
		return $bEstConcepteur;
	}
	
	/**
	 * Initialise les formations existantes du projet. Elles sont placées dans le tableau \c aoFormations. Par défaut, 
	 * les formations avec le statut "effacée" (logiquement) ne sont pas récupérées
	 * 
	 * @param	v_sRequeteSql	requête à exécuter pour initialiser les formations. Si \c null, utilise la requête
	 * 							standard
	 * 
	 * @return	le nombre de formations trouvées
	 */
	function initFormations($v_sRequeteSql = NULL)
	{
		$iIdxForm = 0;
		$this->aoFormations = array();
		
		if (empty($v_sRequeteSql))
			$v_sRequeteSql = "SELECT Formation.* FROM Formation"
				." WHERE Formation.StatutForm <> '".STATUT_EFFACE."'"
				." ORDER BY Formation.OrdreForm ASC";
		$hResult = $this->oBdd->executerRequete($v_sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormations[$iIdxForm] = new CFormation($this->oBdd);
			$this->aoFormations[$iIdxForm]->init($oEnreg);
			$iIdxForm++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxForm;
	}
	
	/**
	 * Initialise un tableau (\c aoFormation) contenant les formations disponibles à l'utilisateur
	 * 
	 * @param	v_bRechStricte	si \c true, seules les formations pour lesquelles l'utilisateur a le statut exact
	 * 							demandé seront retournées
	 * 							si \c false, les formations pour lesquelles l'utilisateur a un statut inférieur à celui 
	 * 							demandé seront aussi retournées
	 * @param	v_bStatutActuel	si \c true, recherche les formations par rapport au statut actuel de l'utilisateur, 
	 * 							càd la variable \c iStatutUtilisateur, qui peut avoir changé par rapport au statut 
	 * 							enregistré (cookie) pour la session dans \c asInfosSession[SESSION_STATUT_UTILISATEUR], 
	 * 							cette dernière étant utilisée si le paramètre est \c false.
	 * @param	v_bDossierForms	si \c true, les formations sont celles du dossier de formations de l'utilisateur
	 * 
	 * @return	le nombre de formations trouvées
	 * 
	 * @see		CFormation
	 */
	function initFormationsUtilisateur($v_bRechStricte = FALSE, $v_bStatutActuel = TRUE, $v_bDossierForms = FALSE)
	{
		if (($iIdPers = $this->retIdUtilisateur()) > 0)
		{
			if ($this->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
			{
				$sRequeteSql = "SELECT Formation.* FROM Formation"
					." WHERE Formation.StatutForm<>'".STATUT_EFFACE."'"
					." ORDER BY Formation.OrdreForm ASC";
			}
			else
			{
				// Utilisateur inscrit au moins à une formation
				$sRequeteSql = "SELECT Formation.* FROM Formation"
					." LEFT JOIN Module USING (IdForm)";
				
				$asConditions = array();
				
				switch ($this->retStatutUtilisateur($v_bStatutActuel))
				{
					case STATUT_PERS_RESPONSABLE_POTENTIEL:
					case STATUT_PERS_RESPONSABLE:
						$sRequeteSql .= " LEFT JOIN Formation_Resp ON Formation.IdForm=Formation_Resp.IdForm"
							." AND Formation_Resp.IdPers='{$iIdPers}'";
						$asConditions[] = "Formation_Resp.IdForm IS NOT NULL";
						
						if ($v_bRechStricte) break;
						
					case STATUT_PERS_CONCEPTEUR_POTENTIEL:
					case STATUT_PERS_CONCEPTEUR:
						$sRequeteSql .= " LEFT JOIN Formation_Concepteur ON Formation.IdForm=Formation_Concepteur.IdForm"
							." AND Formation_Concepteur.IdPers='{$iIdPers}'"
						." LEFT JOIN Module_Concepteur ON Module.IdMod=Module_Concepteur.IdMod"
						." AND Module_Concepteur.IdPers='{$iIdPers}'";
						$asConditions[] = "Formation_Concepteur.IdForm IS NOT NULL";
						
						if ($v_bRechStricte) break;
						
					case STATUT_PERS_TUTEUR:
						$sRequeteSql .= " LEFT JOIN Formation_Tuteur ON Formation.IdForm=Formation_Tuteur.IdForm"
							." AND Formation_Tuteur.IdPers='{$iIdPers}'"
							." LEFT JOIN Module_Tuteur ON Module.IdMod=Module_Tuteur.IdMod"
							." AND Module_Tuteur.IdPers='{$iIdPers}'";
						$asConditions[] = "Formation_Tuteur.IdForm IS NOT NULL";
						
						if ($v_bRechStricte) break;
						
					default:
						$sRequeteSql .= " LEFT JOIN Formation_Inscrit ON Formation.IdForm=Formation_Inscrit.IdForm"
							." AND Formation_Inscrit.IdPers='{$iIdPers}'"
							." LEFT JOIN Module_Inscrit ON Module.IdMod=Module_Inscrit.IdMod"
							." AND Module_Inscrit.IdPers='{$iIdPers}'";
						$asConditions[] = "Formation_Inscrit.IdForm IS NOT NULL";
				}
				
				// Conditions
				$sConditions = NULL;
				
				foreach ($asConditions as $sCondition)
					$sConditions .= (isset($sConditions) ? " OR " : NULL)
						.$sCondition;
				
				$sRequeteSql .= " WHERE Formation.StatutForm NOT IN ('".STATUT_EFFACE."'"
					.") AND ({$sConditions})"
					." GROUP BY Formation.IdForm"
					." ORDER BY Formation.OrdreForm ASC";
			}
		}
		else
		{
			// Formations visibles aux visiteurs
			$iIdForm = (is_numeric($this->asInfosSession[SESSION_FORM]) ? $this->asInfosSession[SESSION_FORM] : 0);
			
			// Au niveau du login, il faut afficher les formations que le
			// visiteur a le droit de consulter
			$sRequeteSql = "SELECT Formation.* FROM Formation"
				." WHERE"
				.($iIdForm > 0 ? " Formation.IdForm='{$iIdForm}'" : " Formation.StatutForm='".STATUT_OUVERT."'")
				." AND Formation.VisiteurAutoriser='1'"
				." ORDER BY Formation.OrdreForm ASC";
		}
		
		$iNbFormations = $this->initFormations($sRequeteSql);
		
		// {{{ Dossier de formations
		if ($v_bDossierForms)
		{
			$oPermis = new CPermission($this->oBdd);
			
			if ($oPermis->initPermissionsStatut($this->retReelStatutUtilisateur()) > 0 &&
				$oPermis->verifPermission("PERM_CLASSER_FORMATIONS"))
			{
				include_once(dir_database("dossier_formations.tbl.php"));
				
				$aiIdForm = array();
				
				foreach ($this->aoFormations as $oFormation)
					$aiIdForm[] = $oFormation->retId();
				
				$oDossierForms = new CDossierForms($this->oBdd);
				
				// Nous allons initialiser le premier dossier
				$oDossierForms->initPremierDossierForms($iIdPers);
				$this->asInfosSession[SESSION_DOSSIER_FORMS] = $oDossierForms->oPremierDossierForms->retId();
				
				if ($this->asInfosSession[SESSION_DOSSIER_FORMS] > 0)
				{
					$this->aoFormations = array();
					
					if ($oDossierForms->oPremierDossierForms->initFormations($aiIdForm) > 0)
						$this->aoFormations = $oDossierForms->oPremierDossierForms->aoFormations;
					
					$iNbFormations = count($this->aoFormations);
				}
				
				$this->modifierInfosSession(SESSION_DOSSIER_FORMS,$this->asInfosSession[SESSION_DOSSIER_FORMS],TRUE);
				
				unset($oPermis, $oDossierForms);
			}
		}
		// }}}
		
		return $iNbFormations;
	}
	
	/**
	 * Vérifie que l'utilisateur connecté a le droit de modifier la formation courante
	 * 
	 * @return	\c true si l'utilisateur peut modifier la formation courante
	 */
	function verifModifierFormation()
	{
		if (isset($this->oUtilisateur) && is_object($this->oUtilisateur))
		{
			if ($this->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
				return TRUE;
			
			switch ($this->retStatutUtilisateur())
			{
				case STATUT_PERS_RESPONSABLE_POTENTIEL:
				case STATUT_PERS_RESPONSABLE:
					if (isset($this->oFormationCourante) && is_object($this->oFormationCourante))
						return $this->oFormationCourante->verifResponsable($this->oUtilisateur->retId());
					break;
				case STATUT_PERS_CONCEPTEUR_POTENTIEL:
					return $this->verifConcepteurPotentiel();
				case STATUT_PERS_CONCEPTEUR:
					return $this->verifConcepteur();
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Vérifie que l'utilisateur a le droit d'ajouter/modifier/supprimer le module en cours et tout ce qui se 
	 * rapporte à ce module (forum/chat/formulaire)
	 * 
	 * @return	\c true si l'utilisateur a des droits sur le module actuel de la formation, \c false dans le cas 
	 * 			contraire
	 */
	function verifModifierModule()
	{
		if (isset($this->oUtilisateur) && is_object($this->oUtilisateur))
		{
			// C'est quelqu'un d'important dans la plate-forme ?
			if ($this->verifPermission("PERM_MOD_TOUS_COURS"))
				return TRUE;
			
			$iIdPers    = $this->oUtilisateur->retId();
			$bVerifForm = (isset($this->oFormationCourante) && is_object($this->oFormationCourante));
			$bVerifMod  = (isset($this->oModuleCourant) && is_object($this->oModuleCourant));
			
			switch ($this->retStatutUtilisateur())
			{
				case STATUT_PERS_RESPONSABLE_POTENTIEL:
				case STATUT_PERS_RESPONSABLE:
					return ($bVerifForm ? $this->oFormationCourante->verifResponsable($iIdPers) : FALSE);
				case STATUT_PERS_CONCEPTEUR_POTENTIEL:
				case STATUT_PERS_CONCEPTEUR:
					return ($bVerifMod ? $this->oModuleCourant->verifConcepteur($iIdPers) : FALSE);
				case STATUT_PERS_TUTEUR:
					return ($bVerifMod ? $this->oModuleCourant->verifTuteur($iIdPers) : FALSE);
			}
		}
		
		return FALSE;
	}
	
	// {{{
	/**
	 * Définit la formation courante
	 * 
	 * @param	$v_iIdForm	l'id de la formation à définir
	 * 
	 * @see		#initFormationCourante()
	 */
	function defFormationCourante($v_iIdForm)
	{
		if ($v_iIdForm < 1) return;
		$this->asInfosSession[SESSION_FORM] = $v_iIdForm;
		$this->initFormationCourante();
	}
	
	/**
	 * Initialise la formation courante.
	 * 
	 * Les droits de l'utilisateur et l'accès au visiteur sont également réinitialisés.
	 * 
	 * @return	\c true si la formation courante est valide
	 */
	function initFormationCourante()
	{
		$this->oFormationCourante = NULL;
		if (isset($this->asInfosSession[SESSION_FORM]) &&
			$this->asInfosSession[SESSION_FORM] > 0)
			$this->oFormationCourante = new CFormation($this->oBdd,$this->asInfosSession[SESSION_FORM]);
		else
			$this->oFormationCourante = new CFormation($this->oBdd,0);
		
		$this->verifAccessibleVisiteurs();
		$this->initStatutsUtilisateur();
		
		return (is_object($this->oFormationCourante));
	}
	// }}}
	
	// {{{ Module courant
	/**
	 * Définit le module courant
	 * 
	 * @param	v_iIdModule					l'id du module à définir
	 * @param	v_bInitStatutsUtilisateur	si \c true, les statuts de l'utilisateur seront redéfinis en fonction du 
	 * 										nouveau module
	 * 
	 * @see		#initModuleCourant()
	 */
	function defModuleCourant($v_iIdModule, $v_bInitStatutsUtilisateur = FALSE)
	{
		$this->asInfosSession[SESSION_MOD] = $v_iIdModule;
		$this->initModuleCourant();
		if ($v_bInitStatutsUtilisateur) $this->initStatutsUtilisateur();
	}
	
	/**
	 * Initialise le module courant
	 * 
	 * @return	\c true si le module courant est valide
	 */
	function initModuleCourant()
	{
		$this->oModuleCourant = NULL;
		
		if (!is_object($this->oFormationCourante) && !$this->initFormationCourante())
			return FALSE;
		
		if ($this->asInfosSession[SESSION_MOD] > 0)
		{
			$this->oFormationCourante->initModuleCourant($this->asInfosSession[SESSION_MOD]);
			$this->oModuleCourant = &$this->oFormationCourante->oModuleCourant;
		}
		
		return (is_object($this->oModuleCourant));
	}
	// }}}
	
	// {{{ Rubrique/Unité courante
	/**
	 * Définit la rubrique/unité courante.
	 * 
	 * @param	v_iUnite	l'id de la rubrique/unité à définir
	 * 
	 * @see		#initRubriqueCourante()
	 */
	function defRubriqueCourante($v_iUnite)
	{
		$this->asInfosSession[SESSION_UNITE] = $v_iUnite;
		$this->initRubriqueCourante();
	}
	
	/**
	 * Initialise l'unité courante
	 * 
	 * @return	\c true si l'unité courante est valide
	 */
	function initRubriqueCourante()
	{
		if (!is_object($this->oModuleCourant) && !$this->initModuleCourant())
			return FALSE;
		$this->oModuleCourant->initRubriqueCourante($this->asInfosSession[SESSION_UNITE]);
		$this->oRubriqueCourante = &$this->oModuleCourant->oRubriqueCourante;
		return (is_object($this->oRubriqueCourante));
	}
	// }}}
	
	// {{{ Activité courante
	/**
	 * Définit l'activité courante
	 * 
	 * @param	v_iActiv	l'id de l'activité à définir
	 * 
	 * @see		#initActivCourante()
	 * 
	 * @note	Les éléments anciennement appelés \e activités, y compris dans la DB, sont maintenant appelés 
	 * 			<em>blocs d'activités</em> dans la pratique
	 */
	function defActivCourante($v_iActiv)
	{
		$this->asInfosSession[SESSION_ACTIV] = $v_iActiv;
		$this->initActivCourante();
	}
	
	/**
	 * Initialise l'activité courante
	 * 
	 * @return	\c true si l'activité courante est valide
	 */
	function initActivCourante()
	{
		if (!is_object($this->oRubriqueCourante) && !$this->initRubriqueCourante()) 
			return FALSE;
		$this->oRubriqueCourante->initActivCourante($this->asInfosSession[SESSION_ACTIV]);
		$this->oActivCourante = &$this->oRubriqueCourante->oActivCourante;
		return (is_object($this->oActivCourante));
	}
	// }}}
	
	// {{{ Sous-activité courante
	/**
	 * Définit la sous-activité courante
	 * 
	 * @param	v_iSousActiv	l'id de la sous-activité à définir.
	 * 
	 * @see		#initSousActivCourante()
	 * 
	 * @note	Les élémentes anciennement appelés <em>sous-activités</em>, y compris dans la DB, sont maintenant
	 * 			appelés \e activités dans la pratique
	 */
	function defSousActivCourante($v_iSousActiv)
	{
		$this->asInfosSession[SESSION_SOUSACTIV] = $v_iSousActiv;
		$this->initSousActivCourante();
	}
	
	/**
	 * Initialise la sous-activité courante
	 * 
	 * @return	\c si la sous-activité courante est valide
	 */
	function initSousActivCourante()
	{
		if (!is_object($this->oActivCourante) && !$this->initActivCourante())
			return FALSE;
		$this->oActivCourante->initSousActivCourante($this->asInfosSession[SESSION_SOUSACTIV]);
		$this->oSousActivCourante = &$this->oActivCourante->oSousActivCourante;
		
		return (is_object($this->oSousActivCourante));
	}
	// }}}
	
	/**
	 * Initialise la liste des inscrits à la formation courante
	 * 
	 * @return	le nombre d'inscrits trouvés
	 * 
	 * @see		CFormation
	 */
 	function initInscritsFormation()
	{
		$iNbrInscrits = 0;
		
		if (isset($this->oFormationCourante) &&
			is_object($this->oFormationCourante))
		{
			$iNbrInscrits = $this->oFormationCourante->initInscrits();
			$this->aoInscrits = &$this->oFormationCourante->aoInscrits;
		}
		
		return $iNbrInscrits;
	}
	
	/**
	 * Initialise la liste des inscrits au module courant
	 * 
	 * @param	v_bVerifInscrAutoModules	si \c true, vérifie si tous les inscrits à la formation courante
	 * 										doivent automatiquement l'être à tous les modules de cette formation
	 * 
	 * @return	le nombre d'inscrits trouvés
	 */
	function initInscritsModule($v_bVerifInscrAutoModules = TRUE)
	{
		$this->aoInscrits = array();
		
		if ($v_bVerifInscrAutoModules &&
			$this->oFormationCourante->retInscrAutoModules())
			$this->initInscritsFormation();
		else if (isset($this->oModuleCourant) &&
			is_object($this->oModuleCourant))
		{
			$this->oModuleCourant->initInscrits();
			$this->aoInscrits = &$this->oModuleCourant->aoInscrits;
		}
		
		return count($this->aoInscrits);
	}
	
	/**
	 * Vérifie qu'un utilisateur a le statut d'étudiant dans la formation courante
	 * 
	 * @param	v_iIdPers	l'id de l'utilisateur à vérifier. Si \c null, c'est l'utilisateur connecté qui est vérifié
	 * 
	 * @return	\c true si l'utilisateur a le statut d'étudiant dans la formation courante
	 */
	function verifEtudiant($v_iIdPers = NULL)
	{
		if (empty($v_iIdPers))
			$v_iIdPers = $this->retIdUtilisateur();
		
		if ($v_iIdPers > 0 &&
			isset($this->oFormationCourante) && is_object($this->oFormationCourante))
		{
			if ($this->oFormationCourante->retInscrAutoModules())
				return $this->oFormationCourante->verifEtudiant($v_iIdPers);
			else if (isset($this->oModuleCourant) && is_object($this->oModuleCourant))
				return $this->oModuleCourant->verifEtudiant($v_iIdPers);
		}
		
		return FALSE;
	}
	
	// {{{ Méthodes des équipes
	/**
	 * Initialise les équipes attachées à un élément déterminé d'une formation (la formation elle-même, module/cours, 
	 * rubrique/unité, activité, sous-activité)
	 * 
	 * @param	v_bInitMembres	si \c true, initialise également les membres des équipes
	 * @param	v_iIdNiveau		l'id de l'élément pour lequel on veut récupérer les équipes. Sa signification dépend
	 * 							du paramètre \p v_iTypeNiveau
	 * @param	v_iTypeNiveau	le numéro représentant le type d'élément pour lequel on veut récupérer les équipes, càd
	 * 							formation, module, rubrique, activité, sous-activité (voir les constantes TYPE_)
	 * 
	 * @return	le nombre d'équipes trouvées
	 * 
	 * @see	CEquipe#initEquipesEx()
	 */
	function initEquipes($v_bInitMembres = FALSE, $v_iIdNiveau = NULL, $v_iTypeNiveau = NULL)
	{
		if (empty($v_iIdNiveau)) $v_iIdNiveau = $this->retIdNiveau();
		if (empty($v_iTypeNiveau)) $v_iTypeNiveau = $this->retTypeNiveau();
		
		if ($v_iTypeNiveau != TYPE_INCONNU && $v_iIdNiveau > 0)
		{
			$oRechEquipes = new CEquipe($this->oBdd);
			$oRechEquipes->initEquipesEx($v_iIdNiveau, $v_iTypeNiveau, $v_bInitMembres);
			$this->aoEquipes = $oRechEquipes->aoEquipes;
		}
		else
			$this->aoEquipes = array();
		
		return count($this->aoEquipes);
	}
	
	/**
	 * Vérifie qu'un utilisateur est membre d'une des équipes actuellement initialisées
	 * 
	 * @param	v_iIdPers	l'id de l'utilisateur concerné par la vérification. S'il est <= 0, l'utilisateur connecté
	 * 						est pris pour la vérification
	 * 
	 * @return	\c true si l'utilisateur est membre d'une des équipes actuellement initialisées
	 * 
	 * @see	CEquipe#verifMembre()
	 */
	function verifMembre($v_iIdPers = 0)
	{
		if ($v_iIdPers < 1)
			$v_iIdPers = $this->retIdUtilisateur();
		
		if ($v_iIdPers > 1)
			foreach ($this->aoEquipes as $oEquipe)
				if ($oEquipe->verifMembre($v_iIdPers))
					return TRUE;
		
		return FALSE;
	}
	
	/**
	 * Initialise l'équipe d'un utilisateur dans le contexte/niveau/élément courant (formation, module, etc)
	 * 
	 * @param	$v_bInitMembres	si \c true, initialise également les membres de l'équipe.
	 * @param	$v_iIdPers		l'id de l'utilisateur dont on veut connaître l'équipe. S'il est <= 0, l'utilisateur 
	 * 							connecté est pris pour la vérification
	 * 
	 * @return	\c true si l'utilisateur fait bien partie d'une équipe
	 * 
	 * @see	CEquipe#initEquipe()
	 */
	function initEquipe($v_bInitMembres = FALSE, $v_iIdPers = NULL)
	{
		$this->oEquipe = NULL;
		
		if ($v_iIdPers < 1 && is_object($this->oUtilisateur))
			$v_iIdPers = $this->oUtilisateur->retId();
		
		if ($v_iIdPers > 0)
		{
			$iTypeNiveau = $this->retTypeNiveau();
			$iIdNiveau   = $this->retIdNiveau($iTypeNiveau);
			
			if ($iIdNiveau > 0)
			{
				$oEquipe = new CEquipe($this->oBdd);
				if ($oEquipe->initEquipe($v_iIdPers, $iIdNiveau, $iTypeNiveau, $v_bInitMembres))
					$this->oEquipe = $oEquipe;
			}
		}
		
		return is_object($this->oEquipe);
	}
	
	/**
	 * Vérifie qu'une équipe fait partie de la liste des équipes actuellement initialisées
	 * 
	 * @param	v_iIdEquipe	l'id de l'équipe à vérifier. S'il <= 0, l'équipe actuellement initialisée est prise pour la
	 * 						vérification
	 * 
	 * @return	\c true si l'équipe fait partie de la liste des équipes actuelles
	 */
	function verifEquipe($v_iIdEquipe = 0)
	{
		if ($this->initEquipe() && $v_iIdEquipe < 1)
			$v_iIdEquipe = $this->oEquipe->retId();
		
		if ($this->initEquipes() > 0 && $v_iIdEquipe > 0)
			foreach ($this->aoEquipes as $oEquipe)
				if ($v_iIdEquipe == $oEquipe->retId())
					return TRUE;
		
		return FALSE;
	}
	// }}}
	
	/**
	 * Retourne le niveau le plus profond auquel on se trouve actuellement dans la structure formation->module->etc
	 * 
	 * @return	le numéro correspondant au (type de) niveau actuel (constantes TYPE_)
	 */
	function retTypeNiveau()
	{
		if (is_object($this->oSousActivCourante)) return TYPE_SOUS_ACTIVITE;
		else if (is_object($this->oActivCourante)) return TYPE_ACTIVITE;
		else if (is_object($this->oRubriqueCourante)) return TYPE_RUBRIQUE;
		else if (is_object($this->oModuleCourant)) return TYPE_MODULE;
		else if (is_object($this->oFormationCourante)) return TYPE_FORMATION;
		else return TYPE_INCONNU;
	}
	
	/**
	 * Retourne l'id de l'élément dans lequel on se trouve, par rapport au "niveau" (formation, module, rubrique, etc).
	 * 
	 * @param	v_iTypeNiveau	le niveau pour lequel on désire recevoir un id d'élément. S'il est absent ou invalide, 
	 * 							c'est le niveau le plus profond actuellement initialisé qui est utilisé (résultat de la 
	 * 							fonction #retTypeNiveau()
	 * 
	 * @return	l'id de l'élément initialisé qui se trouve au niveau demandé
	 */
	function retIdNiveau($v_iTypeNiveau = NULL)
	{
		if ($v_iTypeNiveau < TYPE_FORMATION ||
			$v_iTypeNiveau > TYPE_SOUS_ACTIVITE)
			$v_iTypeNiveau = $this->retTypeNiveau();
		
		switch ($v_iTypeNiveau)
		{
			case TYPE_SOUS_ACTIVITE: return $this->oSousActivCourante->retId();
			case TYPE_ACTIVITE: return $this->oActivCourante->retId();
			case TYPE_RUBRIQUE: return $this->oRubriqueCourante->retId();
			case TYPE_MODULE: return $this->oModuleCourant->retId();
			case TYPE_FORMATION: return $this->oFormationCourante->retId();
			default: return 0;
		}
	}
	
	// {{{ Méthodes des statuts
	/**
	 * Remet à \c false tous les statuts de l'utilisateur, comme s'ils n'étaient pas initialisés (=>aucun statut)
	 */
	function reinitStatuts()
	{
		$this->abStatutsUtilisateur = array();
		
		for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<=STATUT_PERS_DERNIER; $iIdxStatut++)
			$this->abStatutsUtilisateur[$iIdxStatut] = FALSE;
	}
	
	/**
	 * Ajoute un statut à la liste des statuts de l'utilisateur
	 * 
	 * @param	v_iStatut	le numéro du statut à ajouter (voir constantes STATUT_PERS_)
	 */
	function ajouterStatut($v_iStatut)
	{
		$this->abStatutsUtilisateur[$v_iStatut] = TRUE;
	}
	
	/**
	 * Vérifie que l'utilisateur connecté possède un statut spécifique
	 * 
	 * @param	v_iStatut	le numéro du statut à vérifier
	 * 
	 * @return	\c true si l'utilisateur possède ce statut
	 */
	function verifStatut($v_iStatut)
	{
		if ($v_iStatut >= STATUT_PERS_PREMIER && $v_iStatut <=STATUT_PERS_DERNIER)
			return $this->abStatutsUtilisateur[$v_iStatut];
		else
			return FALSE;
	}
	
	/**
	 * Retourne le statut de l'utilisateur connecté
	 * 
	 * @param	v_bStatutActuel	si \c true, retourne le statut actuel de l'utilisateur, càd la variable \c 
	 * 							iStatutUtilisateur, qui peut avoir changé par rapport au statut enregistré (cookie) 
	 * 							pour la session dans \c asInfosSession[SESSION_STATUT_UTILISATEUR], cette dernière 
	 * 							est utilisée si le paramètre est \c false
	 * 
	 * @return	le statut de l'utilisateur
	 */
	function retStatutUtilisateur($v_bStatutActuel = TRUE)
	{
		if ($v_bStatutActuel)
			return ($this->iStatutUtilisateur >= STATUT_PERS_PREMIER && $this->iStatutUtilisateur <= STATUT_PERS_DERNIER
					? $this->iStatutUtilisateur
					: STATUT_PERS_VISITEUR);
		else
			return $this->retReelStatutUtilisateur();
	}
	
	/**
	 * Retourne le statut "réel" de l'utilisateur, càd celui qui est enregistré pour la session dans la variable
	 * asInfosSession. C'est le statut qui est choisi par l'utilisateur, et qui est transmis de page en page. 
	 * Il se peut qu'on le change momentanément sur une page à l'aide de la variable iStatutUtilisateur, mais celui-ci 
	 * ne sera pas enregistré dans la session tant qu'il n'a pas été également transféré dans asInfosSession
	 * 
	 * @return	le statut de l'utilisateur
	 */
	function retReelStatutUtilisateur()
	{
		return $this->asInfosSession[SESSION_STATUT_UTILISATEUR];
	}
	
	/**
	 * Retourne le statut le plus élevé que l'utilisateur possède dans le contexte (session) actuel
	 * 
	 * @return	le statut le plus élevé pour l'utilisateur
	 */
	function retHautStatutUtilisateur()
	{
		return ($this->asInfosSession[SESSION_STATUT_ABSOLU] >= STATUT_PERS_PREMIER && $this->asInfosSession[SESSION_STATUT_ABSOLU] <= STATUT_PERS_DERNIER
			? $this->asInfosSession[SESSION_STATUT_ABSOLU]
			: STATUT_PERS_VISITEUR);
	}
	// }}}
	
	/**
	 * Retourne le nom de l'utilisateur connecté, ou visiteur/invité s'il est inconnu
	 * 
	 * @param	v_bStatutActuel	si \c true, utilise le statut actuel de l'utilisateur, càd la variable \c 
	 * 							iStatutUtilisateur, qui peut avoir changé par rapport au statut enregistré (cookie) 
	 * 							pour la session dans \c asInfosSession[SESSION_STATUT_UTILISATEUR], cette dernière 
	 * 							est utilisée si le paramètre est \c false
	 * 
	 * @return	le nom de l'utilisateur s'il est idientifié, sinon "visiteur/invité"
	 */
	function retTexteUtilisateur($v_bStatutActuel = TRUE)
	{
		if ($this->retIdUtilisateur() > 0)
			return $this->oUtilisateur->retNomComplet();
		else
			return (STATUT_PERS_VISITEUR == ($this->retStatutUtilisateur($v_bStatutActuel)) ? "Visiteur" : "Invité");
	}
	
	/**
	 * Retourne le terme correspondant à un statut, en tenant compte du sexe M/F
	 * 
	 * @param	v_iStatut	le statut pour lequel on veut le terme
	 * @param	v_sSexe		le genre à utiliser pour le terme
	 * 
	 * @return	le terme employé pour désigner le statut
	 */
	function retTexteStatutUtilisateur($v_iStatut = NULL, $v_sSexe = NULL)
	{
		if (empty($v_iStatut)) $v_iStatut = $this->retStatutUtilisateur();
		if (empty($v_sSexe)) $v_sSexe = (is_object($this->oUtilisateur) ? $this->oUtilisateur->retSexe() : "M");
		
		$sRequeteSql = "SELECT IF('{$v_sSexe}' = 'F',NomFemininStatut,NomMasculinStatut)"
			." FROM TypeStatutPers"
			." WHERE IdStatut='{$v_iStatut}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$sTexteStatut = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $sTexteStatut;
	}
	
	/**
	 * Retourne une liste des termes employés pour désigner les différents statuts de l'utilisateur connecté. 
	 * Cette liste est séparée par des &lt;BR&gt;
	 * 
	 * @return	la liste des statuts, sous forme textuelle
	 */
	function retTexteStatutsUtilisateur()
	{
		$asTexteStatuts = array();
		
		for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<=STATUT_PERS_DERNIER; $iIdxStatut++)
			if ($this->verifStatut($iIdxStatut))
				$asTexteStatuts[] = $this->retTexteStatutUtilisateur($iIdxStatut);
		
		if (count($asTexteStatuts) > 0)
			return implode(",<BR>",$asTexteStatuts);
		else
			return "[AUCUN STATUT]";
	}
	
	/**
	 * Insère dans la DB une nouvelle ressource (fichier) associée à une sous-activité.
	 * L'id utilisateur est celui de l'actuel connecté, et la sous-activité est celle qui est actuellement 
	 * initialisée (logiquement celle dans laquelle l'utilisateur se trouve)
	 * 
	 * @param	v_sNom		le nom donné à la ressource (fichier) à insérer
	 * @param	v_sDescr	la description de la ressource
	 * @param	v_sAuteur	l'auteur de la ressource, sous forme de texte (pas un id utilisateur)
	 * @param	v_sUrl		le chemin de la ressource
	 * 
	 * @return	\c true si la ressource a bien été insérée, \c false si l'utilisateur courant n'est pas identifié, 
	 * 			ou s'il ne se trouve pas dans une sous-activité (aucune initialisée), ou encore si un problème est 
	 * 			survenu à l'insertion SQL
	 */
	function insererRessource($v_sNom, $v_sDescr, $v_sAuteur, $v_sUrl)
	{
		$iIdDepos = NULL;
		$iIdSousActiv = NULL;
		
		if (isset($this->oUtilisateur) && is_object($this->oUtilisateur))
			$iIdDepos = $this->oUtilisateur->retId();
		
		if (isset($this->oSousActivCourante) && is_object($this->oSousActivCourante))
			$iIdSousActiv = $this->oSousActivCourante->retId();
		
		if ($iIdDepos < 1 || $iIdSousActiv < 1)
			return FALSE;
		
		$sRequeteSql = "LOCK TABLES Ressource WRITE"
			.", Ressource_SousActiv WRITE";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "INSERT INTO Ressource"
			." (IdRes,NomRes,DescrRes,DateRes,AuteurRes,UrlRes,IdPers,IdFormat)"
			." VALUES"
			." (null"
			.", '".mysql_escape_string($v_sNom)."'"
			.", '".mysql_escape_string($v_sDescr)."'"
			.", NOW()"
			.", '".mysql_escape_string($v_sAuteur)."'"
			.", '".mysql_escape_string($v_sUrl)."'"
			//.", '{$v_sAuteur'}"
			//.",\"$v_sUrl\""
			.", '{$iIdDepos}'"
			.", '1')";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (($iIdRes = $this->oBdd->retDernierId()) > 0)
		{
			$sRequeteSql = "INSERT INTO Ressource_SousActiv"
				." (IdResSousActiv, IdSousActiv, IdRes, StatutResSousActiv)"
				." VALUES"
				." (null,'{$iIdSousActiv}','{$iIdRes}','".STATUT_RES_EN_COURS."')";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return ($iIdRes > 0);
	}
	
	/**
	 * Retourne la colonne sur laquelle le tri est actuellement effectué, telle qu'enregistrée dans la session de 
	 * l'utilisateur (cookie). Cette fonctionnalité est par exemple utilisée pour l'affichage des collecticiels
	 * 
	 * @return	la colonne sur lequel le tri est effectué, sous forme de chaîne de caractères (par ex "date")
	 */
	function retTriCol()
	{
		return $this->asInfosSession[SESSION_TRI_COLONNE];
	}
	
	/**
	 * Retourne le sens (normal ou inversé) actuel du tri sur des colonnes. Cette fonctionnalité est par exemple 
	 * utilisée pour l'affichage des collecticiels.
	 * 
	 * @return	le sens du tri (voir constantes TRI_)
	 */
	function retTriDir()
	{
		return $this->asInfosSession[SESSION_TRI_DIRECTION];
	}
	
	/**
	 * Récupère les informations de la session actuelle de navigation de l'utilisateur. Cette lecture a lieu soit dans 
	 * le cookie, soit à partir des paramètres de l'url, les données passées par cette dernière ayant priorité. 
	 * Ces données sont par exemple le pseudo de l'utilisateur, le mot de passe associé, le statut actuel, les ids 
	 * des formation/module/rubrique/etc dans lesquels l'utilisateur se trouve actuellement
	 */
	function lireInfosSession()
	{
		$this->bIdParFormulaire = FALSE;
		
		for ($iIndexInfo=SESSION_DEBUT; $iIndexInfo<=SESSION_FIN; $iIndexInfo++)
			$this->asInfosSession[$iIndexInfo] = 0;
		
		// Un utilisateur vient de se logger
		if (isset($_POST["idPseudo"]))
		{
			$this->asInfosSession[SESSION_PSEUDO] = $_POST["idPseudo"];
			$this->asInfosSession[SESSION_MDP]    = $_POST["idMdp"];
			
			// Il faudra rechercher le statut le plus haut
			$this->iStatutUtilisateur = NULL;
			
			$this->bIdParFormulaire = TRUE;
		}
		else if (!empty($_COOKIE[$this->sNomCookie]))
		{
			$this->asInfosSession = explode(":", $_COOKIE[$this->sNomCookie]);
			
			if (!empty($this->asInfosSession[SESSION_STATUT_UTILISATEUR]))
				$this->iStatutUtilisateur = $this->asInfosSession[SESSION_STATUT_UTILISATEUR];
		}
		
		// si certaines infos sont passées en paramètre de l'URL, elles priment
		// sur celles contenues dans le cookie
		if (isset($_GET["idForm"]))
		{
			include_once(dir_database("evenement.tbl.php"));
			$oEvenDetail = new CEvenement_Detail($this->oBdd,$this->asInfosSession[SESSION_UID],$_GET["idForm"]);
			$oEvenDetail->entrerFormation();
			
			if (isset($this->asInfosSession[SESSION_FORM]) && 
				$this->asInfosSession[SESSION_FORM] != $_GET["idForm"])
					$oEvenDetail->sortirFormation($this->asInfosSession[SESSION_FORM]);
			
			unset($oEvenDetail);
			
			$this->asInfosSession[SESSION_FORM] = $_GET["idForm"];
		}
		
		if (isset($_GET["idMod"]))
			$this->asInfosSession[SESSION_MOD] = $_GET["idMod"];
		
		if (isset($_GET["idUnite"]))
			$this->asInfosSession[SESSION_UNITE] = $_GET["idUnite"];
		
		if (isset($_GET["idActiv"]))
			$this->asInfosSession[SESSION_ACTIV] = $_GET["idActiv"];
		
		if (isset($_GET["idSousActiv"]))
			$this->asInfosSession[SESSION_SOUSACTIV] = $_GET["idSousActiv"];
		
		if (isset($_GET["triCol"]))
			$this->asInfosSession[SESSION_TRI_COLONNE] = $_GET["triCol"];
		
		if (isset($_GET["triDir"]))
			$this->asInfosSession[SESSION_TRI_DIRECTION] = $_GET["triDir"];
		
		if (empty($this->asInfosSession[SESSION_TRI_COLONNE]))
			$this->asInfosSession[SESSION_TRI_COLONNE] = "date";
		
		if (empty($this->asInfosSession[SESSION_TRI_DIRECTION]))
			$this->asInfosSession[SESSION_TRI_DIRECTION] = 1;
	}
	
	/**
	 * Retourne une donnée parmi celles enregistrées pour la session utilisateur
	 * 
	 * @param	v_iNumSession	le numéro de la donnée de session à récupérer (voir les constantes SESSION_)
	 * 
	 * @return	la donnée provenant de la session
	 */
	function retInfosSession($v_iNumSession)
	{
		if ($v_iNumSession<SESSION_DEBUT || $v_iNumSession>SESSION_FIN)
			return -1;
		return $this->asInfosSession[$v_iNumSession];
	}
	
	/**
	 * Modifie une donnée de la session utilisateur, et réenregistre éventuellement la session (cookie)
	 * 
	 * @param	v_iNumSession		le numéro de la donnée de session à modifier (voir constantes SESSION_)
	 * @param	v_mValeurSession	la nouvelle valeur pour la donnée
	 * @param	v_bEnregistrer		si \c true, la session \e complète sera réenregistrée dans un cookie
	 * 
	 * @return	\c true si le numéro de la donnée de session à enregistrer était correct
	 * 
	 * @note	Si on décide d'enregistrer la session, étant donné que cela implique l'écriture d'un cookie, il faut 
	 * 			qu'aucune sortie HTML/PHP n'ai eu lieu dans la page; cette fonction doit donc être appelée avant tout 
	 * 			affichage
	 */
	function modifierInfosSession($v_iNumSession, $v_mValeurSession, $v_bEnregistrer = FALSE)
	{
		if ($v_iNumSession<SESSION_DEBUT || $v_iNumSession>SESSION_FIN)
			return FALSE;
		
		$this->asInfosSession[$v_iNumSession] = $v_mValeurSession;
		
		if ($v_bEnregistrer)
			$this->enregistrerInfosSession();
		
		return TRUE;
	}
	
	/**
	 * Enregistre les données de session actuelles de l'utilisateur dans un cookie
	 * 
	 * @note	L'écriture d'un cookie exige qu'aucune sortie HTML/PHP, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction
	 */
	function enregistrerInfosSession()
	{
		setcookie($this->sNomCookie, implode(":", $this->asInfosSession), 0, "/", "", 0);
	}
	
	/**
	 * Retourne l'id unique de la session utilisateur
	 * 
	 * @return	l'id de la session utilisateur
	 */
	function retNumeroUniqueSession()
	{
		return $this->asInfosSession[SESSION_UID];
	}
	
	/**
	 * Enregistre la totalité des données de la session utilisateur, dans leur état actuel, dans le cookie
	 * 
	 * @note	L'écriture d'un cookie exige qu'aucune sortie HTML/PHP, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction
	 */
	function ecrireInfosSession()
	{
		if (is_object($this->oUtilisateur))
		{
			$this->asInfosSession[SESSION_PSEUDO] = $this->oUtilisateur->retPseudo();
			$this->asInfosSession[SESSION_PRENOM] = $this->oUtilisateur->retPrenom();
			$this->asInfosSession[SESSION_NOM] = $this->oUtilisateur->retNom();
			$this->asInfosSession[SESSION_MDP] = $this->oUtilisateur->retMdp();
		}
		
		// Formation
		if (is_object($this->oFormationCourante))
			$this->asInfosSession[SESSION_FORM] = $this->oFormationCourante->retId();
		
		// Module/Cours
		if (is_object($this->oModuleCourant))
			$this->asInfosSession[SESSION_MOD] = $this->oModuleCourant->retId();
		
		// Rubrique/Unité
		if (is_object($this->oRubriqueCourante))
			$this->asInfosSession[SESSION_UNITE] = $this->oRubriqueCourante->retId();
		
		// Activité
		if (is_object($this->oActivCourante))
			$this->asInfosSession[SESSION_ACTIV] = $this->oActivCourante->retId();
		
		// Sous-activité
		if (is_object($this->oSousActivCourante))
			$this->asInfosSession[SESSION_SOUSACTIV] = $this->oSousActivCourante->retId();
		
		// Statut de l'utilisateur
		if (empty($this->asInfosSession[SESSION_STATUT_UTILISATEUR]))
			$this->asInfosSession[SESSION_STATUT_UTILISATEUR] = $this->iStatutUtilisateur;
		
		// ensuite, on inscrit la totalité du cookie
		$this->enregistrerInfosSession();
	}
	
	/**
	 * Efface complètement la session de l'utilisateur, ainsi que le cookie qui y est associé.
	 * Cela a pour effet l'annulation de son identification
	 * 
	 * @note	L'écriture d'un cookie exige qu'aucune sortie HTML/PHP, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction
	 */
	function effacerInfosSession()
	{
		$this->asInfosSession = array();
		
		for ($i = SESSION_DEBUT; $i < SESSION_FIN; $i++)
			$this->asInfosSession[$i] = 0;
		
		$this->oUtilisateur = NULL;
		$this->enregistrerInfosSession();
		$this->sNomCookie = NULL;
	}
	
	/**
	 * Affiche toutes les informations de la session utilisateur dans leur état actuel, ligne par ligne (fonction 
	 * utilitaire)
	 */
	function afficherInfosSession()
	{
		foreach ($this->asInfosSession as $sInfoSession)
			echo $sInfoSession."<br>";
	}
	
	/**
	 * Modifie le statut actuel de l'utilisateur
	 * 
	 * @param	v_iStatutUtilisateur	le numéro du statut à activer
	 * @param	v_bSauverDsCookie		si \c true, enregistre immédiatement les données \e complètes de la session 
	 * 									utilisateur, et donc le nouveau statut
	 * 
	 * @return	\c true si le numéro du statut demandé est valide
	 * 
	 * @note	L'écriture d'un cookie exige qu'aucune sortie HTML/PHP, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction
	 */
	function changerStatutUtilisateur($v_iStatutUtilisateur, $v_bSauverDsCookie = TRUE)
	{
		if ($v_iStatutUtilisateur < STATUT_PERS_PREMIER || $v_iStatutUtilisateur > STATUT_PERS_DERNIER)
			return FALSE;
		
		$this->iStatutUtilisateur = $v_iStatutUtilisateur;
		// $this->asInfosSession[SESSION_STATUT_ABSOLU] = $this->iStatutUtilisateur;
		$this->asInfosSession[SESSION_STATUT_UTILISATEUR] = $this->iStatutUtilisateur;
		
		if ($v_bSauverDsCookie)
			$this->ecrireInfosSession();
		
		return TRUE;
	}
	
	/**
	 * Retourne une version cryptée d'une chaîne de caractère (mot de passe)
	 * 
	 * @param	v_sMdp	la chaîne à crypter
	 * 
	 * @return	la version cryptée de la chaîne \p v_sMdp
	 * 
	 * @todo	Pour le moment, le cryptage se fait par la fonction \c PASSWORD() de MySQL, ce qui était malheureusement 
	 * 			une très mauvaise idée, car cette fonction ne devait être utilisée qu'en interne par MySQL, et a été 
	 * 			modifiée dans MySQL 4.1, ce qui rendra les mots de passe déjà encodés incompatible avec la nouvelle 
	 * 			version de la fonction
	 * 
	 * 			Idéalement, il faudrait donc crypter avec une fonction standard comme \c MD5() si on veut garder des 
	 * 			mots de passe indécryptables ou des fonctions comme <code>AES_CRYPT()/AES_DECRYPT()</code> si on veut 
	 * 			pouvoir récupérer le mot de passe à tout moment (ça éviterait le système tordu mis en place après coup, 
	 * 			qui consiste à écrire une version décryptée du mot de passe de chaque utilisateur dans le fichier 
	 * 			\c src/tmp/mdpncpte protégé contre la lecture, à chaque fois que quelqu'un se connecte et passe 
	 * 			le login
	 */
	function retMdpCrypte($v_sMdp)
	{
		$hResult = $this->oBdd->executerRequete("SELECT PASSWORD('{$v_sMdp}')");
		$oEnreg = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $oEnreg;
	}
	
	/**
	 * Ecrit un "événement" dans la DB, pour le moment il s'agit des connexions/déconnexions des utilisateurs
	 * 
	 * @param	v_iTypeEven		le numéro du type d'événement (voir constantes TYPE_EVEN_)
	 * @param	v_sDonneesEven	les données supplémentaires à associer à l'événement
	 */
	function ecrireEvenement($v_iTypeEven, $v_sDonneesEven = NULL)
	{
		if (isset($this->oUtilisateur))
			$iUtilisateur = $this->oUtilisateur->retId();
		else
			$iUtilisateur = "null";
		
		if (isset($v_sDonneesEven))
			$v_sDonneesEven = "'$v_sDonneesEven'";
		else
			$v_sDonneesEven = "null";
		
		$sIp = $_SERVER["REMOTE_ADDR"];
		
		if ($v_iTypeEven == TYPE_EVEN_DECONNEXION)
			$sRequeteSql = "UPDATE Evenement"
				." SET SortiMomentEven=NOW()"
				." WHERE IdEven='{$this->asInfosSession[SESSION_UID]}'";
		else
			$sRequeteSql = "INSERT INTO Evenement"
				." (IdEven,IdTypeEven,IdPers,MomentEven,IpEven,MachineEven,DonneesEven)" 
				." VALUES"
				." (null,$v_iTypeEven,$iUtilisateur,NOW(),'$sIp','".gethostbyaddr($sIp)."',$v_sDonneesEven)";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		// Mettre à jour le cookie
		if ($v_iTypeEven == TYPE_EVEN_LOGIN_REUSSI)
		{
			$this->asInfosSession[SESSION_UID] = $this->oBdd->retDernierId();
			$this->enregistrerInfosSession();
		}
	}
	
	/**
	 * Retourne le nom du projet
	 * 
	 * @return	le nom du projet, tel qu'enregistré dans la DB
	 */
	function retNom()
	{
		return $this->sNom;
	}
	
	/**
	 * Retourne l'adresse e-mail de contact du projet
	 * 
	 * @return	l'adresse e-mail
	 */
	function retEmail()
	{
		return $this->sEmail;
	}
	
	/**
	 * Retourne le port TCP sur lequel est lancé le serveur de chat utilisé par les clients de la plate-forme
	 * 
	 * @return	le port TCP associé au serveur chat
	 */
	function retNumPortChat()
	{
		return $this->iNumPortChat;
	}
	
	/**
	 * Retourne le port TCP sur lequel est lancé le serveur d'awareness utilisé par les clients de la plate-forme
	 * 
	 * @return	le port TCP associé au serveur d'awareness (JCVD-style :-D )
	 */
	function retNumPortAwareness()
	{
		return $this->iNumPortAwareness;
	}
	
	/**
	 * Retourne la langue de l'utilisateur connecté
	 * 
	 * @return	un code qui représente la langue actuelle de l'utilisateur
	 * 
	 * @deprecated	Cette fonction ne semble pas utilisée pour le moment, et sera remplacée par le système \c gettext()
	 */
	function retLanguage()
	{
		return (empty($this->asInfosSession[SESSION_LANG]) ? "fr" : $this->asInfosSession[SESSION_LANG]);
	}
	
	/**
	 * Retourne le chemin du répertoire qui abrite les fichiers de la formation actuelle (initialisée)
	 * 
	 * @param	v_sFichierInclure	le nom d'un éventuel fichier qui fera alors partie du chemin retourné
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourné sera absolu. Si \c false, il sera relatif
	 * 
	 * @return	le chemin vers le répertoire de la formation courante
	 */
	function dir_formation($v_sFichierInclure = NULL, $v_bCheminAbsolu = FALSE)
	{
		return dir_formation($this->oFormationCourante->retId(), $v_sFichierInclure, $v_bCheminAbsolu);
	}
	
	/**
	 * Retourne le chemin du répertoire qui abrite les fichiers de l'activité courante
	 * 
	 * @param	v_sFichierInclure	le nom d'un éventuel fichier qui fera alors partie du chemin retourné
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourné sera absolu. Si \c false, il sera relatif
	 * 
	 * @return	le chemin vers le répertoire de l'activité courante
	 */
	function dir_cours($v_sFichierInclure = NULL, $v_bCheminAbsolu = FALSE)
	{
		$sUrlCours = NULL;
		
		if (isset($this->oFormationCourante) &&
			is_object($this->oFormationCourante) &&
			($iIdForm = $this->oFormationCourante->retId()) > 0)
		{
			if (isset($this->asInfosSession[SESSION_ACTIV]) &&
				$this->asInfosSession[SESSION_ACTIV] > 0)
				$iIdActiv = $this->asInfosSession[SESSION_ACTIV];
			else if (isset($this->oActivCourante) &&
				is_object($this->oActivCourante))
				$iIdActiv = $this->oActivCourante->retId();
			else
				$iIdActiv = 0;
			
			if ($iIdActiv > 0)
				$sUrlCours = dir_cours($iIdActiv, $iIdForm, $v_sFichierInclure, $v_bCheminAbsolu);
		}
		
		return $sUrlCours;
	}
	
	/**
	 * Retourne le chemin du répertoire images de l'activité courante, relatif à la racine de la plate-forme
	 * 
	 * @param	v_sFichierInclure	le nom d'un éventuel fichier qui fera alors partie du chemin retourné
	 * 
	 * @return	le chemin vers le répertoire images pour l'activité courante
	 */
	function dir_images($v_sFichierInclure = NULL)
	{
		return $this->dir_cours()."images/{$v_sFichierInclure}";
	}
	
	/**
	 * Retourne le chemin du répertoire ressources de l'activité courante
	 * 
	 * @param	v_sFichierInclure	le nom d'un éventuel fichier qui fera alors partie du chemin retourné
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourné sera absolu. Si \c false, il sera relatif
	 * 
	 * @return	le chemin vers le répertoire ressources pour l'activité courante
	 */
	function dir_ressources($v_sFichierInclure = NULL, $v_bCheminAbsolu = TRUE)
	{
		return $this->dir_cours(NULL, $v_bCheminAbsolu)."ressources/{$v_sFichierInclure}";
	}
	
	/**
	 * Retourne le chemin du répertoire rubriques de la formation courante, relatif à la racine de la plate-forme
	 * 
	 * @param	v_sFichierInclure	le nom d'un éventuel fichier qui fera alors partie du chemin retourné
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourné sera absolu. Si \c false, il sera relatif
	 * 
	 * @return	le chemin vers le répertoire rubriques pour la formation courante
	 */
	function retRepRubriques($v_sFichierInclure = NULL, $v_bCheminAbsolu = FALSE)
	{
		return $this->dir_formation("rubriques/{$v_sFichierInclure}", $v_bCheminAbsolu);
	}
	
	/**
	 * Vérifie qu'un utilisateur a le statut de tuteur
	 * 
	 * @param	v_iIdPers	l'id de l'utilisateur. S'il est absent, l'utilisateur connecté sera pris pour la 
	 * 						vérification
	 * 
	 * @return	\c true si l'utilisateur est tuteur, \c false si ce n'est pas le cas \e ou que l'utilisateur à vérifier 
	 * 			est invalide
	 */
	function verifTuteur($v_iIdPers = 0)
	{		
		$bEstTuteur = FALSE;
		
		if (empty($v_iIdPers) && is_object($this->oUtilisateur))
			$v_iIdPers = $this->oUtilisateur->retId();
			
		if ($v_iIdPers < 1)
			return $bEstTuteur;
				
		$sRequeteSql = "SELECT * FROM Module_Tuteur"
			." WHERE IdPers='{$v_iIdPers}'"
			." LIMIT 1";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregSuiv($hResult))
	   		$bEstTuteur = TRUE;
		
		$this->oBdd->libererResult($hResult);
		
		return $bEstTuteur;
	}
	
	/**
	 * Retourne le code HTML nécessaire à la représentation d'un lien, en fonction de différents paramètres
	 * 
	 * @param	v_sLien			l'url du lien
	 * @param	v_sIntitule		le texte à utiliser pour afficher le lien
	 * @param	v_iMode			le mode d'affichage de l'url lorsque le lien sera cliqué (voir constantes)
	 * @param	v_sInfoBulle	le texte à afficher dans l'infobulle du lien (attribut "title"). Si \p v_iMode vaut
	 * 							MODE_LIEN_TELECHARGER, l'infobulle sera automatiquement un message indiquant qu'il 
	 * 							s'agit d'un téléchargement
	 * 
	 * @return	le code HTML pour créer le lien demandé
	 */
	function retLien($v_sLien = NULL, $v_sIntitule = NULL, $v_iMode = NULL, $v_sInfoBulle = NULL)
	{
		$sCheminAbsolu = dir_document_root();
		
		if (empty($v_sLien) && empty($v_sIntitule))
			return "<b>Pas de lien attribué</b>";
		
		$err = FALSE;
		
		// si l'intitulé du lien n'est pas fourni, on utilise le lien lui-même
		// à la place
		if (empty($v_sIntitule))
			$v_sIntitule = rawurldecode($v_sLien);
		
		// le "mode" du lien spécifie si celui-ci passera par le "filtre" de
		// téléchargement, s'il sera ouvert dans la fenêtre principale, ou 
		// dans une nouvelle fenêtre
		$ok = TRUE;
		$sParamLien = NULL;
		$sIcone = NULL;
		
		switch ($v_iMode)
		{
			case FRAME_CENTRALE_INDIRECT:
			case NOUVELLE_FENETRE_DIRECT:
			case NOUVELLE_FENETRE_INDIRECT:
				
				if (!eregi("^http://",$v_sLien))
				{
					$v_sLien = $this->dir_cours(NULL,FALSE).$v_sLien;
					$ok = is_file($sCheminAbsolu.rawurldecode($v_sLien));
				}
				
				if ($v_iMode == FRAME_CENTRALE_INDIRECT)
					$sParamBalise = " target=\"Principal\" title=\"{$v_sInfoBulle}\"";
				else
					$sParamBalise = " target=\"_blank\"";
				
				$sIcone = "<img src=\"".dir_theme("lien.gif")."\" border=\"0\">&nbsp;";
				break;
				
			case MODE_LIEN_TELECHARGER:
				
				// *************************************
				// le nom du fichier à télécharger est passé en URL, donc il doit être
				// encodé pour éviter les erreurs avec les caractères spéciaux
				// *************************************
				
				if (empty($v_sLien))
					$v_sLien = NULL;
				else				
					$v_sLien = $this->dir_cours(NULL,FALSE).rawurlencode($v_sLien);
				
				if (($ok = is_file($sCheminAbsolu.rawurldecode($v_sLien))))
				{
					if (empty($v_sIntitule))
						$v_sIntitule = rawurldecode($v_sLien);
					
					$sParamLien = dir_lib("download.php?f=");
					
					$sParamBalise = " target=\"_self\"";
					$sIcone = "<img src=\"".dir_theme("disquette.gif")."\" border=\"0\">&nbsp;";
					$v_sInfoBulle = "T&eacute;l&eacute;chargez ce document";
				}
				
				break;
		}
			
		if ($ok)
			$r_sBalise = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n"
				."<tr><td>{$sIcone}&nbsp;</td>"
				."<td>"
				."<a href=\"{$sParamLien}{$v_sLien}\"{$sParamBalise}"
				." onfocus=\"blur()\""
				.(isset($v_sInfoBulle) ? " title=\"{$v_sInfoBulle}\"" : NULL)
				.">".mb_convert_encoding($v_sIntitule,"HTML-ENTITIES","UTF-8")."</a>"
				."</td></tr>\n"
				."</table>\n";
		else
			$r_sBalise = "&nbsp;&nbsp;<b>Ce document n'a pas &eacute;t&eacute; trouv&eacute; sur le serveur<br>"
				."[&nbsp;<span style=\"font-weight: normal;\">".basename(rawurldecode ($v_sLien))."</span>&nbsp;]"
				."</b>";
		
		return $r_sBalise;
	}
	
	
	/**
	 * Affiche un message de débuggage
	 * 
	 * @param	v_sMessage		le message à afficher
	 * @param	v_iNumLigne		le numéro de ligne concerné (utiliser la constante magique PHP \c __LINE__ à l'appel)
	 * @param	v_sNomFichier	le fichier dans lequel on se trouve au moment du message (utiliser la constante magique 
	 * 							PHP \c __FILE__ à l'appel)
	 */
	function debug($v_sMessage , $v_iNumLigne = NULL, $v_sNomFichier = NULL)
	{
		echo " [:DEBUG"
			.(($v_iNumLigne !== NULL) ? " - L'{$v_iNumLigne}'" : NULL)
			.(($v_sNomFichier !== NULL) ? " - F'{$v_sNomFichier}'" : NULL)
			.": {$v_sMessage}"
			."]<br>\n";
	}
	
	/**
	 * Retourne un tableau multi-dimensionnel contenant, pour chaque indice de premier niveau, les infos sur un statut 
	 * sous forme d'indices textuels \c IdStatut (l'id dans la DB), \c NomStatut (statut sous forme texte, au masculin)
	 * et \c TxtStatut (le nom de la constante représentant le statut)
	 * 
	 * @return	un tableau contenant les infos sur les types de statuts
	 */
	function retListeStatut()
	{
		$sRequeteSql = "SELECT * FROM TypeStatutPers"
			." ORDER BY IdStatut ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ( $oEnreg = $this->oBdd->retEnregSuiv($hResult) )
			$aListeStatut[] = array(
				"IdStatut" => $oEnreg->IdStatut,
				"NomStatut" => $oEnreg->NomMasculinStatut,
				"TxtStatut" => $oEnreg->TxtStatut);
		
		$this->oBdd->libererResult($hResult);
		
		return $aListeStatut;
	}
	
	/**
	 * Initialise toutes les permissions de la personne par rapport à son statut actuel
	 * 
	 * @param	v_bStatutActuel	si \c true, utilise le statut actuel de l'utilisateur, càd la variable \c 
	 * 							iStatutUtilisateur, qui peut avoir changé par rapport au statut enregistré (cookie) 
	 * 							pour la session dans \c asInfosSession[SESSION_STATUT_UTILISATEUR], cette dernière 
	 * 							est utilisée si le paramètre est \c false.
	 */
	function initPermisUtilisateur($v_bStatutActuel = TRUE)
	{
		$this->oPermisUtilisateur = new CStatutPermission($this->oBdd);
		$this->oPermisUtilisateur->initPermissions($this->retStatutUtilisateur($v_bStatutActuel));
	}
	
	/**
	 * Vérifie que l'utilisateur connecté dispose d'une permission particulière
	 * 
	 * @param	v_sNomPermis	la constante représentant la permission à vérifier, mais <b>sous forme de chaîne</b>
	 * 
	 * @return	\c true si l'utilisateur connecté dispose de la permission
	 */
	function verifPermission($v_sNomPermis)
	{
		if (isset($this->oPermisUtilisateur) && is_object($this->oPermisUtilisateur))
			return $this->oPermisUtilisateur->verifPermission($v_sNomPermis);
		else
			return FALSE;
	}
	
	/**
	 * Crée un fichier de définitions PHP représentant les statuts possibles pour les utilisateurs. Ce fichier doit 
	 * être recréee lorsqu'on ajoute de nouveaux statuts, ce qui est rare. Par contre, on peut ajouter des permissions 
	 * sans pour autant devoir régénérer ce fichier.
	 * 
	 * @see	include/def/statut.def.php
	 */
	function creerFichierStatut()
	{
		$aListeStatut = $this->retListeStatut();
		
		// ouverture du fichier en écriture
		if ($hFichier = fopen(dir_definition("statut.def.php"), "w+"))
		{
			// écriture balise PHP début + 'define's + balise PHP fin, puis fermeture
			fputs($hFichier,"<?php\n\n");
			fputs($hFichier,sprintf(_("// Ce fichier a été généré (%s) automatiquement par la plate-forme\n\n"),date ("d M Y")));
			fputs($hFichier,"define(\"STATUT_POTENTIEL\", 1000);\n\n");
			
			$aFirst = reset($aListeStatut);
			
			fputs($hFichier, "define(\"STATUT_PERS_PREMIER\", ".$aFirst["IdStatut"].");\n");
			
			foreach ($aListeStatut as $aStatut)
				fputs($hFichier, "define(\"".$aStatut["TxtStatut"]."\", ".$aStatut["IdStatut"].");\n");
			
			$aLast = end($aListeStatut);
			
			fputs($hFichier, "define(\"STATUT_PERS_DERNIER\", ".$aLast["IdStatut"].");\n");
			fputs($hFichier, "\n?>\n");
			fclose($hFichier);
		}
	}
	
	/**
	 * Vérifie que l'utilisateur a le droit d'utiliser les différents outils d'administration. Si l'utilisateur n'est 
	 * pas identifié, est visiteur, ou n'a pas la permission requise, une page blanche est affichée
	 * 
	 * @param	v_sNomPermission	la constante représentant la permission d'utiliser l'outil, mais <b>sous forme de 
	 * 								chaîne</b>
	 * 
	 * @todo	Vérifier que \c bPeutUtiliserOutils ne devrait pas plutôt valoir \c false au cas où la constante passée 
	 * 			en paramètre n'existe pas
	 */
	function verifPeutUtiliserOutils($v_sNomPermission = NULL)
	{
		$bPeutUtiliserOutils = (isset($v_sNomPermission) ? $this->verifPermission($v_sNomPermission) : TRUE);
		
		if (!$bPeutUtiliserOutils ||
			!is_object($this->oUtilisateur) ||
			$this->retStatutUtilisateur() >= STATUT_PERS_ETUDIANT)
		{
			// Cette utilisateur ne peut pas utiliser cet outil
			header("Location: ".dir_root_plateform("blank.php", FALSE));
			exit();
		}
	}
	
	/**
	 * Initialise un tableau d'objets CPersonne (\c aoPersonnes) selon des critères de statut
	 * 
	 * @param	v_iIdStatutPers	la constante rerpésentant le statut désiré
	 * @param	v_iIdForm		l'id de la session de formation à croiser avec le statut
	 * @param	v_iIdMod		l'id du module à croiser avec le statut, n'est pas requis pour certains statuts
	 * 
	 * @return	le nombre de personnes trouvées
	 */
	function initPersonnes($v_iIdStatutPers = NULL, $v_iIdForm = 0, $v_iIdMod = 0)
	{
		$iIdxPers = 0;
		$this->aoPersonnes = array();
		
		switch ($v_iIdStatutPers)
		{
			case STATUT_PERS_RESPONSABLE:
			//   -----------------------
				$sRequeteSql = "SELECT p.*"
					." FROM ".($v_iIdForm > 0
						? "Formation_Resp AS fr"
						: " Projet_Resp AS pr")
					." LEFT JOIN Personne AS p USING (IdPers)"
					.($v_iIdForm > 0 ? " WHERE fr.IdForm='{$v_iIdForm}'" : NULL)
					." GROUP BY p.IdPers";
				break;
				
			case STATUT_PERS_CONCEPTEUR:
			//   ----------------------
				$sRequeteSql = "SELECT p.*"
					." FROM ".($v_iIdMod > 0 ? "Module_Concepteur AS mc"
						: ($v_iIdForm > 0
							? "Formation_Concepteur AS fc"
							: "Projet_Concepteur AS pc"))
					." LEFT JOIN Personne AS p USING (IdPers)"
					.($v_iIdMod > 0
						? " WHERE mc.IdMod='{$v_iIdMod}'"
						: ($v_iIdForm > 0
							? " WHERE fc.IdForm='{$v_iIdForm}'"
							: NULL))
					." GROUP BY p.IdPers";
				break;
				
			case STATUT_PERS_TUTEUR:
			//   ------------------
				$sRequeteSql = "SELECT p.*"
					." FROM ".($v_iIdMod > 0 ? "Module_Tuteur AS mt" : "Formation_Tuteur AS ft")
					." LEFT JOIN Personne AS p USING (IdPers)"
					." WHERE"
					.($v_iIdMod > 0
						? " mt.IdMod='{$v_iIdMod}'"
						: ($v_iIdForm > 0
							? " ft.IdForm='{$v_iIdForm}'"
							: " ft.IdForm IS NOT NULL"))
					." GROUP BY p.IdPers";
				break;
				
			case STATUT_PERS_ETUDIANT:
			//   --------------------
				$sRequeteSql = "SELECT p.*"
					." FROM ".($v_iIdMod > 0 ? "Module_Inscrit AS mi" : "Formation_Inscrit AS fi")
					." LEFT JOIN Personne AS p USING (IdPers)"
					." WHERE"
					.($v_iIdMod > 0
						? " mi.IdMod='{$v_iIdMod}'"
						: ($v_iIdForm > 0
							? " fi.IdForm='{$v_iIdForm}'"
							: " fi.IdForm IS NOT NULL"))
					." GROUP BY p.IdPers";
				break;
				
			default:
				if ($v_iIdForm > 0)
					$sRequeteSql = "SELECT p.*"
						." FROM Formation AS f, Personne AS p"
						." LEFT JOIN Formation_Resp AS fr ON f.IdForm=fr.IdForm AND p.IdPers=fr.IdPers"
						." LEFT JOIN Formation_Concepteur AS fc ON f.IdForm=fc.IdForm AND p.IdPers=fc.IdPers"
						." LEFT JOIN Formation_Tuteur AS ft ON f.IdForm=ft.IdForm AND p.IdPers=ft.IdPers"
						." LEFT JOIN Formation_Inscrit AS fi ON f.IdForm=fi.IdForm AND p.IdPers=fi.IdPers"
						." WHERE f.IdForm='{$v_iIdForm}'"
						." AND (fr.IdPers IS NOT NULL"
						." OR fc.IdPers IS NOT NULL"
						." OR ft.IdPers IS NOT NULL"
						." OR fi.IdPers IS NOT NULL)"
						." GROUP BY p.IdPers";
				else
					$sRequeteSql = "SELECT p.* FROM Personne AS p";
		}
		
		$sRequeteSql .= " ORDER BY p.Nom, p.Prenom ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoPersonnes[$iIdxPers] = new CPersonne($this->oBdd);
				$this->aoPersonnes[$iIdxPers]->init($oEnreg);
				$iIdxPers++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxPers;
	}
}

/**
 * Retourne un tableau de chaînes de caractères représentant la version textuelle d'un statut ouvert/fermé/etc pour les 
 * éléments des formations
 * 
 * @param	v_sGenre	la chaîne \c F pour obtenir la version au féminin du mot, \c M pour la version au masculin
 * 
 * @return	le tableau contenant les versions texte des statuts, avec pour indices les constantes STATUT_ correspondantes
 */
function retListeStatuts($v_sGenre = "F")
{
	if (strtoupper($v_sGenre) == "M")
		return array(
			array(STATUT_FERME,_("Fermé")),
			array(STATUT_OUVERT,_("Ouvert")),
			array(STATUT_INVISIBLE,_("Invisible")),
			array(STATUT_ARCHIVE,_("Archivé")));
	else
		return array(
			array(STATUT_FERME,_("Fermée")),
			array(STATUT_OUVERT,_("Ouverte")),
			array(STATUT_INVISIBLE,_("Invisible")),
			array(STATUT_ARCHIVE,_("Archivé")));
}

?>
