<?php
/**
 * @file	plate_forme.class.php
 * 
 * Contient la classe principale de la plate-forme, ainsi qu'une classe pour le moment inutilis�e pour les "traductions".
 * 
 * @date	06/09/2001
 * 
 * @author	C�dric FLOQUET
 * @author	Filippo PORCO
 * @author	J�r�me TOUZE
 * @author	Ludovic FLAMME
 */


/** @name Constantes - �tat d'identification utilisateur */
//@{
define("LOGIN_OK"				, 0);
define("LOGIN_MDP_INCORRECT"	, 8);
define("LOGIN_PAS_ENCORE_ID"	, 9);
define("LOGIN_PERSONNE_INCONNUE", 10);
//@}

/** @name Constantes - �l�ments de la session enregistr�e dans le cookie */
//@{
define("SESSION_DEBUT"				, 0);
define("SESSION_PSEUDO"				, 0);	/// Pseudo de la personne															@enum SESSION_PSEUDO
define("SESSION_NOM"				, 1);	/// Nom de la personne																@enum SESSION_NOM
define("SESSION_PRENOM"				, 2);	/// Pr�nom de la personne															@enum SESSION_PRENOM
define("SESSION_MDP"				, 3);	/// Mot de passe de la personne														@enum SESSION_MDP
define("SESSION_STATUT_ABSOLU"		, 4);	/// Statut de l'utilisateur le plus important										@enum SESSION_STATUT_ABSOLU
define("SESSION_STATUT_UTILISATEUR"	, 5);	/// Statut que l'utilisateur a choisit												@enum SESSION_STATUT_UTILISATEUR
define("SESSION_FORM"				, 6);	/// Num�ro de la formation courante													@enum SESSION_FORM
define("SESSION_MOD"				, 7);	/// Num�ro du module/cours courant													@enum SESSION_MOD
define("SESSION_UNITE"				, 8);	/// Num�ro de l'unit� courante (plus utilis�)										@enum SESSION_UNITE
define("SESSION_ACTIV"				, 9);	/// Num�ro de l'activit�															@enum SESSION_ACTIV
define("SESSION_SOUSACTIV"			, 10);	/// Num�ro de la sous-activit�														@enum SESSION_SOUSACTIV
define("SESSION_TRI_COLONNE"		, 11);	/// Pour les �crans o� l'on peut trier des tableaux, la colonne de tri principale	@enum SESSION_TRI_COLONNE
define("SESSION_TRI_DIRECTION"		, 12);	/// Toujours pour les m�mes �crans, tri croissant ou d�croissant ?					@enum SESSION_TRI_DIRECTION
define("SESSION_UID"				, 13);	/// Num�ro ID unique donn� par la table 'Evenement'									@enum SESSION_UID
define("SESSION_DOSSIER_FORMS"		, 14);	/// Num�ro du dossier de formations													@enum SESSION_DOSSIER_FORMS
define("SESSION_LANG"				, 15);	/// Langue de l'interface de l'utilisateur											@enum SESSION_LANG
define("SESSION_FIN"				, 14);
//@}

/** @name Constantes - types d'�v�nements (� logger) */
//@{
define("TYPE_EVEN_LOGIN_RATE"	, 1);
define("TYPE_EVEN_LOGIN_REUSSI"	, 2);
define("TYPE_EVEN_DECONNEXION"	, 3);
//@}

/** @name Constantes - types de "liens", en fait les types de sous-activit�s possibles (dans la colonne de gauche d'une rubrique) */
//@{
define("LIEN_PAGE_HTML"				, 1);
define("LIEN_DOCUMENT_TELECHARGER"	, 2);
define("LIEN_SITE_INTERNET"			, 3);
define("LIEN_CHAT"					, 4);
define("LIEN_FORUM"					, 5);
define("LIEN_GALERIE"				, 6);
define("LIEN_COLLECTICIEL"			, 7);
define("LIEN_UNITE"					, 8);
define("LIEN_FORMULAIRE"			, 9);	/// questionnaire = AEL (activit� en ligne)		@enum LIEN_FORMULAIRE
define("LIEN_TEXTE_FORMATTE"		, 10);
define("LIEN_GLOSSAIRE"				, 11);
define("LIEN_TABLEAU_DE_BORD"		, 12);
//@}

/** @name Constantes - modalit�s d'affichage pour certains liens HTML de la plate-forme */
//@{
define("FRAME_CENTRALE_DIRECT"		, 1);	/// Affichage imm�diat dans la frame centrale														@enum FRAME_CENTRALE_DIRECT
define("FRAME_CENTRALE_INDIRECT"	, 2);	/// Affichage d'une consigne pr�alable, contenant le lien, qui s'ouvrira dans la frame centrale		@enum FRAME_CENTRALE_INDIRECT
define("NOUVELLE_FENETRE_DIRECT"	, 3);	/// Affichage imm�diat dans une nouvelle fen�tre de navigateur										@enum NOUVELLE_FENETRE_DIRECT
define("NOUVELLE_FENETRE_INDIRECT"	, 4);	/// Affichage d'une consigne pr�alable, contenant le lien, qui s'ouvrira dans une nouvelle fen�tre	@enum NOUVELLE_FENETRE_INDIRECT
define("MODE_LIEN_TELECHARGER"		, 5);	/// Force le t�l�chargement de la cible du lien														@enum MODE_LIEN_TELECHARGER
//@}

/** @name Constantes - �l�ments de "structure" de formation */
//@{
define("TYPE_INCONNU"		, 0);
define("TYPE_FORMATION"		, 1);
define("TYPE_MODULE"		, 2);
define("TYPE_RUBRIQUE"		, 3);
define("TYPE_UNITE"			, 4);
define("TYPE_ACTIVITE"		, 5);
define("TYPE_SOUS_ACTIVITE"	, 6);
//@}

/** @name Constantes - statuts/disponibilit� des �l�ments de structure */
//@{
define("STATUT_FERME"			, 1);	/// Le lien est visible mais pas accessible															@enum STATUT_FERME
define("STATUT_OUVERT"			, 2);	/// Le lien est visible et accessible																@enum STATUT_OUVERT
define("STATUT_INVISIBLE"		, 3);	/// Le lien n'est pas affich�																		@enum STATUT_INVISIBLE
define("STATUT_ARCHIVE"			, 4);
define("STATUT_EFFACE"			, 5);	/// Effacement logique des enregistrements															@enum STATUT_EFFACE
define("STATUT_IDEM_PARENT"		, 6);
define("STATUT_LECTURE_SEULE"	, 7);	/// Le lien est visible, cliquable mais nous ne pouvons pas modifier quoique ce soit				@enum STATUT_LECTURE_SEULE

//define("STATUT_USER",3);
//@}

/** @name Constantes - modalit�s individuelles ou par �quipes pour certaines sous-activit�s */
//@{
define("MODALITE_IDEM_PARENT"				,0);
define("MODALITE_INDIVIDUEL"				,1);
define("MODALITE_PAR_EQUIPE"				,2);	/// (isol�e)         ==> Les �quipes ne voient pas les autres �quipes										@enum MODALITE_PAR_EQUIPE
define("MODALITE_POUR_TOUS"					,3);
define("MODALITE_PAR_EQUIPE_INTERCONNECTEE"	,4);	/// (interconnect�e) ==> Les �quipes voient les autres �quipes mais ne peuvent pas collaborer entre elles	@enum MODALITE_PAR_EQUIPE_INTERCONNECTEE	
define("MODALITE_PAR_EQUIPE_COLLABORANTE"	,5);	/// (collaborante)   ==> Les �quipes voient les autres �quipes et peuvent collaborer						@enum MODALITE_PAR_EQUIPE_COLLABORANTE
//@}

/** @name Constantes - types d'�l�ments dans les formulaires */
//@{
define("OBJFORM_QTEXTELONG"		, 1);
define("OBJFORM_QTEXTECOURT"	, 2);
define("OBJFORM_QNOMBRE"		, 3);
define("OBJFORM_QLISTEDEROUL"	, 4);
define("OBJFORM_QRADIO"			, 5);
define("OBJFORM_QCOCHER"		, 6);
define("OBJFORM_MPTEXTE"		, 7);
define("OBJFORM_MPSEPARATEUR"	, 8);
//@}

// ---------------------
// Utiliser dans les formulaires, lorsqu'un tuteur d�cide que le document de
// l'�tudiant est soumis automatiquement ou pas au tuteur
// ---------------------
/** @name Constantes - */
//@{
define("SOUMISSION_MANUELLE"	, 0);
define("SOUMISSION_AUTOMATIQUE"	, 1);
//@}

// ---------------------
// Tri
// ---------------------
define("PAS_TRI"		, 0);
define("TRI_CROISSANT"	, 1);
define("TRI_DECROISSANT", 2);

// ---------------------
// D�claration des fichiers � inclure
// ---------------------
require_once(dir_code_lib("bdd_mysql.class.php"));		// G�rer la base de donn�es

$sDirInclude = dir_include();
require_once("{$sDirInclude}theme.global.php");			// Th�me de la plate-forme
require_once("{$sDirInclude}config.inc");				// Informations � propos de la base de donn�es
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
 * Classe permettant de r�cup�rer des constantes 'texte' (consignes, messages...)
 * dans la base de donn�es.
 *
 * @author C�dric FLOQUET
 */
class CConstantes
{
	var $oBdd;				// Nom de la base � utiliser
	var $sTable;			// Nom de la table (i18n_fr) qui contient toutes les traductions pour la plate-forme
	var $sTableI18N;		// Nom de la table qui contient toutes les d�clarations des constantes
	
	/**
	 * Initialise les param�tres de db et de tables de l'objet.
	 * 
	 * @param	v_oBdd		l'objet CBdd qui repr�sente la connexion � la db
	 * @param	v_sTable	le nom de la table correspondant � la langue voulue
	 */
	function CConstantes(&$v_oBdd, $v_sTable)
	{
		$this->oBdd			= &$v_oBdd;
		$this->sTable		= $v_sTable;
		$this->sTableI18N	= "i18n";
	}
	
	/**
	 * R�cup�re un texte traduit sur base de son id.
	 * 
	 * @param	v_iId				l'id du texte � chercher
	 * @param	v_bConversionHtml	si \c true, remplace les caract�res qui le n�cessitent par des entit�s html dans le texte retourn�
	 * 
	 * @return	le texte associ� � l'Id, dans la langue voulue (d�finie dans le constructeur par un nom de table correspondant dans la db)
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
				return htmlentities($r_Enreg->ContenuTxt);
			else
				return $r_Enreg->ContenuTxt;
		}
		else
			return "[$v_iId]";
	}
	
	/**
	 * Cr�e un fichier de constantes repr�sentant les termes traduits.
	 * 
	 * Cette fonction doit �tre appel�e lorsque de nouveaux termes � traduire ont �t� ajout�s dans la db, sinon leur id sera inaccessible en php.
	 * Si \c v_sNomFichier n'est pas sp�cifi�, on le fichier aura le m�me nom que la table utilis�e pour la langue (voir CConstantes::CConstantes).
	 * 
	 * @param	v_sNomFichier	nom du fichier de constantes � cr�er
	 */
	function creerFichierConstantes ($v_sNomFichier = NULL)
	{
		if (!$v_sNomFichier)
			$v_sNomFichier = dir_definition("{$this->sTable}.def.php");
		
		// r�cup�ration de TOUS les enregs de la table contenant les noms des constantes
		$hResult = $this->oBdd->executerRequete("SELECT * FROM {$this->sTableI18N} ORDER BY ConstTxt");
		
		if ($this->oBdd->retNbEnregsDsResult($hResult))
		{
			if ($hFichier = fopen($v_sNomFichier, "w+"))
			{
				// �criture balise php d�but + 'define's + balise php fin, puis fermeture
				fputs($hFichier,"<?php\n\n");
				fputs($hFichier,sprintf(_("// Ce fichier a �t� g�n�r� (%s) automatiquement par la plate-forme\n\n"),date ("d M Y")));
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
 * Classe principale de la plate-forme.
 *
 * @see CPersonne
 * @see CFormation
 */
class CProjet
{
	var $sCheminWeb;			// Chemin du projet � partir de la racine du serveur web
	var $sCheminComplet;		// chemin complet du projet sur le syst�me de fichiers
	var $sNomRep;				// uniquement le nom du r�pertoire du projet
	//var $sCheminDocs;			// chemin (relatif) du r�pertoire de stockage des documents
	var $sNomCookie;			// nom du cookie associ� au projet
	var $asInfosSession;		// contenu du cookie associ� au projet
	var $bIdParFormulaire;		// les infos utilisateurs ont-elles �t� transmises par formulaire ? Sinon, c'est par cookie
	var $oBdd;					// interface vers la base de donn�es du projet
	var $sNom;					// nom complet du projet
	var $sUrlAccueil;			// URL compl�te de la page d'accueil du projet
	var $sUrlLogin;				// URL de la page permettant de s'identifier
	var $oErreurs;				// :DEBUG: pour tester la classe CConstantes
	var $aoAdmins;				// tableau contenant les administrateurs de la plate-forme
	var $oUtilisateur;			// personne actuellement connect�e	
	var $oEquipe;				// �quipe � laquelle cette personne appartient (si applicable)
	var $aoFormations;
	var $aoInscrits;			// pointeur vers les inscrits � la formation courante
	
	var $oFormationCourante;	// formation courante pendant la navigation
	var $oModuleCourant;		// pointeur vers le module courant de la formation courante
	var $oRubriqueCourante;
	var $oActivCourante;		// pointeur vers l'activit� courante
	var $oSousActivCourante;	// pointeur vers la sous-activit� courante
	
	var $aoPersonnes;
	var $aoEquipes;				// pointeur vers les �quipes de l'activit� courante
	var $abStatutsUtilisateur;	// statuts de l'utilisateur connect� dans le contexte actuel (administrateur du projet, tuteur du module, etc...)
	var $iStatutUtilisateur;	// parmi les statuts possibles, lequel est utilis� ?
	var $oPermisUtilisateur;	// permission par rapport au statut courant
	var $iCodeEtat;
	
	var $oI18N;
	
	/**
	 * Initialise l'objet principal du projet, g�n�ralement unique et global.
	 */
	function CProjet ($v_bEffacerCookie = FALSE, $v_bRedirigerSiIncorrect = FALSE)
	{
		global $HTTP_SERVER_VARS;
		global $g_sNomCookie;
		global $g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd;
		
		// init 'simples' des propri�t�s, c�d sans acc�s � la bdd
		$this->sCheminWeb     = str_replace('\\', '/', dirname($HTTP_SERVER_VARS["PHP_SELF"]));
		$this->sCheminComplet = $HTTP_SERVER_VARS["DOCUMENT_ROOT"].$this->sCheminWeb;
		$this->sNomRep        = $g_sNomBdd;
		$this->sUrlLogin      = "http://".$HTTP_SERVER_VARS["HTTP_HOST"].$this->sCheminWeb."/"."login-index.php";
		$this->sNomCookie     = $g_sNomCookie;
		
		// connexion � la base de donn�es du projet
		$this->oBdd = new CBddMySql($g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd);
		
		// lecture de la config � partir des param�tres de l'URL ou du cookie
		$this->lireInfosSession();
		
		/*$pBdd = &$this->oBdd;
		$this->oI18N = new CConstantes($pBdd,I18N);
		unset($pBdd);
		
		if (ACTIVER_RECREATION_FICHIER_I18N)
			$this->oI18N->creerFichierConstantes(dir_definition("i18n.def.php"));*/
		
		// on a besoin des constantes d�finies pour ce projet
		$this->init();										// init des propri�t�s � partir de la base
		$this->initUtilisateur($v_bRedirigerSiIncorrect);	// init des infos sur la personne connect�e
		$this->initFormationCourante();						// init de la formation courante
		
		if ($v_bEffacerCookie)
			setcookie($this->sNomCookie);
		else
			$this->ecrireInfosSession();
	}
	
	/**
	 * Lib�re les ressources utilis�es par l'objet CProjet.
	 * 
	 * Pour le moment, seule la connexion � la db est explicitement ferm�e.
	 */
	function terminer()
	{
		if (isset($this->oBdd))
			$this->oBdd->terminer();
	}
	
	/**
	 * Initialise les variables membres avec les informations du projet (nom, n� du port pour les chats, etc).
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
	 * Initialise les variables/objets repr�sentant les admins du projet.
	 * 
	 * @return	le nombre d'admins trouv�s
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
	 * Initialise l'objet oUtilisateur en fonction des donn�es d'identification disponibles.
	 * 
	 * Les donn�es d'id v�rifi�es dans le tableau \c asInfosSession, lui-m�me initialis� dans
	 * \c lireInfosSession().
	 * 
	 * @param	v_bRedirigerSiIncorrect	si \c true, et qu'un probl�me survient avec l'identification
	 * 									de l'utilisateur, on arr�te le chargement de la page et on le renvoie � 
	 * 									l'�cran de login (redirection HTTP), ce qui signifie que tout appel de 
	 * 									cette fonction doit �tre fait avant d'�crire quoi que ce soit dans la 
	 * 									page HTML
	 */
	function initUtilisateur($v_bRedirigerSiIncorrect = FALSE)
	{
		global $HTTP_SERVER_VARS;
		
		// on r�cup�re les nom/pr�nom/pseudo/mdp du cookie
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
				
				// si mdp OK, on init la propri�t� 'Utilisateur'
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
				$this->redirigerVersLogin($sTmpPrenom,$sTmpNom);
		}
		else if ($this->bIdParFormulaire)
		{
			// si ID ok, on inscrit le login dans la table Evenement, mais seulement la 1�re
			// fois (donc quand on vient de l'�cran login, donc quand les infos proviennent 
			// du formulaire)
			$this->ecrireEvenement(TYPE_EVEN_LOGIN_REUSSI,$HTTP_SERVER_VARS["HTTP_USER_AGENT"]);
		}
	}
	
	/**
	 * R�cup�re l'id de l'utilisateur identifi�.
	 * 
	 * @return	l'id de l'utilisateur s'il est identifi�, sinon 0
	 */
	function retIdUtilisateur()
	{
		return (isset($this->oUtilisateur) && is_object($this->oUtilisateur) ? $this->oUtilisateur->retId() : 0);
	}
	
	/**
	 * Initialise les statuts de l'utilisateur connect�.
	 *
	 * @param	$v_bVerifierStatutForm	si \c true, les statuts seront d�termin� par rapport � la formation
	 * 									courante
	 * 
	 * @return	le nombre de statuts trouv�s pour cet utilisateur
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
	 * Effectue une redirection Http vers la page de login apr�s avoir enregistr� "l'�v�nement".
	 * 
	 * @param	v_sPrenom	pr�nom de l'utilisateur concern�
	 * @param	v_sNom		nom de l'utilisateur concern�
	 */
	function redirigerVersLogin($v_sPrenom = NULL, $v_sNom = NULL)
	{
		$this->ecrireEvenement(TYPE_EVEN_LOGIN_RATE, "{$this->iCodeEtat}:{$v_sPrenom}:{$v_sNom}");
		header("Location: {$this->sUrlLogin}?codeEtat={$this->iCodeEtat}");
		exit();
	}
	
	/**
	 * V�rifie si un visiteur connect� a le droit de se trouver dans la formation actuelle.
	 * Si ce n'est pas le cas, une redirection automatique vers le login a lieu.
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
	 * V�rifie que l'utilisateur connect� est admin du projet.
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
	 * V�rifie que l'utilisateur connect� est "responsable potentiel".
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
	 * V�rifie que l'utilisateur connect� est "concepteur potentiel".
	 * 
	 * @return	\c true si l'utilisateur est concepteur potenetiel sur le projet
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
	 * V�rifie que l'utilisateur connect� est concepteur pour le module/cours courant.
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
	 * Initialise les formations existantes du projet.
	 * 
	 * Elle sont plac�es dans le tableau aoFormations. Par d�faut, les formations avec le statut "effac�e" 
	 * (logiquement) ne sont pas r�cup�r�es.
	 * 
	 * @param	v_sRequeteSql	requ�te � ex�cuter pour initialiser les formations. Si \c null, utilise la requ�te
	 * 							standard
	 * 
	 * @return	le nombre de formations trouv�es
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
	 * Remplit un tableau contenant les formations disponibles � l'utilisateur.
	 * 
	 * @param	v_bRechStricte	si \c true, seules les formations pour lesquelles l'utilisateur a le statut exact
	 * 							demand� seront retourn�es
	 * 							si \c false, les formations pour lesquelles l'utilisateur a un statut inf�rieur � celui 
	 * 							demand� seront aussi retourn�es
	 * @param	v_bStatutActuel	si \c true, recherche les formations par rapport au statut actuel de l'utilisateur, 
	 * 							c�d la variable \c iStatutUtilisateur, qui peut avoir chang� par rapport au statut 
	 * 							enregistr� (cookie) pour la session dans \c asInfosSession[SESSION_STATUT_UTILISATEUR], 
	 * 							cette derni�re �tant utilis�e si le param�tre est \c false.
	 * @param	v_bDossierForms	si \c true, les formations sont celles du dossier de formations de l'utilisateur
	 * 
	 * @return	le nombre de formations trouv�es
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
				// Utilisateur inscrit au moins � une formation
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
	 * V�rifie si l'utilisateur connect� a le droit de modifier la formation courante.
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
	 * V�rifier que l'utilisateur a le droit d'ajouter/modifier/supprimer le module en cours et tout ce qui se 
	 * rapporte � ce module (forum/chat/formulaire).
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
	 * D�finit la formation courante.
	 * 
	 * @param	$v_iIdForm	id de la formation � d�finir
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
	 * Les droits de l'utilisateur et l'acc�s au visiteur sont �galement r�initialis�s.
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
	 * D�finit le module courant.
	 * 
	 * @param v_iIdModule				id du module � d�finir
	 * @param v_bInitStatutsUtilisateur	si \c true, les statuts de l'utilisateur seront red�finis en fonction du 
	 * 									nouveau module
	 */
	function defModuleCourant($v_iIdModule, $v_bInitStatutsUtilisateur = FALSE)
	{
		$this->asInfosSession[SESSION_MOD] = $v_iIdModule;
		$this->initModuleCourant();
		if ($v_bInitStatutsUtilisateur) $this->initStatutsUtilisateur();
	}
	
	/**
	 * Initialise le module courant.
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
	
	// {{{ Rubrique/Unit� courante
	/**
	 * D�finit la rubrique/unit� courante.
	 * 
	 * @param	v_iUnite	id de la rubrique/unit� � d�finir
	 */
	function defRubriqueCourante($v_iUnite)
	{
		$this->asInfosSession[SESSION_UNITE] = $v_iUnite;
		$this->initRubriqueCourante();
	}
	
	/**
	 * Initialise l'unit� courante.
	 * 
	 * @return	\c true si l'unit� courante est valide
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
	
	// {{{ Activit� courante
	/**
	 * D�finit l'activit� courante
	 * 
	 * @param	v_iActiv	id de l'activit� � d�finir.
	 * 
	 * @note	Les �l�ments anciennement appel�s \e activit�s, y compris dans la db, sont maintenant appel�s 
	 * 			<em>blocs d'activit�s</em> dans la pratique.
	 */
	function defActivCourante($v_iActiv)
	{
		$this->asInfosSession[SESSION_ACTIV] = $v_iActiv;
		$this->initActivCourante();
	}
	
	/**
	 * Initialise l'activit� courante.
	 * 
	 * @return	\c true si l'activit� courante est valide
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
	
	// {{{ Sous-activit� courante
	/**
	 * D�finit la sous-activit� courante.
	 * 
	 * @param	v_iSousActiv	id de la sous-activit� � d�finir.
	 * 
	 * @note	Les �l�mentes anciennement appel�s <em>sous-activit�s</em>, y compris dans la db, sont maintenant
	 * 			appel�s \e activit�s dans la pratique.
	 */
	function defSousActivCourante($v_iSousActiv)
	{
		$this->asInfosSession[SESSION_SOUSACTIV] = $v_iSousActiv;
		$this->initSousActivCourante();
	}
	
	/**
	 * Initialise l'activit� courante.
	 * 
	 * @return	\c si la sous-activit� courante est valide.
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
	 * Initialise la liste des inscrits � la formation courante.
	 * 
	 * @return	le nombre d'inscrits
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
	 * Initialise la liste des inscrits au module courant.
	 * 
	 * @param	v_bVerifInscrAutoModules	si \c true, v�rifie si tous les inscrits � la formation courante
	 * 										doivent automatiquement l'�tre � tous les modules de cette formation.
	 * 
	 * @return	le nombre d'inscrits
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
	 * V�rifie qu'un utilisateur a le statut d'�tudiant dans la formation courante.
	 * 
	 * @param	v_iIdPers	id de l'utilisateur � v�rifier. Si \c null, c'est l'utilisateur connect� qui est v�rifi�.
	 * 
	 * @return	\c true si l'utilisateur a le statut d'�tudiant dans la formation courante.
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
	
	// {{{ M�thodes des �quipes
	/**
	 * Initialise les �quipes attach�es � un �l�ment d�termin� d'une formation (la formation elle-m�me, module/cours, 
	 * rubrique/unit�, activit�, sous-activit�).
	 * 
	 * @param	v_bInitMembres	si \c true, initialise �galement les membres des �quipes.
	 * @param	v_iIdNiveau		l'id de l'�l�ment pour lequel on veut r�cup�rer les �quipes. Sa signification d�pend
	 * 							du param�tre \c v_iTypeNiveau.
	 * @param	v_iTypeNiveau	le num�ro repr�sentant le type d'�l�ment pour lequel on veut r�cup�rer les �quipes, c�d
	 * 							formation, module, rubrique, activit�, sous-activit� (voir les constantes TYPE_).
	 * 
	 * @return	le nombre d'�quipes trouv�es.
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
	 * V�rifie qu'un utilisateur est membre d'une des �quipes actuellement initialis�es.
	 * 
	 * @param	v_iIdPers	id de l'utilisateur concern� par la v�rification. S'il est <= 0, l'utilisateur connect�
	 * 						est pris pour la v�rification.
	 * 
	 * @return	\c true si l'utilisateur est membre d'une des �quipes actuellement initialis�es.
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
	 * Initialise l'�quipe d'un utilisateur dans le contexte/niveau/�l�ment courant (formation, module, etc).
	 * 
	 * @param	$v_bInitMembres	si \c true, initialise �galement les membres de l'�quipe.
	 * @param	$v_iIdPers		id de l'utilisateur dont on veut conna�tre l'�quipe. S'il est <= 0, l'utilisateur 
	 * 							connect� est pris pour la v�rification.
	 * 
	 * @return	\c true si l'utilisateur fait bien partie d'une �quipe.
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
	 * V�rifie qu'une �quipe fait partie de la liste des �quipes actuellement initialis�es.
	 * 
	 * @param	v_iIdEquipe	id de l'�quipe � v�rifier. S'il <= 0, l'�quipe actuellement initialis�e est prise pour la
	 * 						v�rification.
	 * 
	 * @return	\c true si l'�quipe fait partie de la liste des �quipes actuelles.
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
	 * Retourne le niveau le plus profond auquel on se trouve actuellement dans la structure formation->module->etc.
	 * 
	 * @return	le num�ro correspondant au (type de) niveau actuel (constantes TYPE_).
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
	 * Retourne l'id de l'�l�ment dans lequel on se trouve, par rapport au "niveau" (formation, module, rubrique, etc).
	 * 
	 * @param	v_iTypeNiveau	le niveau pour lequel on d�sire recevoir un id d'�l�ment. S'il est absent ou invalide, 
	 * 							c'est le niveau le plus profond actuellement initialis� qui est utilis� (r�sultat de la 
	 * 							fonction ::retTypeNiveau().
	 * 
	 * @return	l'id de l'�l�ment initialis� qui se trouve au niveau demand�.
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
	
	// {{{ M�thodes des statuts
	/**
	 * Remet � \c false tous les statuts de l'utilisateur, comme s'ils n'�taient pas initialis�s (=>aucun statut).
	 */
	function reinitStatuts()
	{
		$this->abStatutsUtilisateur = array();
		
		for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<=STATUT_PERS_DERNIER; $iIdxStatut++)
			$this->abStatutsUtilisateur[$iIdxStatut] = FALSE;
	}
	
	/**
	 * Ajoute un statut � la liste des statuts de l'utilisateur.
	 * 
	 * @param	v_iStatut	le num�ro du statut � ajouter (voir constantes STATUT_PERS_).
	 */
	function ajouterStatut($v_iStatut)
	{
		$this->abStatutsUtilisateur[$v_iStatut] = TRUE;
	}
	
	/**
	 * V�rifie que l'utilisateur connect� poss�de un statut sp�cifique.
	 * 
	 * @param	v_iStatut	le num�ro du statut � v�rifier.
	 * 
	 * @return	\c true si l'utilisateur poss�de ce statut.
	 */
	function verifStatut($v_iStatut)
	{
		if ($v_iStatut >= STATUT_PERS_PREMIER && $v_iStatut <=STATUT_PERS_DERNIER)
			return $this->abStatutsUtilisateur[$v_iStatut];
		else
			return FALSE;
	}
	
	/**
	 * Retourne le statut de l'utilisateur connect�.
	 * 
	 * @param	v_bStatutActuel	si \c true, retourne les statut actuel de l'utilisateur, c�d la variable \c 
	 * 							iStatutUtilisateur, qui peut avoir chang� par rapport au statut enregistr� (cookie) 
	 * 							pour la session dans \c asInfosSession[SESSION_STATUT_UTILISATEUR], cette derni�re 
	 * 							est utilis� si le param�tre est \c false.
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
	 * Retourne le statut "r�el" de l'utilisateur, c�d celui qui est enregistr� pour la session dans la variable
	 * asInfosSession. C'est le statut qui est choisi par l'utilisateur, et qui est transmis de page en page. 
	 * Il se peut qu'on le change momentan�ment sur une page � l'aide de la variable iStatutUtilisateur, mais celui-ci 
	 * ne sera pas enregistr� dans la session tant qu'il n'a pas �t� �galement transf�r� dans asInfosSession.
	 * 
	 * @return	le statut de l'utilisateur.
	 */
	function retReelStatutUtilisateur()
	{
		return $this->asInfosSession[SESSION_STATUT_UTILISATEUR];
	}
	
	/**
	 * Retourne le statut le plus �lev� que l'utilisateur poss�de dans le contexte (session) actuel.
	 * 
	 * @return	le statut le plus �lev� pour l'utilisateur.
	 */
	function retHautStatutUtilisateur()
	{
		return ($this->asInfosSession[SESSION_STATUT_ABSOLU] >= STATUT_PERS_PREMIER && $this->asInfosSession[SESSION_STATUT_ABSOLU] <= STATUT_PERS_DERNIER
			? $this->asInfosSession[SESSION_STATUT_ABSOLU]
			: STATUT_PERS_VISITEUR);
	}
	// }}}
	
	/**
	 * Retourne le nom de l'utilisateur connect�, ou visiteur/invit� s'il est inconnu.
	 * 
	 * @param	v_bStatutActuel
	 * 
	 * @return	le nom de l'utilisateur s'il est idientifi�, sinon "visiteur/invit�".
	 */
	function retTexteUtilisateur($v_bStatutActuel = TRUE)
	{
		if ($this->retIdUtilisateur() > 0)
			return $this->oUtilisateur->retNomComplet();
		else
			return (STATUT_PERS_VISITEUR == ($this->retStatutUtilisateur($v_bStatutActuel)) ? "Visiteur" : "Invit�");
	}
	
	/**
	 * Retourne le terme correspondant � un statut, en tenant compte du sexe M/F.
	 * 
	 * @param	v_iStatut	le statut pour lequel on veut le terme.
	 * @param	v_sSexe		le genre � utiliser pour le terme.
	 * 
	 * @return	le terme employ� pour d�signer le statut.
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
	 * Retourne une liste des termes employ�s pour d�signer les diff�rents statuts de l'utilisateur connect�. 
	 * Cette liste est s�par�e par des &lt;BR&gt;.
	 * 
	 * @return	la liste des statuts, sous forme textuelle.
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
	 * Ins�re dans la db une nouvelle ressource (fichier) associ�e � une sous-activit�.
	 * L'id utilisateur est celui de l'actuel connect�, et la sous-activit� est celle qui est actuellement 
	 * initialis�e (logiquement celle dans laquelle l'utilisateur se trouve).
	 * fait rien.
	 * 
	 * @param	v_sNom		le nom donn� � la ressource (fichier) � ins�rer.
	 * @param	v_sDescr	la description de la ressource.
	 * @param	v_sAuteur	l'auteur de la ressource, sous forme de texte (pas un id utilisateur).
	 * @param	v_sUrl		le chemin de la ressource.
	 * 
	 * @return	\c true si la ressource a bien �t� ins�r�e, \c false si l'utilisateur courant n'est pas identifi�, 
	 * 			ou s'il ne se trouve pas dans une sous-activit� (aucune initialis�e), ou encore si un probl�me est 
	 * 			survenu � l'insertion SQL.
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
	 * Retourne la colonne sur laquelle le tri est actuellement effectu�, telle qu'enregistr�e dans la session de 
	 * l'utilisateur (cookie). Cette fonctionnalit� est par exemple utilis�e pour l'affichage des collecticiels.
	 * 
	 * @return	la colonne sur lequel le tri est effectu�, sous forme de cha�ne de caract�res (par ex "date").
	 */
	function retTriCol()
	{
		return $this->asInfosSession[SESSION_TRI_COLONNE];
	}
	
	/**
	 * Retourne le sens (normal ou invers�) actuel du tri sur des colonnes. Cette fonctionnalit� est par exemple 
	 * utilis�e pour l'affichage des collecticiels.
	 * 
	 * @return	le sens du tri.
	 * 
	 * @todo	indiquer le format exact de la donn�e dans ce commentaire.
	 */
	function retTriDir()
	{
		return $this->asInfosSession[SESSION_TRI_DIRECTION];
	}
	
	/**
	 * R�cup�re les informations de la session actuelle de navigation de l'utilisateur. Cette lecture a lieu soit dans 
	 * le cookie, soit � partir des param�tres de l'url, les donn�es pass�es par cette derni�re ayant priorit�. 
	 * Ces donn�es sont par exemple le pseudo de l'utilisateur, le mot de passe associ�, le statut actuel, les ids 
	 * des formation/module/rubrique/etc dans lesquels l'utilisateur se trouve actuellement.
	 */
	function lireInfosSession()
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
		
		// si certaines infos sont pass�es en param�tre de l'URL, elles priment
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
	
	/**
	 * Retourne une donn�e parmi celles enregistr�es pour la session utilisateur.
	 * 
	 * @param	v_iNumSession	num�ro de la donn�e de session � r�cup�rer (voir les constantes SESSION_).
	 * 
	 * @return	la donn�e provenant de la session.
	 */
	function retInfosSession($v_iNumSession)
	{
		if ($v_iNumSession<SESSION_DEBUT || $v_iNumSession>SESSION_FIN)
			return -1;
		return $this->asInfosSession[$v_iNumSession];
	}
	
	/**
	 * Modifie une donn�e de la session utilisateur, et r�enregistre �ventuellement la session (cookie).
	 * 
	 * @param	v_iNumSession		num�ro de la donn�e de session � modifier (voir constantes SESSION_).
	 * @param	v_mValeurSession	nouvelle valeur pour la donn�e.
	 * @param	v_bEnregistrer		si \c true, la session \e compl�te sera r�enregistr�e dans un cookie.
	 * 
	 * @return	\c true si le num�ro de la donn�e de session � enregistrer �tait correct.
	 * 
	 * @note	Si on d�cide d'enregistrer la session, �tant donn� que cela implique l'�criture d'un cookie, il faut 
	 * 			qu'aucune sortie html/php n'ai eu lieu dans la page; cette fonction doit donc �tre appel�e avant tout 
	 * 			affichage.
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
	 * Enregistre les donn�es de session actuelles de l'utilisateur dans un cookie.
	 * 
	 * @note	L'�criture d'un cookie exige qu'aucune sortie html/php, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction.
	 */
	function enregistrerInfosSession()
	{
		setcookie($this->sNomCookie, implode(":", $this->asInfosSession), 0, "/", "", 0);
	}
	
	/**
	 * Retourne l'id unique de la session utilisateur.
	 * 
	 * @return	l''id de la session utilisateur.
	 */
	function retNumeroUniqueSession()
	{
		return $this->asInfosSession[SESSION_UID];
	}
	
	/**
	 * Enregistre la totalit� des donn�es de la session utilisateur, dans leur �tat actuel, dans le cookie.
	 * 
	 * @note	L'�criture d'un cookie exige qu'aucune sortie html/php, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction.
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
		
		// Rubrique/Unit�
		if (is_object($this->oRubriqueCourante))
			$this->asInfosSession[SESSION_UNITE] = $this->oRubriqueCourante->retId();
		
		// Activit�
		if (is_object($this->oActivCourante))
			$this->asInfosSession[SESSION_ACTIV] = $this->oActivCourante->retId();
		
		// Sous-activit�
		if (is_object($this->oSousActivCourante))
			$this->asInfosSession[SESSION_SOUSACTIV] = $this->oSousActivCourante->retId();
		
		// Statut de l'utilisateur
		if (empty($this->asInfosSession[SESSION_STATUT_UTILISATEUR]))
			$this->asInfosSession[SESSION_STATUT_UTILISATEUR] = $this->iStatutUtilisateur;
		
		// ensuite, on inscrit la totalit� du cookie
		$this->enregistrerInfosSession();
	}
	
	/**
	 * Efface compl�tement la session de l'utilisateur, ainsi que le cookie qui y est associ�.
	 * Cela a pour effet l'annulation de son identification.
	 * 
	 * @note	L'�criture d'un cookie exige qu'aucune sortie html/php, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction.
	 */
	function effacerInfosSession()
	{
		$this->asInfosSession = array();
		
		for ($i=SESSION_DEBUT; $i<SESSION_FIN; $i++)
			$this->asInfosSession[$i] = 0;
		
		$this->oUtilisateur = NULL;
		$this->enregistrerInfosSession();
		$this->sNomCookie = NULL;
	}
	
	/**
	 * Fonction utilitaire qui permet d'afficher toutes les informations de la session utilisateur dans leur �tat 
	 * actuel, ligne par ligne.
	 */
	function afficherInfosSession()
	{
		foreach ($this->asInfosSession as $sInfoSession)
			echo $sInfoSession."<br>";
	}
	
	/**
	 * Modifie le statut actuel de l'utilisateur.
	 * 
	 * @param	v_iStatutUtilisateur	le num�ro du statut � activer.
	 * @param	v_bSauverDsCookie		si \c true, enregistre imm�diatement les donn�es \e compl�tes de la session 
	 * 									utilisateur, et donc le nouveau statut.
	 * 
	 * @return	\c true si le num�ro du statut demand� est valide.
	 * 
	 * @note	L'�criture d'un cookie exige qu'aucune sortie html/php, donc aucun affichage, n'ait eu lieu avant 
	 * 			l'appel de la fonction.
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
	 * Retourne une version crypt�e d'une cha�ne de caract�re (mot de passe).
	 * 
	 * @param	v_sMdp	cha�ne � crypter.
	 * 
	 * @return	la version crypt�e de la cha�ne \c v_sMdp.
	 * 
	 * @todo	Pour le moment, le cryptage se fait par la fonction \c PASSWORD() de MySQL, ce qui �tait malheureusement 
	 * 			une tr�s mauvaise id�e, car cette fonction ne devait �tre utilis�e qu'en interne par MySQL, et a �t� 
	 * 			modifi�e dans MySQL 4.1, ce qui rendra les mots de passe d�j� encod�s incompatible avec la nouvelle 
	 * 			version de la fonction.
	 * 
	 * 			Id�alement, il faudrait donc crypter avec une fonction standard comme \c MD5() si on veut garder des 
	 * 			mots de passe ind�cryptables ou des fonctions comme \c AES_CRYPT()/AES_DECRYPT() si on veut pouvoir 
	 * 			r�cup�rer le mot de passe � tout moment (�a �viterait le syst�me tordu mis en place apr�s coup, qui 
	 * 			consiste � �crire une version d�crypt�e du mot de passe de chaque utilisateur dans le fichier 
	 * 			\c src/tmp/mdpncpte prot�g� contre la lecture, � chaque fois que quelqu'un se connecte et passe 
	 * 			le login.
	 */
	function retMdpCrypte($v_sMdp)
	{
		$hResult = $this->oBdd->executerRequete("SELECT PASSWORD('{$v_sMdp}')");
		$oEnreg = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $oEnreg;
	}
	
	/**
	 * Ecrit un "�v�nement" dans la db, pour le moment il s'agit des connexions/d�connexions des utilisateurs.
	 * 
	 * @param	v_iTypeEven		num�ro du type d'�v�nement (voir constantes TYPE_EVEN_).
	 * @param	v_sDonneesEven	donn�es suppl�mentaires � associer � l'�v�nement.
	 */
	function ecrireEvenement($v_iTypeEven, $v_sDonneesEven = NULL)
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
		
		// Mettre � jour le cookie
		if ($v_iTypeEven == TYPE_EVEN_LOGIN_REUSSI)
		{
			$this->asInfosSession[SESSION_UID] = $this->oBdd->retDernierId();
			$this->enregistrerInfosSession();
		}
	}
	
	/**
	 * Retourne le nom du projet.
	 * 
	 * @return	le nom du projet, tel qu'enregistr� dans la db.
	 */
	function retNom()
	{
		return $this->sNom;
	}
	
	/**
	 * Retourne l'adresse e-mail de contact du projet.
	 * 
	 * @return	l'adresse e-mail.
	 */
	function retEmail()
	{
		return $this->sEmail;
	}
	
	/**
	 * Retourne le port TCP sur lequel est lanc� le serveur de chat utilis� par les clients d'Esprit.
	 * 
	 * @return	le port TCP associ� au serveur chat.
	 */
	function retNumPortChat()
	{
		return $this->iNumPortChat;
	}
	
	/**
	 * Retourne le port TCP sur lequel est lanc� le serveur d'awareness utilis� par les clients d'Esprit.
	 * 
	 * @return	le port TCP associ� au serveur d'awareness (JCVD-style :-D ).
	 */
	function retNumPortAwareness()
	{
		return $this->iNumPortAwareness;
	}
	
	/**
	 * Retourne la langue de l'utilisateur connect�.
	 * 
	 * @return	un code qui repr�sente la langue actuelle de l'utilisateur.
	 * 
	 * @deprecated	???
	 */
	function retLanguage()
	{
		return (empty($this->asInfosSession[SESSION_LANG]) ? "fr" : $this->asInfosSession[SESSION_LANG]);
	}
	
	/**
	 * Retourne le chemin du r�pertoire qui abrite les fichiers de la formation actuelle (initialis�e).
	 * 
	 * @param	v_sFichierInclure	le nom d'un �ventuel fichier qui fera alors partie du chemin retourn�.
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourn� sera absolu. Si \c false, il sera relatif.
	 * 
	 * @return	le chemin vers le r�pertoire de la formation courante.
	 */
	function dir_formation($v_sFichierInclure = NULL, $v_bCheminAbsolu = FALSE)
	{
		return dir_formation($this->oFormationCourante->retId(), $v_sFichierInclure, $v_bCheminAbsolu);
	}
	
	/**
	 * Retourne le chemin du r�pertoire qui abrite les fichiers de l'activit� courante.
	 * 
	 * @param	v_sFichierInclure	le nom d'un �ventuel fichier qui fera alors partie du chemin retourn�.
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourn� sera absolu. Si \c false, il sera relatif.
	 * 
	 * @return	le chemin vers le r�pertoire de l'activit� courante.
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
	 * Retourne le chemin du r�pertoire images de l'activit� courante, relatif � la racine de la plate-forme.
	 * 
	 * @param	v_sFichierInclure	le nom d'un �ventuel fichier qui fera alors partie du chemin retourn�.
	 * 
	 * @return	le chemin vers le r�pertoire images pour l'activit� courante.
	 */
	function dir_images($v_sFichierInclure = NULL)
	{
		return $this->dir_cours()."images/{$v_sFichierInclure}";
	}
	
	/**
	 * Retourne le chemin du r�pertoire ressources de l'activit� courante.
	 * 
	 * @param	v_sFichierInclure	le nom d'un �ventuel fichier qui fera alors partie du chemin retourn�.
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourn� sera absolu. Si \c false, il sera relatif.
	 * 
	 * @return	le chemin vers le r�pertoire ressources pour l'activit� courante.
	 */
	function dir_ressources($v_sFichierInclure = NULL, $v_bCheminAbsolu = TRUE)
	{
		return $this->dir_cours(NULL, $v_bCheminAbsolu)."ressources/{$v_sFichierInclure}";
	}
	
	/**
	 * Retourne le chemin du r�pertoire rubriques de la formation courante, relatif � la racine de la plate-forme.
	 * 
	 * @param	v_sFichierInclure	le nom d'un �ventuel fichier qui fera alors partie du chemin retourn�.
	 * @param	v_bCheminAbsolu		si \c true, le chemin retourn� sera absolu. Si \c false, il sera relatif.
	 * 
	 * @return	le chemin vers le r�pertoire rubriques pour la formation courante.
	 */
	function retRepRubriques($v_sFichierInclure = NULL, $v_bCheminAbsolu = FALSE)
	{
		return $this->dir_formation("rubriques/{$v_sFichierInclure}", $v_bCheminAbsolu);
	}
	
	/**
	 * V�rifie qu'un utilisateur a le statut de tuteur
	 * 
	 * @param	v_iIdPers	l'id de l'utilisateur. S'il est absent, l'utilisateur connect� sera pris pour la 
	 * 						v�rification
	 * 
	 * @return	\c true si l'utilisateur est tuteur, \c false si ce n'est pas le cas \e ou que l'utilisateur � v�rifier 
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
	 * Retourne le code HTML n�cessaire � la repr�sentation d'un lien, en fonction de diff�rents param�tres
	 * 
	 * @param	v_sLien			l'url du lien
	 * @param	v_sIntitule		le texte � utiliser pour afficher le lien
	 * @param	v_iMode			le mode d'affichage de l'url lorsque le lien sera cliqu�
	 * @param	v_sInfoBulle
	 * 
	 * @return	
	 */
	function retLien($v_sLien = NULL, $v_sIntitule = NULL, $v_iMode = NULL, $v_sInfoBulle = NULL)
	{
		$sCheminAbsolu = dir_document_root();
		
		if (empty($v_sLien) && empty($v_sIntitule))
			return "<b>Pas de lien attribu�</b>";
		
		$err = FALSE;
		
		// si l'intitul� du lien n'est pas fourni, on utilise le lien lui-m�me
		// � la place
		if (empty($v_sIntitule))
			$v_sIntitule = rawurldecode($v_sLien);
		
		// le "mode" du lien sp�cifie si celui-ci passera par le "filtre" de
		// t�l�chargement, s'il sera ouvert dans la fen�tre principale, ou 
		// dans une nouvelle fen�tre
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
				// le nom du fichier � t�l�charger est pass� en URL, donc il doit �tre
				// encod� pour �viter les erreurs avec les caract�res sp�ciaux
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
	
	function debug($v_sMessage , $v_iNumLigne = NULL, $v_sNomFichier = NULL)
	{
		echo " [:DEBUG"
			.(($v_iNumLigne !== NULL) ? " - L'{$v_iNumLigne}'" : NULL)
			.(($v_sNomFichier !== NULL) ? " - F'{$v_sNomFichier}'" : NULL)
			.": {$v_sMessage}"
			."]<br>\n";
	}
	
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
	 * Cette m�thode initialise toutes les permissions de la personne
	 * par rapport � son statut actuel.
	 */
	function initPermisUtilisateur($v_bStatutActuel = TRUE)
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
	function verifPermission($v_sNomPermis)
	{
		if (isset($this->oPermisUtilisateur) && is_object($this->oPermisUtilisateur))
			return $this->oPermisUtilisateur->verifPermission($v_sNomPermis);
		else
			return FALSE;
	}
	
	function creerFichierStatut()
	{
		$aListeStatut = $this->retListeStatut();
		
		// ouverture du fichier en �criture
		if ($hFichier = fopen(dir_definition("statut.def.php"), "w+"))
		{
			// �criture balise php d�but + 'define's + balise php fin, puis fermeture
			fputs($hFichier,"<?php\n\n");
			fputs($hFichier,sprintf(_("// Ce fichier a �t� g�n�r� (%s) automatiquement par la plate-forme\n\n"),date ("d M Y")));
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
	 * Cette m�thode v�rifie si cet utilisateur a le droit d'utiliser les
	 * diff�rents outils d'administration
	 */
	function verifPeutUtiliserOutils($v_sNomPermission = NULL)
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

function retListeStatuts($v_sGenre = "F")
{
	if (strtoupper($v_sGenre) == "M")
		return array(
			array(STATUT_FERME,_("Ferm�")),
			array(STATUT_OUVERT,_("Ouvert")),
			array(STATUT_INVISIBLE,_("Invisible")),
			array(STATUT_ARCHIVE,_("Archiv�")));
	else
		return array(
			array(STATUT_FERME,_("Ferm�e")),
			array(STATUT_OUVERT,_("Ouverte")),
			array(STATUT_INVISIBLE,_("Invisible")),
			array(STATUT_ARCHIVE,_("Archiv�")));
}

?>
