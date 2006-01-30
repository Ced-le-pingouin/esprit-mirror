<?php

/*
** Fichier .................: formation.tbl.php
** Description .............: 
** Date de création ........: 01/03/2002
** Dernière modification ...: 03/06/2005
** Auteurs .................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                            Filippo PORCO <filippo.porco@umh.ac.be>
**                            Jérôme TOUZE <webmaster@guepard.org>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("module.tbl.php"));
require_once(dir_code_lib("tri.inc.php"));

define("INTITULE_FORMATION","Formation");

/**
 * @class CFormation
 *
 * @see CModule
 * @see CForum
 * @see CPersonne
 * @see CStatutUtilisateur
 */
class CFormation
{
	var $iId;				/**< Ce membre est de type ENTIER. Numéro d'identifiant unique de la formation. */
	
	var $oBdd;				/**< Ce membre est de type CBdd. @see CBdd */
	var $oEnregBdd;
	
	var $oAuteur;			/**< Ce membre est de type CPersonne. Il contient les renseignements de la personne qui a créé cette formation. @see CPersonne */
	
	var $aoFormations;		/**< Tableau contenant la liste de formations. */
	var $aoModules;
	
	var $aoConcepteurs;
	var $aoInscrits;
	var $aoModelesEquipes;
	var $oModuleCourant;
	
	var $aoForums;
	
	var $aoGlossaires;
	var $aoElementsGlossaire;
	
	var $sTri;
	
	// Déclaration des pseudo-constantes
	var $ORDRE=12;
	var $TYPE=13;
	
	function CFormation (&$v_oBdd,$v_iIdForm=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdForm;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdForm;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Formation"
				." WHERE IdForm='".$this->retId()."'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function detruire ()
	{
		$this->iId = NULL;
		$this->oBdd = NULL;
		$this->oEnregBdd = NULL;
		
		$this->aoFormations = NULL;
	}
	
	function rafraichir ()
	{
		if ($this->retId() > 0)
			$this->init();
	}
	
	function verrouillerTables ()
	{
		$sRequeteSql = "LOCK TABLES"
			." Formation WRITE"
			.", Module WRITE"
			.", Intitule WRITE"
			.", Module_Rubrique WRITE"
			.", Activ WRITE"
			.", SousActiv WRITE"
			.", Forum WRITE"
			.", SujetForum WRITE"
			.", MessageForum WRITE"
			.", Formation_Inscrit WRITE"
			.", Formation_Tuteur WRITE"
			.", Formation_Concepteur WRITE"
			.", Formation_Resp WRITE"
			.", Module_Concepteur WRITE"
			.", Module_Tuteur WRITE"
			.", Module_Inscrit WRITE"
			.", SousActiv_SousActiv WRITE"
			.", ".CRessourceSousActiv::verrouillerTables(FALSE)
			.", ".CEquipe::verrouillerTables(FALSE)
			.", Chat WRITE"
			/*.", SousActiv_Glossaire WRITE"*/;
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function retValeurMax ($v_iChamp)
	{
		switch ($v_iChamp)
		{
			case $this->ORDRE: $sChampMax = "OrdreForm"; break;
			case $this->TYPE:  $sChampMax = "TypeForm"; break;
			default: return;
		}
		$sRequeteSql = "SELECT MAX({$sChampMax}) FROM Formation";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iMax = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iMax;
	}
	
	/**
	 * Cette fonction permet d'ajouter une nouvelle formation.
	 *
	 * @param v_sNomForm Nom de la formation;
	 * @param v_sDescrForm Description de la formation;
	 * @param v_iInscrSpontForm Si ce paramètre vaut 1 alors tous les étudiants sont inscrits automatiquement à tous les cours;
	 * @param v_iIdPers Numéro d'identifiant de la personne qui vient de créer cette formation.
	 */
	function ajouter ($v_sNomForm=NULL,$v_sDescrForm=NULL,$v_iInscrSpontForm=1,$v_iIdPers=0)
	{
		if (empty($v_sNomForm))
			$v_sNomForm = INTITULE_FORMATION." sans nom";
		
		$sRequeteSql = "INSERT INTO Formation SET"
			." IdForm=NULL"
			.", NomForm='".MySQLEscapeString($v_sNomForm)."'"
			.", DescrForm='".MySQLEscapeString($v_sDescrForm)."'"
			.", DateDebForm=NOW()"
			.", DateFinForm=NOW()"
			.", StatutForm='".STATUT_FERME."'"
			.", InscrSpontForm='0'"
			.", InscrAutoModules='{$v_iInscrSpontForm}'"
			.", InscrSpontEquipeF='0'"
			.", NbMaxDsEquipeF='10'"
			.", OrdreForm='".($this->retValeurMax($this->ORDRE) + 1)."'"
			.", TypeForm='".($this->retValeurMax($this->TYPE) + 1)."'"
			.", VisiteurAutoriser='0'"
			.", IdPers='{$v_iIdPers}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
		$this->associerResponsable($v_iIdPers);
		
		return $this->iId;
	}
	
	/**
	 * Cette méthode permet ajouter dans la table des reponsables de formation
	 * la personne qui vient de créer une nouvelle formation.
	 *
	 * @param v_iIdPers identifiant de la personne
	 */
	function associerResponsable ($v_iIdPers)
	{
		if (($iIdForm = $this->retId()) < 1 || $v_iIdPers < 1)
			return;
		
		$sRequeteSql = "REPLACE INTO Formation_Resp SET"
			." IdForm='{$iIdForm}'"
			.", IdPers='{$v_iIdPers}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function retNumOrdreMax () { return $this->retValeurMax($this->ORDRE); }
	
	function copier ($v_bRecursive=TRUE)
	{
		$iIdForm = $this->copierFormation();
		
		if ($iIdForm < 1)
			return 0;
		
		// ---------------------
		// Copier les ressources de la formation actuelle
		// vers la nouvelle formation
		// ---------------------
		$srcRep = dir_formation($this->retId());
		$dstRep = dir_formation($iIdForm);
		
		@mkdir($dstRep,0744);
		
		$handle = @opendir($srcRep);
		
		while ($fichier = @readdir($handle))
			if ($fichier != "." &&
				$fichier != ".." &&
				!strstr($fichier,"activ_"))
				copyTree(($srcRep.$fichier),($dstRep.$fichier));
		
		@closedir($dir);
		
		// ---------------------
		// Copier tous les modules
		// ---------------------
		if ($v_bRecursive)
			$this->copierModules($iIdForm);
		
		return $iIdForm;
	}
	
	function copierFormation ()
	{
		$sRequeteSql = "INSERT INTO Formation SET"
			." IdForm=NULL"
			.", NomForm='".MySQLEscapeString($this->retNom())."'"
			.", DescrForm='".MySQLEscapeString($this->retDescr())."'"
			.", DateDebForm=NOW()"
			.", DateFinForm=NOW()"
			.", StatutForm='".STATUT_FERME."'"
			.", OrdreForm='".($this->retNumOrdreMax() + 1)."'"
			.", TypeForm='".$this->retType()."'"
			.", IdPers='{$this->oEnregBdd->IdPers}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return $this->oBdd->retDernierId();
	}
	
	function copierModules ($v_iIdForm)
	{
		$this->initModules();
		foreach ($this->aoModules as $oModule)
			$oModule->copier($v_iIdForm);
		$this->aoModules = NULL;
	}
	
	function effacer ()
	{
		if ($this->retId() < 1)
			return;
		
		$this->effacerEvenements();
		
		$this->effacerEtudiants();
		$this->effacerTuteurs();
		$this->effacerConcepteurs();
		$this->effacerResponsables();
		
		// Effacer les équipes
		$this->effacerEquipes();
		
		// Effacer tous les modules qui appartiennent à cette formation
		$this->effacerModules();
		
		// Effacer cette formation
		$this->effacerFormation();
		
		if (PHP_OS === "Linux")
			exec("rm -rf ".dir_formation($this->retId(),NULL,TRUE));
		
		$this->redistNumsOrdre();
		
		$iIdFormPrecedent = $this->retIdFormPrecedente();
		
		unset($this->iId,$this->oEnregBdd);
		
		return $iIdFormPrecedent;
	}
	
	function effacerFormation ()
	{
		$sRequeteSql = "DELETE FROM Formation"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerEvenements ()
	{
		require_once(dir_database("evenement.tbl.php"));
		$oEven = new CEvenement($this->oBdd);
		$oEven->defIdFormation($this->retId());
		$oEven->effacer();
	}
	
	function initEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$iNbrEquipes = $oEquipe->initEquipes($this->retId(),NULL,NULL,NULL,NULL,TRUE);
		$this->aoEquipes = $oEquipe->aoEquipes;
		return $iNbrEquipes;
	}
	
	function initMembres ($v_bAppartenirEquipe=TRUE,$v_iSensTri=TRI_CROISSANT)
	{
		$iIdxMembre = 0;
		$this->aoMembres = array();
		
		$iIdForm = $this->retId();
		
		if ($v_bAppartenirEquipe)
			$sRequeteSql = "SELECT Personne.*"
				." FROM Personne"
 				." LEFT JOIN Equipe_Membre USING (IdPers)"
 				." LEFT JOIN Equipe USING (IdEquipe)"
 				." WHERE Equipe.IdForm='{$iIdForm}' AND Equipe.IdMod='0'";
		else
			$sRequeteSql = "SELECT Personne.*"
				." FROM Formation_Inscrit"
				." LEFT JOIN Personne USING (IdPers)"
				." LEFT JOIN Equipe ON Equipe.IdForm=Formation_Inscrit.IdForm"
				." LEFT JOIN Equipe_Membre ON Equipe.IdEquipe=Equipe_Membre.IdEquipe"
				." AND Formation_Inscrit.IdPers=Equipe_Membre.IdPers"
				." WHERE Equipe.IdForm='{$iIdForm}' AND Equipe.IdMod='0'"
				." GROUP BY Personne.IdPers	HAVING COUNT(Equipe_Membre.IdEquipe)='0'";
		
		if ($v_iSensTri <> PAS_TRI)
			$sRequeteSql .= " ORDER BY Personne.Nom ".($v_iSensTri == TRI_DECROISSANT ? "DESC" : "ASC");
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoMembres[$iIdxMembre] = new CPersonne($this->oBdd);
			$this->aoMembres[$iIdxMembre]->init($oEnreg);
			$iIdxMembre++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxMembre;
	}
	
	/**
	 * Cette fonction recherche la liste des glossaires appartenant à une formation.
	 *
	 * @brief Contruire une liste de glossaires.
	 * @return integer La fonction renvoie le nombre totale de glossaires trouvés ou zéro dans le cas contraire.
	 * @see CGlossaire
	 */
	function initGlossaires ()
	{
		$iIdxGlossaire = 0;
		$this->aoGlossaires = array();
		
		$sRequeteSql = "SELECT * FROM Glossaire"
			." WHERE IdForm='".$this->retId()."'"
			." ORDER BY TitreGlossaire";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoGlossaires[$iIdxGlossaire] = new CGlossaire($this->oBdd);
			$this->aoGlossaires[$iIdxGlossaire]->init($oEnreg);
			$iIdxGlossaire++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxGlossaire;
	}
	
	function ajouterGlossaire ($v_sTitreGlossaire,$v_iIdPers)
	{
		$oGlossaire = new CGlossaire($this->oBdd);
		$oGlossaire->ajouter($v_sTitreGlossaire,$this->retId(),$v_iIdPers);
	}
	
	function initElementsGlossaire ($v_iIdGlossaire=NULL)
	{
		$iIdxElems = 0;
		$this->aoElementsGlossaire = array();
		
		if ($v_iIdGlossaire < 1)
			$v_iIdGlossaire = 0;
		
		$sRequeteSql = "SELECT GlossaireElement.*"
			.", Glossaire_GlossaireElement.IdGlossaire AS estSelectionne"
			." FROM GlossaireElement"
			." LEFT JOIN Glossaire_GlossaireElement ON GlossaireElement.IdGlossaireElement=Glossaire_GlossaireElement.IdGlossaireElement"
			." AND Glossaire_GlossaireElement.IdGlossaire='{$v_iIdGlossaire}'"
			." WHERE GlossaireElement.IdForm='".$this->retId()."'"
			." ORDER BY GlossaireElement.TitreGlossaireElement ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoElementsGlossaire[$iIdxElems] = new CGlossaireElement($this->oBdd);
			$this->aoElementsGlossaire[$iIdxElems]->init($oEnreg);
			$iIdxElems++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxElems;
	}
	
	function ajouterElementsGlossaire ($v_iIdGlossaire,$v_aiIdsElementsGlossaire)
	{
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdsElementsGlossaire as $iIdGlossaireElement)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."('{$v_iIdGlossaire}','{$iIdGlossaireElement}')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "INSERT INTO Glossaire_GlossaireElement"
				." (IdGlossaire,IdGlossaireElement)"
				." VALUES {$sValeursRequete};";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerElementsGlossaire ($v_iIdGlossaire)
	{
		$sRequeteSql = "DELETE FROM Glossaire_GlossaireElement"
			." WHERE IdGlossaire='{$v_iIdGlossaire}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_FORMATION,$this->iId);
	}
	
	function effacerModelesEquipes ()
	{
		return FALSE;
	}
	
	function effacerLogiquement ()
	{
		$this->defStatut(STATUT_EFFACE);
		$this->defNumOrdre(0);
		$this->redistNumsOrdre();
		return $this->retIdFormPrecedente();
	}
	
	function effacerResponsables ()
	{
		$sRequeteSql = "DELETE FROM Formation_Resp"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerConcepteurs ()
	{
		$sRequeteSql = "DELETE FROM Formation_Concepteur"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerTuteurs ()
	{
		$sRequeteSql = "DELETE FROM Formation_Tuteur"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerEtudiants ()
	{
		$sRequeteSql = "DELETE FROM Formation_Inscrit"
			." WHERE IdForm='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerModules ()
	{
		$iNbrModules = $this->initModules();
		
		for ($idx=0; $idx<$iNbrModules; $idx++)
			$this->aoModules[$idx]->effacer();
		
		unset($this->aoModules);
	}
	
	function retIdFormPrecedente ()
	{
		$iNumOrdre = $this->retNumOrdre();
		
		if ($iNumOrdre>1) $iNumOrdre--;
		
		$iIdForm = 0;
		
		while ($iNumOrdre>0)
		{
			$sRequeteSql = "SELECT * FROM Formation"
				." WHERE OrdreForm='{$iNumOrdre}'"
				." AND StatutForm<>'".STATUT_EFFACE."'";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$iIdForm = $oEnreg->IdForm;
				break;
			}
			
			$this->oBdd->libererResult($hResult);
			$iNumOrdre--;
		}
		
		return $iIdForm;
	}
	
	function initInscrits ($v_sModeTri="ASC")
	{
		$iIdxInscrit = 0;
		$this->aoInscrits = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Formation_Inscrit"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Formation_Inscrit.IdForm='".$this->retId()."'"
			." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom ASC";
		$hResult = $this->oBdd->executerRequete ($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoInscrits[$iIdxInscrit] = new CPersonne($this->oBdd);
			$this->aoInscrits[$iIdxInscrit]->init($oEnreg);
			$iIdxInscrit++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxInscrit;
	}
	
	function retNombreLignes ()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM Formation"
			." WHERE StatutForm<>'".STATUT_EFFACE."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbrLignes = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iNbrLignes;
	}
	
	function initResponsables ()
	{
		$iIdxResp = 0;
		$this->aoResponsables = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Formation_Resp"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Formation_Resp.IdForm='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete ($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoResponsables[$iIdxResp] = new CPersonne($this->oBdd);
			$this->aoResponsables[$iIdxResp]->init($oEnreg);
			$iIdxResp++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxResp;
	}
	
	function initModuleCourant ($v_iIdModule=NULL)
	{
		if ($v_iIdModule>0)
		{
			$this->oModuleCourant = new CModule($this->oBdd, $v_iIdModule);
			
			if ($this->oModuleCourant->retIdParent() != $this->retId())
				unset($this->oModuleCourant);
		}
	}
	
	function defTrier ($v_sNomChamps=NULL,$v_sSensTri=NULL)
	{
		if ($v_sNomChamps == "types")
			$this->sTri = " ORDER BY TypeForm";
		else if ($v_sNomChamps == "noms")
			$this->sTri = " ORDER BY NomForm";
		else
			$this->sTri = " ORDER BY OrdreForm";
		
		$this->sTri .= " ".(isset($v_sSensTri) ? $v_sSensTri : "ASC");
	}
	
	function retTrier ()
	{
		if (empty($this->sTri))
			$this->defTrier();
		
		return $this->sTri;
	}
	
	function defTrierParAnnee ($v_dDebut,$v_dFin)
	{
		$this->sTri = " AND DateDebForm>=\"{$v_dDebut}\""
			." AND DateDebForm<=\"{$v_dFin}\"".$this->sTri;
	}
	
	function defTrierParType ($v_iSensTri=NULL)
	{
		$this->sTri = " GROUP BY TypeForm"
			." ".(isset($v_iSensTri) ? $v_iSensTri : "ASC");
	}
	
	function defVisiteurAutoriser ($v_bAutoriserVisiteur)
	{
		$this->mettre_a_jour("VisiteurAutoriser",$v_bAutoriserVisiteur);
	}
	
	function accessibleVisiteurs () { return $this->oEnregBdd->VisiteurAutoriser; }
	function setVisiteurAutoriser () { return $this->oEnregBdd->VisiteurAutoriser; }
	
	function initFormationsPourCopie ($v_iIdPers)
	{
		$idx = 0;
		$this->aoFormations = array();
		
		$sRequeteSql = "SELECT Formation.* FROM Formation"
			." LEFT JOIN Formation_Resp ON Formation.IdForm=Formation_Resp.IdForm"
			." WHERE Formation.StatutForm IN ('".STATUT_OUVERT."','".STATUT_FERME."')"
			." AND Formation_Resp.IdPers='{$v_iIdPers}'"
			.$this->retTrier();
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormations[$idx] = new CFormation($this->oBdd);
			$this->aoFormations[$idx]->init($oEnreg);
			$idx++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $idx;
	}
	
	function initFormations ($v_bAdministrateur=FALSE)
	{
		$idx = 0;
		$this->aoFormations = array();
		
		$sRequeteSql = "SELECT * FROM Formation"
			.($v_bAdministrateur ? NULL : " WHERE StatutForm IN ('".STATUT_OUVERT."','".STATUT_FERME."')")
			.$this->retTrier();
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormations[$idx] = new CFormation($this->oBdd);
			$this->aoFormations[$idx]->init($oEnreg);
			$idx++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $idx;
	}
	
	/**
	 * Cette fonction remplit un tableau contenant la liste des modules
	 * de l'utilisateur.
	 *
	 * @param v_iIdPers numéro d'identifiant de la personne ;
	 * @param v_bPeutVoirTousModules ce paramètre reçoit TRUE si cette personne peut voir tous les modules ou FALSE dans le cas contraire ;
	 * @param v_iIdStatutUtilisateur rechercher les formations par rapport à un statut.
	 */
	function initModules ($v_iIdPers=0,$v_iIdStatutUtilisateur=NULL,$v_bRechStricte=FALSE)
	{
		include_once(dir_database("modules.class.php"));
		
		$iIdxModule = 0;
		$this->aoModules = array();
		
		$oModules = new CModules($this->oBdd,$this->retId(),$this->aoModules);
		
		if (isset($v_iIdStatutUtilisateur) && $v_bRechStricte)
			$iIdxModule = $oModules->initModulesParStatut($v_iIdPers,$v_iIdStatutUtilisateur);
		else if (isset($v_iIdStatutUtilisateur))
			$iIdxModule = $oModules->initModulesUtilisateur($v_iIdPers,$v_iIdStatutUtilisateur,$this->retInscrAutoModules());
		else
			$iIdxModule = $oModules->initTousModules();
		
		return $iIdxModule;
	}
	
	function verifEquipe ($v_iIdEquipe,$v_iIdPers=NULL)
	{
		$sRequeteSql = "SELECT Equipe_Membre.IdPers FROM Ressource"
			." LEFT JOIN Equipe_Membre USING (IdPers)"
			." LEFT JOIN Equipe USING (IdEquipe)"
			." LEFT JOIN Ressource_SousActiv ON Ressource.IdRes=Ressource_SousActiv.IdRes"
			." LEFT JOIN SousActiv USING (IdSousActiv)"
			." LEFT JOIN Activ USING (IdActiv)"
			." WHERE Activ.ModaliteActiv='".MODALITE_PAR_EQUIPE."'"
			." AND Equipe.IdEquipe='{$v_iIdEquipe}'"
			.(isset($v_iIdPers) ? " AND Ressource.IdPers='$v_iIdPers'" : NULL);
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		
		$this->oBdd->libererResult($hResult);
		
		return (!empty($oEnreg));
	}
	
	/**
	 * Cette fonction remplit un tableau contenant tous les concepteurs inscrits aux cours de cette formation.
	 * @see CPersonne
	 */
	function initConcepteurs ($v_sModeTri="ASC")
	{
		$iIdxConcepteur = 0;
		$this->aoConcepteurs = array();
		
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." LEFT JOIN Formation_Concepteur USING (IdPers)"
			." WHERE Formation_Concepteur.IdForm='".$this->retId()."'"
			." GROUP BY Personne.IdPers"
			." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoConcepteurs[$iIdxConcepteur] = new CPersonne($this->oBdd);
			$this->aoConcepteurs[$iIdxConcepteur]->init($oEnreg);
			$iIdxConcepteur++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxConcepteur;
	}
	
	/**
	 * Cette fonction remplit un tableau contenant tous les tuteurs inscrits aux cours de cette formation.
	 * @see CPersonne
	 */
	function initTuteurs ($v_sModeTri="ASC")
	{
		$iIdxTuteur = 0;
		$this->aoTuteurs = array();
		
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." LEFT JOIN Formation_Tuteur USING (IdPers)"
			." WHERE Formation_Tuteur.IdForm='".$this->retId()."'"
			." GROUP BY Personne.IdPers"
			." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoTuteurs[$iIdxTuteur] = new CPersonne($this->oBdd);
			$this->aoTuteurs[$iIdxTuteur]->init($oEnreg);
			$iIdxTuteur++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxTuteur;
	}
	
	function defNom ($v_sNomForm)
	{		
		$v_sNomForm = $this->oBdd->validerDonnee($v_sNomForm);
		
		if (strlen($v_sNomForm) < 1)
			$v_sNomForm = INTITULE_FORMATION." sans nom";
		
		$this->mettre_a_jour("NomForm",$v_sNomForm);
	}
	
	function defDescr ($v_sDescrForm)
	{		
		$this->mettre_a_jour("DescrForm",$this->oBdd->validerDonnee($v_sDescrForm));
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function retDateDeb ()
	{
		list($sDate) = explode(" ",$this->oEnregBdd->DateDebForm);
		return ereg_replace("([0-9]{4})-([0-9]{2})-([0-9]{2})","\\3-\\2-\\1",$sDate);
	}
	
	function retDateFin ()
	{
		list($sDate) = explode(" ",$this->oEnregBdd->DateFinForm);
		return ereg_replace("([0-9]{4})-([0-9]{2})-([0-9]{2})","\\3-\\2-\\1",$sDate);
	}
	
	function retStatut ()
	{
		return $this->oEnregBdd->StatutForm;
	}
	
	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutForm",$v_iStatut);
	}
	
	function retInscrSpontForm () { return $this->oEnregBdd->InscrSpontForm; }
	function retInscrAutoModules () { return $this->oEnregBdd->InscrAutoModules; }
	
	function defInscrAutoModules ($v_bInscrAutoModules=TRUE)
	{
		if (is_numeric($v_bInscrAutoModules))
			$this->mettre_a_jour("InscrAutoModules",$v_bInscrAutoModules);
	}
	
	function retInscrSpontEquipe () { return $this->oEnregBdd->InscrSpontEquipeF; }
	function retNbMaxDsEquipe () { return $this->oEnregBdd->NbMaxDsEquipeF; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreForm; }
	function retNom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->NomForm) : $this->oEnregBdd->NomForm); }
	
	function retNomParDefaut () { return INTITULE_FORMATION." sans nom"; }
	
	function retDescr ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->DescrForm) : $this->oEnregBdd->DescrForm);
	}
	
	function redistNumsOrdre ($v_iNouveauNumOrdre=NULL)
	{
		if (isset($v_iNouveauNumOrdre) && $v_iNouveauNumOrdre == $this->retNumOrdre())
			return FALSE;
		
		if (($cpt = $this->initFormations()) < 0)
			return FALSE;
		
		// *************************************
		// Ajouter dans ce tableau les ids et les numéros d'ordre
		// *************************************
		
		$aoNumsOrdre = array();
		
		for ($i=0; $i<$cpt; $i++)
			$aoNumsOrdre[$i] = array($this->aoFormations[$i]->retId(),$this->aoFormations[$i]->retNumOrdre());
		
		// *************************************
		// Mettre à jour dans la table avec les nouveaux numéros d'ordre
		// *************************************
		
		if ($v_iNouveauNumOrdre > 0)
		{
			// *************************************
			// Appel à une fonction externe pour une redistribution des numéros d'ordre
			// *************************************
			
			$aoNumsOrdre = redistNumsOrdre($aoNumsOrdre,$this->retNumOrdre(),$v_iNouveauNumOrdre);
			
			$iIdFormCourante = $this->retId();
			
			for ($i=0; $i<$cpt; $i++)
				if ($aoNumsOrdre[$i][0] != $iIdFormCourante)
					$this->mettre_a_jour("OrdreForm",$aoNumsOrdre[$i][1],$aoNumsOrdre[$i][0]);
			
			$this->defNumOrdre($v_iNouveauNumOrdre);
		}
		else
		{
			// Cette boucle est utilisée, par exemple, lorsqu'on efface une ligne de la table
			// et nous voulons simplement remettre de l'ordre (de 1 à n)
			for ($i=0; $i<$cpt; $i++)
				$this->mettre_a_jour("OrdreForm",($i+1),$aoNumsOrdre[$i][0]);
		}
		
		return TRUE;
	}
	
	function defNumOrdre ($v_iNumOrdre)
	{
		if (is_numeric($v_iNumOrdre))
			$this->mettre_a_jour("OrdreForm",$v_iNumOrdre);
	}
	
	function initFormationsEffacer ()
	{
		$iIdxForms = 0;
		
		$this->aoFormations = array();
		
		$sRequeteSql = "SELECT * FROM Formation"
			." WHERE StatutForm='".STATUT_EFFACE."'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormations[$iIdxForms] = new CFormation($this->oBdd);
			$this->aoFormations[$iIdxForms]->init($oEnreg);
			$iIdxForms++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxForms;
	}
	
	function verifEtudiant ($v_iIdPers)
	{
		$sRequeteSql = "SELECT COUNT(*) FROM Formation_Inscrit"
			." WHERE IdForm='".$this->retId()."'"
			." AND IdPers='{$v_iIdPers}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bVerifEtudiant = ($this->oBdd->retEnregPrecis($hResult) == 1);
		$this->oBdd->libererResult($hResult);
		return $bVerifEtudiant;
	}
	
	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdForm=0)
	{
		if ($v_iIdForm < 1)
			$v_iIdForm = $this->retId();
		
		if ($v_iIdForm < 1)
			return FALSE;
		
		$sRequeteSql = "UPDATE Formation SET"
			." {$v_sNomChamp}='".mysql_escape_string($v_mValeurChamp)."'"
			." WHERE IdForm='{$v_iIdForm}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function retType () { return $this->oEnregBdd->TypeForm; }
	function defType ($v_iType) { if (is_numeric($v_iType)) $this->mettre_a_jour("TypeForm",$v_iType); }
	
	/**
	 * Cette fonction initialise les informations de la personne qui a créé cette formation.
	 *
	 * \see CFormation::$oAuteur
	 */
	function initAuteur ()
	{
		if (is_object($this->oEnregBdd))
			$this->oAuteur = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers);
		else
			$this->oAuteur = NULL;
	}
	
	function defIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	
	// --------------------------------
	
	function verifResponsable ($v_iIdPers)
	{
		$bEstResponsable = FALSE;
		$iIdForm = $this->retId();
		
		if ($iIdForm > 0 && $v_iIdPers > 0)
		{
			$sRequeteSql = "SELECT Formation_Resp.*"
				." FROM Formation"
				." LEFT JOIN Formation_Resp USING (IdForm)"
				." WHERE Formation.IdForm='{$iIdForm}'"
				." AND Formation.StatutForm<>".STATUT_EFFACE
				." AND (Formation.IdPers='{$v_iIdPers}'"
				." OR Formation_Resp.IdPers='{$v_iIdPers}')"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($this->oBdd->retEnregSuiv($hResult))
				$bEstResponsable = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstResponsable;
	}
	
	/**
	 * Cette fonction vérifie que l'utilisateur a bien été inscrit en
	 * temps que concepteur à un cours.
	 *
	 * \param v_iIdPers numéro d'identifiant de l'utilisateur
	 * \param v_bAuMoinsUnCours l'utilisateur doit être au moins inscrit comme concepteur à un cours
	 * \return cette fonction retourne TRUE si l'utilisateur est un concepteur de cours
	 * ou FALSE dans le cas contraire.
	 */
	function verifConcepteur ($v_iIdPers,$v_bAuMoinsUnCours=TRUE)
	{
		$bEstConcepteur = FALSE;
		$iIdForm = $this->retId();
		
		if ($iIdForm > 0 && $v_iIdPers > 0)
		{
			if ($v_bAuMoinsUnCours)
				$sRequeteSql = "SELECT Module_Concepteur.IdPers"
					." FROM Module"
					." LEFT JOIN Module_Concepteur USING (IdMod)"
					." WHERE Module.IdForm='{$iIdForm}'"
					." AND Module_Concepteur.IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			else
				$sRequeteSql = "SELECT IdPers FROM Formation_Concepteur"
					." WHERE IdForm='{$iIdForm}'"
					." AND IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);		
			
			if ($this->oBdd->retEnregSuiv($hResult))
				$bEstConcepteur = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstConcepteur;
	}
	
	function verifTuteur ($v_iIdPers,$v_bAuMoinsUnCours=TRUE)
	{
		$bEstTuteur = FALSE;
		$iIdForm = $this->retId();
		
		if ($iIdForm > 0 && $v_iIdPers > 0)
		{
			if ($v_bAuMoinsUnCours)
				$sRequeteSql = "SELECT Module_Tuteur.IdPers"
					." FROM Module"
					." LEFT JOIN Module_Tuteur USING (IdMod)"
					." WHERE Module.IdForm='{$iIdForm}'"
					." AND Module_Tuteur.IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			else
				$sRequeteSql = "SELECT IdPers FROM Formation_Tuteur"
					." WHERE IdForm='{$iIdForm}'"
					." AND IdPers='{$v_iIdPers}'"
					." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($this->oBdd->retEnregSuiv($hResult))
				$bEstTuteur = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstTuteur;
	}
	
	function retStatutsUtilisateur ($v_iIdPers)
	{
		$oStatutUtilisateur = new CStatutUtilisateur($this->oBdd,$v_iIdPers);
		$oStatutUtilisateur->initStatuts($this->oEnregBdd->IdForm,0,$this->oEnregBdd->InscrAutoModules);
		return $oStatutUtilisateur->aiStatuts;
	}
	
	function retStatutHautUtilisateur ($v_iIdPers,$v_iStatutActuelUtilisateur=NULL)
	{
		$aiStatutsUtilisateur = $this->retStatutsUtilisateur($v_iIdPers);
		
		if ($v_iStatutActuelUtilisateur == NULL)
			$v_iStatutActuelUtilisateur = STATUT_PERS_ADMIN;
		
		for ($iIdxStatut=$v_iStatutActuelUtilisateur; $iIdxStatut<=STATUT_PERS_VISITEUR; $iIdxStatut++)
			if ($aiStatutsUtilisateur[$iIdxStatut])
				break;
		
		return $iIdxStatut;
	}
	
	// --------------------------------
	function retTypes ()
	{
		return array(array(0,INTITULE_FORMATION));
	}
	
	function retListeStatuts ()
	{
		return array(
			array(STATUT_FERME,"Fermé"),
			array(STATUT_OUVERT,"Ouvert"),
			array(STATUT_INVISIBLE,"Invisible"),
			array(STATUT_LECTURE_SEULE,"Clôturé")
			/*array(STATUT_ARCHIVE,"Archivé")*/);
	}
}

?>
