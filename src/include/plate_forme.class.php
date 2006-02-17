<?php

/*
** Fichier ................: plate_forme.class.php
** Description ............:
** Date de création .......: 06/09/2001
** Dernière modification ..: 30/11/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**                           Ludovic FLAMME
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// *************************************
// Les n° 1 à 3 des constantes 'LOGIN' sont dans la base, dans les tables
// 'TxtErreursxxx', qui ont été traduites en un fichier
// 'include/constantes.def.php' à l'aidede la classe 'CConstantes' définie
// dans ce fichier
// *************************************
define("LOGIN_OK",0);
define("LOGIN_MDP_INCORRECT",8);
define("LOGIN_PAS_ENCORE_ID",9);
define("LOGIN_PERSONNE_INCONNUE",10);

// ---------------------
// Déclaration de constantes des cookies
// ---------------------
define("SESSION_DEBUT",0);
define("SESSION_PSEUDO",0);						// Pseudo de la personne
define("SESSION_NOM",1);						// Nom de la personne
define("SESSION_PRENOM",2);						// Prénom de la personne
define("SESSION_MDP",3);						// Mot de passe de la personne
define("SESSION_STATUT_ABSOLU",4);				// Statut de l'utilisateur le plus important
define("SESSION_STATUT_UTILISATEUR",5);			// Statut que l'utilisateur a choisit
define("SESSION_FORM",6);						// Numéro de la formation courante
define("SESSION_MOD",7);						// Numéro du module/cours courant
define("SESSION_UNITE",8);						// Numéro de l'unité courante (plus utilisé)
define("SESSION_ACTIV",9);						// Numéro de l'activité
define("SESSION_SOUSACTIV",10);					// Numéro de la sous-activité
define("SESSION_TRI_COLONNE",11);
define("SESSION_TRI_DIRECTION",12);
define("SESSION_UID",13);						// Numéro ID unique donné par la table 'Evenement'
define("SESSION_DOSSIER_FORMS",14);				// Numéro du dossier de formations
define("SESSION_LANG",15);						// Langue de l'interface de l'utilisateur
define("SESSION_FIN",14);

// ---------------------
// Déclaration de constantes de l'états du loggin
// ---------------------
define("TYPE_EVEN_LOGIN_RATE",1);
define("TYPE_EVEN_LOGIN_REUSSI",2);
define("TYPE_EVEN_DECONNEXION",3);

// ---------------------
// Déclaration de constantes qui définissent le fonctionnement d'un lien
// ---------------------
define("LIEN_PAGE_HTML",1);
define("LIEN_DOCUMENT_TELECHARGER",2);
define("LIEN_SITE_INTERNET",3);
define("LIEN_CHAT",4);
define("LIEN_FORUM",5);
define("LIEN_GALERIE",6);
define("LIEN_COLLECTICIEL",7);
define("LIEN_UNITE",8);
define("LIEN_FORMULAIRE",9);
define("LIEN_TEXTE_FORMATTE",10);
define("LIEN_GLOSSAIRE",11);
define("LIEN_TABLEAU_DE_BORD",12);

// ---------------------
// ---------------------
define("FRAME_CENTRALE_DIRECT",1);
define("FRAME_CENTRALE_INDIRECT",2);
define("NOUVELLE_FENETRE_DIRECT",3);
define("NOUVELLE_FENETRE_INDIRECT",4);
define("MODE_LIEN_TELECHARGER",5);

// ---------------------
// Déclaration de constantes qui définit les différents types
// ---------------------
define("TYPE_INCONNU",0);
define("TYPE_FORMATION",1);
define("TYPE_MODULE",2);
define("TYPE_RUBRIQUE",3);
define("TYPE_UNITE",4);
define("TYPE_ACTIVITE",5);
define("TYPE_SOUS_ACTIVITE",6);

define("STATUT_FERME",1);						// Le lien est visible mais pas accessible
define("STATUT_OUVERT",2);						// Le lien est visible et accessible
define("STATUT_INVISIBLE",3);					// Le lien n'est pas affiché
define("STATUT_ARCHIVE",4);
define("STATUT_EFFACE",5);						// Effacement logique des enregistrements
define("STATUT_IDEM_PARENT",6);
define("STATUT_LECTURE_SEULE",7);				// Le lien est visible, cliquable mais nous ne pouvons pas modifier quoique ce soit

//define("STATUT_USER",3);

// ---------------------
// Modalité
// ---------------------
define("MODALITE_IDEM_PARENT",0);
define("MODALITE_INDIVIDUEL",1);
define("MODALITE_PAR_EQUIPE",2);				// (isolée)         ==> Les équipes ne voient pas les autres équipes
define("MODALITE_POUR_TOUS",3);
define("MODALITE_PAR_EQUIPE_INTERCONNECTEE",4);	// (interconnectée) ==> Les équipes voient les autres équipes mais ne peuvent pas collaborer entre-eux
define("MODALITE_PAR_EQUIPE_COLLABORANTE",5);	// (collaborante)   ==> Les équipes voient les autres équipes et peuvent collaborer

// ---------------------
// "Objets" de formulaire
// ---------------------
define("OBJFORM_QTEXTELONG", 1);
define("OBJFORM_QTEXTECOURT", 2);
define("OBJFORM_QNOMBRE", 3);
define("OBJFORM_QLISTEDEROUL", 4);
define("OBJFORM_QRADIO", 5);
define("OBJFORM_QCOCHER", 6);
define("OBJFORM_MPTEXTE", 7);
define("OBJFORM_MPSEPARATEUR", 8);

// ---------------------
// Utiliser dans les formulaires, lorsqu'un tuteur décide que le document de
// l'étudiant est soumis automatiquement ou pas au tuteur
// ---------------------
define("SOUMISSION_MANUELLE",0);
define("SOUMISSION_AUTOMATIQUE",1);

// ---------------------
// Tri
// ---------------------
define("PAS_TRI",0);
define("TRI_CROISSANT",1);
define("TRI_DECROISSANT",2);

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
 * Classe permettant de récupérer des constantes 'texte' (consignes, messages...)
 * dans la base de données.
 *
 * @class CConstantes
 * @author Cédric FLOQUET <cedric.floquet@umh.ac.be>
 */
class CConstantes
{
	var $oBdd;				// nom de la base à utiliser
	var $sTable;			// Nom de la table (i18n_fr) qui contient toutes les traductions pour la plate-forme
	var $sTableI18N;		// Nom de la table qui contient toutes les déclarations des constantes
	
	function CConstantes (&$v_oBdd,$v_sTable)
	{
		$this->oBdd = &$v_oBdd;
		$this->sTable = $v_sTable;
		$this->sTableI18N = "i18n";
	}
	
	function retTexte ($v_iId,$v_bConversionHtml=TRUE)
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
				return htmlentities($r_Enreg->ContenuTxt);
			else
				return $r_Enreg->ContenuTxt;
		}
		else
			return "[$v_iId]";
	}
	
	function creerFichierConstantes ($v_sNomFichier=NULL)
	{
		// si le nom du fichier à créer n'est pas spécifié, on prend le nom
		// de la table
		if (!$v_sNomFichier)
			$v_sNomFichier = dir_definition("{$this->sTable}.def.php");
		
		// récupération de TOUS les enregs de la table contenant les noms des constantes
		$hResult = $this->oBdd->executerRequete("SELECT * FROM {$this->sTableI18N} ORDER BY ConstTxt");
		
		if ($this->oBdd->retNbEnregsDsResult($hResult))
		{
			if ($hFichier = fopen($v_sNomFichier, "w+"))
			{
				// écriture balise php début + 'define's + balise php fin, puis fermeture
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
 * Classe principale
 *
 * @class CProjet
 * @see CPersonne
 * @see CFormation
 */
class CProjet
{
	var $sCheminWeb;			/** Chemin du projet à partir de la racine du serveur web */
	var $sCheminComplet;		// chemin complet du projet sur le système de fichiers
	var $sNomRep;				// uniquement le nom du répertoire du projet
	//var $sCheminDocs;			// chemin (relatif) du répertoire de stockage des documents
	var $sNomCookie;			// nom du cookie associé au projet
	var $asInfosSession;		// contenu du cookie associé au projet
	var $bIdParFormulaire;		// les infos utilisateurs ont-elles été transmises par
								// formulaire ? Sinon, c'est par cookie
	var $oBdd;					// interface vers la base de données du projet
	var $sNom;					// nom complet du projet
	var $sUrlAccueil;			// URL complète de la page d'accueil du projet
	var $sUrlLogin;				// URL de la page permettant de s'identifier
	var $oErreurs;				// :DEBUG: pour tester la classe CConstantes
	var $sSuffixeTxt = "_PDH"; 	// Normalement ça sera une propriété de FORMATION
	var $aoAdmins;				// tableau contenant les administrateurs de la plate-forme
	var $oUtilisateur;			// personne actuellement connectée	
	var $oEquipe;				// équipe à laquelle cette personne appartient (si applicable)
	var $aoFormations;
	var $aoInscrits;			// pointeur vers les inscrits à la formation courante
	
	var $oFormationCourante;	// formation courante pendant la navigation
	var $oModuleCourant;		// pointeur vers le module courant de la formation courante
	var $oRubriqueCourante;
	var $oActivCourante;		// pointeur vers l'activité courante
	var $oSousActivCourante;	// pointeur vers la sous-activité courante
	
	var $aoPersonnes;
	var $aoEquipes;				// pointeur vers les équipes de l'activité courante
	var $abStatutsUtilisateur;	// statuts de l'utilisateur connecté dans le contexte actuel
								// (administrateur du projet, tuteur du module, etc...)
	var $iStatutUtilisateur;	// parmi les statuts possibles, lequel est utilisé ?
	var $oPermisUtilisateur;	// permission par rapport au statut courant
	var $iCodeEtat;
	
	var $oI18N;
	
	function CProjet ($v_bEffacerCookie=FALSE,$v_bRedirigerSiIncorrect=FALSE)
	{
		global $HTTP_SERVER_VARS;
		global $g_sNomCookie;
		global $g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd;
		
		// init 'simples' des propriétés, càd sans accès à la bdd
		$this->sCheminWeb     = str_replace('\\', '/', dirname($HTTP_SERVER_VARS["PHP_SELF"]));
		$this->sCheminComplet = $HTTP_SERVER_VARS["DOCUMENT_ROOT"].$this->sCheminWeb;
		$this->sNomRep        = $g_sNomBdd;
		$this->sUrlLogin      = "http://".$HTTP_SERVER_VARS["HTTP_HOST"].$this->sCheminWeb."/"."login-index.php";
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
	
	function terminer ()
	{
		if (isset($this->oBdd))
			$this->oBdd->terminer();
	}
	
	/**
	 * Cette fonction récupére les informations du projet,
	 * c'est-à-dire, le nom du projet ou le numéro du port pour les chats.
	 */
	function init ()
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
	
	function initAdministrateurs ()
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
	
	function initUtilisateur ($v_bRedirigerSiIncorrect=FALSE)
	{
		global $HTTP_SERVER_VARS, $HTTP_COOKIE_VARS;
		
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
		
		// s'il y a un problème avec l'identification de la personne, on
		// arrête le chargement de la page et on la renvoie à l'écran de
		// login (grâce à une redirection dans l'en-tête HTTP, ce qui
		// signifie que tout objet de cette classe doit être créé avant
		// d'écrire quoi que ce soit dans le page HTML)
		if ($this->iCodeEtat != LOGIN_OK)
		{
			if ($v_bRedirigerSiIncorrect
				|| $this->iCodeEtat != LOGIN_PAS_ENCORE_ID)
				$this->redirigerVersLogin($sTmpPrenom,$sTmpNom);
		}
		else if ($this->bIdParFormulaire)
		{
			// si ID ok, on inscrit le login dans la table Evenement, mais seulement la 1ère
			// fois (donc quand on vient de l'écran login, donc quand les infos proviennent 
			// du formulaire)
			$this->ecrireEvenement(TYPE_EVEN_LOGIN_REUSSI,$HTTP_SERVER_VARS["HTTP_USER_AGENT"]);
		}
	}
	
	function retIdUtilisateur () { return (isset($this->oUtilisateur) && is_object($this->oUtilisateur) ? $this->oUtilisateur->retId() : 0); }
	
	/**
	 * Initialise les statuts de l'utilisateur
	 *
	 * @param $v_bVerifierStatutForm boolean Si ce paramètre est à TRUE, nous devrons donc rechercher les
	 *        statuts par rapport à la formation actuelle.
	 */
	function initStatutsUtilisateur ($v_bVerifierStatutForm=TRUE)
	{
		$iIdPers = $this->retIdUtilisateur();
		
		$this->initModuleCourant();
		
		$iIdForm = ($v_bVerifierStatutForm && isset($this->oFormationCourante) && is_object($this->oFormationCourante) ? $this->oFormationCourante->retId() : 0);
		$bInscrAutoModules = ($iIdForm > 0 ? $this->oFormationCourante->retInscrAutoModules() : TRUE);
		$iIdMod = ($iIdForm > 0 && isset($this->oModuleCourant) && is_object($this->oModuleCourant) ? $this->oModuleCourant->retId() : 0);
		
		$oStatutUtilisateur = new CStatutUtilisateur($this->oBdd,$iIdPers);
		$oStatutUtilisateur->initStatuts($iIdForm,$iIdMod,$bInscrAutoModules);
		
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
	
	function redirigerVersLogin ($v_sPrenom=NULL,$v_sNom=NULL)
	{
		$this->ecrireEvenement(TYPE_EVEN_LOGIN_RATE,"{$this->iCodeEtat}:{$v_sPrenom}:{$v_sNom}");
		header("Location: {$this->sUrlLogin}?codeEtat={$this->iCodeEtat}");
		exit();
	}
	
	/**
	 * Cette méthode vérifie si un visiteur connecté à le droit de se trouver
	 * dans la formation actuelle.
	 * @param Aucun
	 */
	function verifAccessibleVisiteurs ()
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
	 * Cette méthode vérifie que l'utilisateur est inscrit dans la table des administrateurs.
	 *
	 * @return Cette méthode retourne TRUE si l'utilisateur actuel est inscrit dans la table
	 * des administrateurs ou FALSE dans le cas contraire.
	 */
	function verifAdministrateur ()
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
	
	function verifRespPotentiel ()
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
	
	function verifConcepteurPotentiel ()
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
	
	function verifConcepteur ()
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
	
	function initFormations ($v_sRequeteSql=NULL)
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
	 * Cette fonction remplit un tableau contenant les formations disponibles à l'utilisateur.
	 * @see CFormation
	 * @param $v_bRechStricte boolean
	 * @param $v_bStatutActuel boolean Si ce paramètre est à TRUE, on recherche les formations par rapport au statut actuel de l'utilisateur
	 * @return Retourne le nombre de formations trouvées
	 */
	function initFormationsUtilisateur ($v_bRechStricte=FALSE,$v_bStatutActuel=TRUE,$v_bDossierForms=FALSE)
	{
		if (($iIdPers = $this->retIdUtilisateur()) > 0)
		{
			if ($this->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
				$sRequeteSql = "SELECT Formation.* FROM Formation"
					." WHERE Formation.StatutForm<>'".STATUT_EFFACE."'"
					." ORDER BY Formation.OrdreForm ASC";
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
					
					$iNbrFormations = count($this->aoFormations);
				}
				
				$this->modifierInfosSession(SESSION_DOSSIER_FORMS,$this->asInfosSession[SESSION_DOSSIER_FORMS],TRUE);
				
				unset($oPermis, $oDossierForms);
			}
		}
		// }}}
		
		return $iNbFormations;
	}
	
	function verifModifierFormation ()
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
	 * Cette méthode permet de savoir si l'utilisateur a le droit
	 * d'ajouter/modifier/supprimer le module en cours et tout ce qui se
	 * rapporte à ce module (forum/chat/formulaire).
	 * 
	 * @return Retourne TRUE si l'utilisateur a des droits sur le module actuel
	 * de la formation ou FALSE dans le cas contraire.
	 */
	function verifModifierModule ()
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
	 * Définir la formation courante
	 * @param $v_iIdForm integer Numéro d'identifiant de la formation.
	 */
	function defFormationCourante ($v_iIdForm)
	{
		if ($v_iIdForm < 1) return;
		$this->asInfosSession[SESSION_FORM] = $v_iIdForm;
		$this->initFormationCourante();
	}
	
	function initFormationCourante ()
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
	 * Définir le module courant
	 * @param $v_iIdModule integer Numéro d'identidiant du module
	 * @param $v_bInitStatutsUtilisateur boolean Réinitialiser les statuts de l'utilisateur par rapport à ce module
	 */
	function defModuleCourant ($v_iIdModule,$v_bInitStatutsUtilisateur=FALSE)
	{
		$this->asInfosSession[SESSION_MOD] = $v_iIdModule;
		$this->initModuleCourant();
		if ($v_bInitStatutsUtilisateur) $this->initStatutsUtilisateur();
	}
	
	function initModuleCourant ()
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
	function defRubriqueCourante ($v_iUnite)
	{
		$this->asInfosSession[SESSION_UNITE] = $v_iUnite;
		$this->initRubriqueCourante();
	}
	
	function initRubriqueCourante ()
	{
		if (!is_object($this->oModuleCourant) && !$this->initModuleCourant())
			return FALSE;
		$this->oModuleCourant->initRubriqueCourante($this->asInfosSession[SESSION_UNITE]);
		$this->oRubriqueCourante = &$this->oModuleCourant->oRubriqueCourante;
		return (is_object($this->oRubriqueCourante));
	}
	// }}}
	
	// {{{ Activité courante
	function defActivCourante ($v_iActiv)
	{
		$this->asInfosSession[SESSION_ACTIV] = $v_iActiv;
		$this->initActivCourante();
	}
	
	function initActivCourante ()
	{
		if (!is_object($this->oRubriqueCourante) && !$this->initRubriqueCourante()) 
			return FALSE;
		$this->oRubriqueCourante->initActivCourante($this->asInfosSession[SESSION_ACTIV]);
		$this->oActivCourante = &$this->oRubriqueCourante->oActivCourante;
		return (is_object($this->oActivCourante));
	}
	// }}}
	
	// {{{ Sous-activité courante
	function defSousActivCourante ($v_iSousActiv)
	{
		$this->asInfosSession[SESSION_SOUSACTIV] = $v_iSousActiv;
		$this->initSousActivCourante();
	}
	
	function initSousActivCourante ()
	{
		if (!is_object($this->oActivCourante) && !$this->initActivCourante())
			return FALSE;
		$this->oActivCourante->initSousActivCourante($this->asInfosSession[SESSION_SOUSACTIV]);
		$this->oSousActivCourante = &$this->oActivCourante->oSousActivCourante;
		
		return (is_object($this->oSousActivCourante));
	}
	// }}}
	
 	function initInscritsFormation ()
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
	
	function initInscritsModule ($v_bVerifInscrAutoModules=TRUE)
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
	
	function verifEtudiant ($v_iIdPers=NULL)
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
	function initEquipes ($v_bInitMembres=FALSE,$v_iIdNiveau=NULL,$v_iTypeNiveau=NULL)
	{
		if (empty($v_iIdNiveau)) $v_iIdNiveau = $this->retIdNiveau();
		if (empty($v_iTypeNiveau)) $v_iTypeNiveau = $this->retTypeNiveau();
		
		if ($v_iTypeNiveau != TYPE_INCONNU && $v_iIdNiveau > 0)
		{
			$oRechEquipes = new CEquipe($this->oBdd);
			$oRechEquipes->initEquipesEx($v_iIdNiveau,$v_iTypeNiveau,$v_bInitMembres);
			$this->aoEquipes = $oRechEquipes->aoEquipes;
		}
		else
			$this->aoEquipes = array();
		
		return count($this->aoEquipes);
	}
	
	function verifMembre ($v_iIdPers=0)
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
	 * Cette méthode permet de récupérer les informations à propos de son équipe
	 * @param  $v_bInitMembres boolean Initialiser les informations personnels des membres de l'équipe
	 * @param  $v_iIdPers integer Numéro d'identifiant de la personne
	 * @return La valeur renvoyée par la méthode est TRUE si l'opération réussit ou FALSE dans le cas contraire
	 */
	function initEquipe ($v_bInitMembres=FALSE,$v_iIdPers=NULL)
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
				if ($oEquipe->initEquipe($v_iIdPers,$iIdNiveau,$iTypeNiveau,$v_bInitMembres))
					$this->oEquipe = $oEquipe;
			}
		}
		
		return is_object($this->oEquipe);
	}
	
	function verifEquipe ($v_iIdEquipe=0)
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
	
	function retTypeNiveau ()
	{
		if (is_object($this->oSousActivCourante)) return TYPE_SOUS_ACTIVITE;
		else if (is_object($this->oActivCourante)) return TYPE_ACTIVITE;
		else if (is_object($this->oRubriqueCourante)) return TYPE_RUBRIQUE;
		else if (is_object($this->oModuleCourant)) return TYPE_MODULE;
		else if (is_object($this->oFormationCourante)) return TYPE_FORMATION;
		else return TYPE_INCONNU;
	}
	
	function retIdNiveau ($v_iTypeNiveau=NULL)
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
	function reinitStatuts ()
	{
		$this->abStatutsUtilisateur = array();
		
		for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<=STATUT_PERS_DERNIER; $iIdxStatut++)
			$this->abStatutsUtilisateur[$iIdxStatut] = FALSE;
	}
	
	function ajouterStatut ($v_iStatut) { $this->abStatutsUtilisateur[$v_iStatut] = TRUE; }
	
	function verifStatut ($v_iStatut)
	{
		if ($v_iStatut >= STATUT_PERS_PREMIER && $v_iStatut <=STATUT_PERS_DERNIER)
			return $this->abStatutsUtilisateur[$v_iStatut];
		else
			return FALSE;
	}
	
	function retStatutUtilisateur ($v_bStatutActuel=TRUE)
	{
		if ($v_bStatutActuel)
			return ($this->iStatutUtilisateur >= STATUT_PERS_PREMIER && $this->iStatutUtilisateur <= STATUT_PERS_DERNIER
					? $this->iStatutUtilisateur
					: STATUT_PERS_VISITEUR);
		else
			return $this->retReelStatutUtilisateur();
	}
	
	function retReelStatutUtilisateur () {	return $this->asInfosSession[SESSION_STATUT_UTILISATEUR]; }
	
	function retHautStatutUtilisateur ()
	{
		return ($this->asInfosSession[SESSION_STATUT_ABSOLU] >= STATUT_PERS_PREMIER && $this->asInfosSession[SESSION_STATUT_ABSOLU] <= STATUT_PERS_DERNIER
			? $this->asInfosSession[SESSION_STATUT_ABSOLU]
			: STATUT_PERS_VISITEUR);
	}
	// }}}
	
	function retTexteUtilisateur ($v_bStatutActuel=TRUE)
	{
		if ($this->retIdUtilisateur() > 0)
			return $this->oUtilisateur->retNomComplet();
		else
			return (STATUT_PERS_VISITEUR == ($this->retStatutUtilisateur($v_bStatutActuel)) ? "Visiteur" : "Invité");
	}
	
	function retTexteStatutUtilisateur ($v_iStatut=NULL,$v_sSexe=NULL)
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
	
	function retTexteStatutsUtilisateur ()
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
	
	function insererRessource ($v_sNom,$v_sDescr,$v_sAuteur,$v_sUrl)
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
	
	function retTriCol () { return $this->asInfosSession[SESSION_TRI_COLONNE]; }
	
	function retTriDir () { return $this->asInfosSession[SESSION_TRI_DIRECTION]; }	
	
	/*
	** Fonction 		: lireInfosSession
	** Description		: lecture du cookie contenant l'état actuel de
	**					  la plate-forme (pour la navigation), ou des
	**					  données des paramètres de l'URL en l'absence
	**					  d'infos du cookie
	** Entrée			: aucune
	** Sortie			: aucune
	*/
	
	function lireInfosSession ()
	{
		global $HTTP_COOKIE_VARS, $HTTP_POST_VARS, $HTTP_GET_VARS;
		
		$this->bIdParFormulaire = FALSE;
		
		for ($iIndexInfo=SESSION_DEBUT; $iIndexInfo<=SESSION_FIN; $iIndexInfo++)
			$this->asInfosSession[$iIndexInfo] = 0;
		
		// Un utilisateur vient de se logger
		if (isset($HTTP_POST_VARS["idPseudo"]))
		{
			$this->asInfosSession[SESSION_PSEUDO] = $HTTP_POST_VARS["idPseudo"];
			$this->asInfosSession[SESSION_MDP]    = $HTTP_POST_VARS["idMdp"];
			
			// Il faudra rechercher le statut le plus haut
			$this->iStatutUtilisateur = NULL;
			
			$this->bIdParFormulaire = TRUE;
		}
		else if (!empty($HTTP_COOKIE_VARS[$this->sNomCookie]))
		{
			$this->asInfosSession = explode(":", $HTTP_COOKIE_VARS[$this->sNomCookie]);
			
			if (!empty($this->asInfosSession[SESSION_STATUT_UTILISATEUR]))
				$this->iStatutUtilisateur = $this->asInfosSession[SESSION_STATUT_UTILISATEUR];
		}
		
		// si certaines infos sont passées en paramètre de l'URL, elles priment
		// sur celles contenues dans le cookie
		if (isset($HTTP_GET_VARS["idForm"]))
		{
			include_once(dir_database("evenement.tbl.php"));
			$oEvenDetail = new CEvenement_Detail($this->oBdd,$this->asInfosSession[SESSION_UID],$HTTP_GET_VARS["idForm"]);
			$oEvenDetail->entrerFormation();
			
			if (isset($this->asInfosSession[SESSION_FORM]) && 
				$this->asInfosSession[SESSION_FORM] != $HTTP_GET_VARS["idForm"])
					$oEvenDetail->sortirFormation($this->asInfosSession[SESSION_FORM]);
			
			unset($oEvenDetail);
			
			$this->asInfosSession[SESSION_FORM] = $HTTP_GET_VARS["idForm"];
		}
		
		if (isset($HTTP_GET_VARS["idMod"]))
			$this->asInfosSession[SESSION_MOD] = $HTTP_GET_VARS["idMod"];
		
		if (isset($HTTP_GET_VARS["idUnite"]))
			$this->asInfosSession[SESSION_UNITE] = $HTTP_GET_VARS["idUnite"];
		
		if (isset($HTTP_GET_VARS["idActiv"]))
			$this->asInfosSession[SESSION_ACTIV] = $HTTP_GET_VARS["idActiv"];
		
		if (isset($HTTP_GET_VARS["idSousActiv"]))
			$this->asInfosSession[SESSION_SOUSACTIV] = $HTTP_GET_VARS["idSousActiv"];
		
		if (isset($HTTP_GET_VARS["triCol"]))
			$this->asInfosSession[SESSION_TRI_COLONNE] = $HTTP_GET_VARS["triCol"];
		
		if (isset($HTTP_GET_VARS["triDir"]))
			$this->asInfosSession[SESSION_TRI_DIRECTION] = $HTTP_GET_VARS["triDir"];
		
		if (empty($this->asInfosSession[SESSION_TRI_COLONNE]))
			$this->asInfosSession[SESSION_TRI_COLONNE] = "date";
		
		if (empty($this->asInfosSession[SESSION_TRI_DIRECTION]))
			$this->asInfosSession[SESSION_TRI_DIRECTION] = 1;
	}
	
	function retInfosSession ($v_iNumSession)
	{
		if ($v_iNumSession<SESSION_DEBUT || $v_iNumSession>SESSION_FIN)
			return -1;
		return $this->asInfosSession[$v_iNumSession];
	}
	
	/**
	 * Cette fonction permet de modifier un champ dans le cookie.
	 * @param integer v_iNumSession
	 * @param mixte v_mValeurSession
	 * @param boolean v_bEnregistrer
	 * @return Retourne TRUE si tout à fonctionner ou FALSE dans le cas contraire.
	 */
	function modifierInfosSession ($v_iNumSession,$v_mValeurSession,$v_bEnregistrer=FALSE)
	{
		if ($v_iNumSession<SESSION_DEBUT || $v_iNumSession>SESSION_FIN)
			return FALSE;
		
		$this->asInfosSession[$v_iNumSession] = $v_mValeurSession;
		
		if ($v_bEnregistrer)
			$this->enregistrerInfosSession();
		
		return TRUE;
	}
	
	function enregistrerInfosSession () { setcookie($this->sNomCookie,implode(":",$this->asInfosSession),0,"/","",0); }
	function retNumeroUniqueSession () { return $this->asInfosSession[SESSION_UID]; }
	
	function ecrireInfosSession ()
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
	
	function effacerInfosSession ()
	{
		$this->asInfosSession = array();
		
		for ($i=SESSION_DEBUT; $i<SESSION_FIN; $i++)
			$this->asInfosSession[$i] = 0;
		
		$this->oUtilisateur = NULL;
		$this->enregistrerInfosSession();
		$this->sNomCookie = NULL;
	}
	
	function afficherInfosSession ()
	{
		foreach ($this->asInfosSession as $sInfoSession)
			echo $sInfoSession."<br>";
	}
	
	/**
	 * @param  $v_iStatutUtilisateur integer
	 * @param  $v_bSauverDsCookie    boolean
	 * @return Retourne FALSE dans le cas d'un problème.
	 */
	function changerStatutUtilisateur ($v_iStatutUtilisateur,$v_bSauverDsCookie=TRUE)
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
	
	function retMdpCrypte ($v_sMdp)
	{
		$hResult = $this->oBdd->executerRequete("SELECT PASSWORD('{$v_sMdp}')");
		$oEnreg = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $oEnreg;
	}
	
	function ecrireEvenement ($v_iTypeEven,$v_sDonneesEven=NULL)
	{
		global $HTTP_SERVER_VARS;
		
		if (isset($this->oUtilisateur))
			$iUtilisateur = $this->oUtilisateur->retId();
		else
			$iUtilisateur = "null";
		
		if (isset($v_sDonneesEven))
			$v_sDonneesEven = "'$v_sDonneesEven'";
		else
			$v_sDonneesEven = "null";
		
		$sIp = $HTTP_SERVER_VARS["REMOTE_ADDR"];
		
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
	
	function retNom () { return $this->sNom; }
	function retEmail () { return $this->sEmail; }
	function retNumPortChat () { return $this->iNumPortChat; }
	function retNumPortAwareness () { return $this->iNumPortAwareness; }
	
	function retLanguage () { return (empty($this->asInfosSession[SESSION_LANG]) ? "fr" : $this->asInfosSession[SESSION_LANG]); }
	
	function dir_formation ($v_sFichierInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_formation($this->oFormationCourante->retId(),$v_sFichierInclure,$v_bCheminAbsolu); }
	
	function dir_cours ($v_sFichierInclure=NULL,$v_bCheminAbsolu=FALSE)
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
				$sUrlCours = dir_cours($iIdActiv,$iIdForm,$v_sFichierInclure,$v_bCheminAbsolu);
		}
		
		return $sUrlCours;
	}
	
	function dir_images ($v_sFichierInclure=NULL)
	{
		return $this->dir_cours()."images/{$v_sFichierInclure}";
	}
	
	function dir_ressources ($v_sFichierInclure=NULL,$v_bCheminAbsolu=TRUE)
	{
		return $this->dir_cours(NULL,$v_bCheminAbsolu)."ressources/{$v_sFichierInclure}";
	}
	
	function retRepRubriques ($v_sFichierInclure=NULL,$v_bCheminAbsolu=FALSE)
	{
		return $this->dir_formation ("rubriques/{$v_sFichierInclure}",$v_bCheminAbsolu);
	}
	
	function verifTuteur ($v_iIdPers=0)
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
	
	function retLien ($v_sLien=NULL,$v_sIntitule=NULL,$v_iMode=NULL,$v_sInfoBulle=NULL)
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
					$v_sLien = $this->dir_cours(NULL,FALSE).rawurlencode ($v_sLien);
				
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
				.">".htmlentities($v_sIntitule)."</a>"
				."</td></tr>\n"
				."</table>\n";
		else
			$r_sBalise = "&nbsp;&nbsp;<b>Ce document n'a pas &eacute;t&eacute; trouv&eacute; sur le serveur<br>"
				."[&nbsp;<span style=\"font-weight: normal;\">".basename(rawurldecode ($v_sLien))."</span>&nbsp;]"
				."</b>";
		
		return $r_sBalise;
	}
	
	function debug ($v_sMessage,$v_iNumLigne=NULL,$v_sNomFichier=NULL)
	{
		echo " [:DEBUG"
			.(($v_iNumLigne !== NULL) ? " - L'{$v_iNumLigne}'" : NULL)
			.(($v_sNomFichier !== NULL) ? " - F'{$v_sNomFichier}'" : NULL)
			.": {$v_sMessage}"
			."]<br>\n";
	}
	
	function retListeStatut ()
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
	 * Cette méthode initialise toutes les permissions de la personne
	 * par rapport à son statut actuel.
	 */
	function initPermisUtilisateur ($v_bStatutActuel=TRUE)
	{
		$this->oPermisUtilisateur = new CStatutPermission($this->oBdd);
		$this->oPermisUtilisateur->initPermissions($this->retStatutUtilisateur($v_bStatutActuel));
	}
	
	/**
	 * Cette fonction...
	 *
	 * @param v_sNomPermis string nom de la permission
	 * @return Retourne TRUE
	 */
	function verifPermission ($v_sNomPermis)
	{
		if (isset($this->oPermisUtilisateur) && is_object($this->oPermisUtilisateur))
			return $this->oPermisUtilisateur->verifPermission($v_sNomPermis);
		else
			return FALSE;
	}
	
	function creerFichierStatut ()
	{
		$aListeStatut = $this->retListeStatut();
		
		// ouverture du fichier en écriture
		if ($hFichier = fopen(dir_definition("statut.def.php"), "w+"))
		{
			// écriture balise php début + 'define's + balise php fin, puis fermeture
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
	 * Cette méthode vérifie si cet utilisateur a le droit d'utiliser les
	 * différents outils d'administration
	 */
	function verifPeutUtiliserOutils ($v_sNomPermission=NULL)
	{
		$bPeutUtiliserOutils = (isset($v_sNomPermission) ? $this->verifPermission($v_sNomPermission) : TRUE);
		
		if (!$bPeutUtiliserOutils ||
			!is_object($this->oUtilisateur) ||
			$this->retStatutUtilisateur() >= STATUT_PERS_ETUDIANT)
		{
			// Cette utilisateur ne peut pas utiliser cet outil
			header("Location: ".dir_root_plateform("blank.php",FALSE));
			exit();
		}
	}
	
	function initPersonnes ($v_iIdStatutPers=NULL,$v_iIdForm=0,$v_iIdMod=0)
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

function retListeStatuts ($v_sGenre="F")
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
