<?php

/*
** Fichier ................: forum.tbl.php
** Description ............:
** Date de cr�ation .......: 14/05/2004
** Derni�re modification ..: 05/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           J�r�me TOUZE
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("forum_prefs.tbl.php"));
require_once(dir_database("sujetforum.tbl.php"));

class CForum
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iId;
	
	var $aoForumsPrefs;
	var $aoSousForums;
	var $aoSujets;
	
	var $iIdTypeForum;
	
	function CForum (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdForum;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Forum"
				." WHERE IdForum='{$this->iId}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function detruire ()
	{
		$this->oBdd = NULL;
		$this->oEnregBdd = NULL;
		$this->iId = NULL;
		$this->aoSousForums = NULL;
		$this->aoSujets = NULL;
		$this->iIdTypeForum = NULL;
	}
	
	function initForumParType ($v_iType,$v_iIdType)
	{
		$sRequeteSql = NULL;
		$this->iId = NULL;
		$this->oEnregBdd = NULL;
		
		if ($v_iType < 1 || $v_iIdType < 1)
			return;
		
		$this->iIdTypeForum = $v_iType;
		
		switch ($this->iIdTypeForum)
		{
			case TYPE_RUBRIQUE:
				$sRequeteSql = "SELECT * FROM Forum"
					." WHERE IdRubrique='{$v_iIdType}'"
					." LIMIT 1";
				break;
			case TYPE_SOUS_ACTIVITE:
				$sRequeteSql = "SELECT * FROM Forum"
					." WHERE IdSousActiv='{$v_iIdType}'"
					." LIMIT 1";
				break;
		}
		
		if (isset($sRequeteSql))
		{
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->init($this->oBdd->retEnregSuiv($hResult));
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function retDernierSujets ()
	{
		$oSujetForum = NULL;
		
		$sRequeteSql = "SELECT * FROM SujetForum"
			." WHERE IdForum='".$this->retId()."'"
			." ORDER BY DateSujetForum DESC"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$oSujetForum = new CSujetForum($this->oBdd);
			$oSujetForum->init($oEnregBdd);
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $oSujetForum;
	}
	
	function retNumOrdreMax ($v_iIdMod,$v_iIdRubrique,$v_iIdSousActiv)
	{
		$iNumOrdreMax = 1;
		$sRecherche = NULL;
		
		if ($v_iIdMod > 0)
			$sRecherche = "IdMod='{$v_iIdMod}'";
		else if ($v_iIdRubrique > 0)
			$sRecherche = "IdRubrique='{$v_iIdRubrique}'";
		else if ($v_iIdSousActiv > 0)
			$sRecherche = "IdSousActiv='{$v_iIdSousActiv}'";
		
		if (isset($sRecherche))
		{
			$sRequeteSql = "SELECT MAX(OrdreForum) FROM Forum"
				." WHERE {$sRecherche}";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNumOrdreMax += $this->oBdd->retEnregPrecis($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		return $iNumOrdreMax;
	}
	
	/**
	 * Permet d'ajouter un nouveau forum dans la table
	 *
	 * @param v_sNomForum                 string  Nom du forum
	 * @param v_iModaliteForum            integer Modalit� du forum {MODALITE_IDEM_PARENT | MODALITE_POUR_TOUS | MODALITE_PAR_EQUIPE}
	 * @param v_iStatutForum              integer Statut du forum {STATUT_OUVERT | STATUT_LECTURE_SEULE | STATUT_FERME | STATUT_INVISIBLE}
	 * @param v_bAccessibleVisiteursForum boolean Ce forum est-il accessible aux visiteurs ?
	 * @param v_iIdMod                    integer Num�ro d'identifiant du module
	 * @param v_iIdRubrique               integer Num�ro d'identifiant de la rubrique
	 * @param v_iIdSousActiv              integer Num�ro d'identifiant de la sous-activit�
	 * @param v_iIdForumParent            integer Num�ro d'identifiant du forum parent
	 * @param v_iIdPers                   integer Num�ro d'identifiant de l'auteur du forum
	 * @return Retourne le nouveau num�ro d'identifiant du forum
	 */
	function ajouter ($v_sNomForum,$v_iModaliteForum,$v_iStatutForum,$v_bAccessibleVisiteursForum,$v_iIdMod,$v_iIdRubrique,$v_iIdSousActiv,$v_iIdForumParent,$v_iIdPers)
	{
		if (strlen($v_sNomForum) < 1)
			return;
		
		$sRequeteSql = "INSERT INTO Forum SET"
			." IdForum=NULL"
			.", NomForum='".MySQLEscapeString($v_sNomForum)."'"
			.", DateForum=NOW()"
			.", ModaliteForum='{$v_iModaliteForum}'"
			.", StatutForum='{$v_iStatutForum}'"
			.", AccessibleVisiteursForum='{$v_bAccessibleVisiteursForum}'"
			.", OrdreForum='".$this->retNumOrdreMax($v_iIdMod,$v_iIdRubrique,$v_iIdSousActiv)."'"
			.", IdForumParent='{$v_iIdForumParent}'"
			.", IdMod='{$v_iIdMod}'"
			.", IdRubrique='{$v_iIdRubrique}'"
			.", IdSousActiv='{$v_iIdSousActiv}'"
			.", IdPers='{$v_iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
		return $this->iId;
	}
	
	function enregistrer ()
	{
		if ($this->iId < 1)
			return;
		
		$sRequeteSql = "UPDATE Forum SET"
			." NomForum='".MySQLEscapeString($this->oEnregBdd->NomForum)."'"
			.", ModaliteForum='".$this->oEnregBdd->ModaliteForum."'"
			.", StatutForum='".$this->oEnregBdd->StatutForum."'"
			.", AccessibleVisiteursForum='".$this->oEnregBdd->AccessibleVisiteursForum."'"
			." WHERE IdForum='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function copier ()
	{
		$this->ajouter($this->retNom()
			,$this->retModalite()
			,$this->retStatut()
			,$this->retAccessibleVisiteurs()
			,0
			,0
			,$this->retId()
			,0
			,$this->retIdPers()
		);
	}
	
	function effacer ()
	{
		$this->verrouillerTables();
		$this->effacerForum();
		$this->verrouillerTables(FALSE);
	}
	
	function effacerForum ()
	{
		// Effacer tous les sous-forums
		//$this->effacerSousForums();
		
		// Effacer les pr�f�rences du forum des utilisateurs
		//$this->effacerForumsPrefs();
		
		// Effacer les sujets de ce forum
		$this->effacerSujets();
		
		// Effacer ce forum
		$sRequeteSql = "DELETE FROM Forum"
			." WHERE IdForum='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerForumsPrefs ()
	{
		$this->initForumsPrefs();
		
		foreach ($this->aoForumsPrefs as $oForumPrefs)
			$oForumPrefs->effacerForumPrefs();
	}
	
	function effacerSousForums ()
	{
		$this->initSousForums();
		
		foreach ($this->aoSousForums as $oSousForum)
			$oSousForum->effacer();
	}
	
	function effacerSujets ()
	{
		// Rechercher tous les sujets
		$this->initSujets();
		
		// Effacer tous les sujets de ce forum
		foreach ($this->aoSujets as $oSujet)
			$oSujet->effacer();
	}
	
	function initSousForums ()
	{
		$iIdxSousForum = 0;
		$this->aoSousForums = array();
		
		if ($this->iId > 0)
		{
			$sRequeteSql = "SELECT * FROM Forum"
				." WHERE IdForumParent='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoSousForums[$iIdxSousForum] = new CForum($this->oBdd);
				$this->aoSousForums[$iIdxSousForum]->init($oEnregBdd);
				$iIdxSousForum++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxSousForum;
	}
	
	/**
	 * Rechercher tous les sujets qui appartiennent � ce forum.
	 *
	 * @return Retourne le nombre de sujets trouv�s
	 * @see CSujet
	 */
	function initSujets ($v_iIdEquipe=0)
	{
		$iIdxSujet = 0;
		$this->aoSujets = array();
		
		$sRequeteSql = "SELECT SujetForum.* FROM SujetForum"
			.($v_iIdEquipe > 0
				? " LEFT JOIN SujetForum_Equipe USING (IdSujetForum)"
				: NULL)
			." WHERE SujetForum.IdForum='".$this->retId()."'"
			.($v_iIdEquipe > 0
				? " AND (SujetForum_Equipe.IdEquipe IS NULL OR SujetForum_Equipe.IdEquipe='{$v_iIdEquipe}')"
				: NULL)
			." ORDER BY SujetForum.DateSujetForum DESC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoSujets[$iIdxSujet] = new CSujetForum($this->oBdd);
			$this->aoSujets[$iIdxSujet]->init($oEnregBdd);
			$iIdxSujet++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxSujet;
	}
	
	function initForumsPrefs ()
	{
		$oForumPrefs = new CForumPrefs($this->oBdd);
		$oForumPrefs->initForumsPrefs($this->retId());
		$this->aoForumsPrefs = $oForumPrefs->aoForumsPrefs;
		return count($this->aoForumsPrefs);
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retIdParent () { return $this->retIdNiveau(); }
	
	function retIdNiveau ()
	{
		if (is_object($this->oEnregBdd))
		{
			if ($this->oEnregBdd->IdMod > 0) return $this->oEnregBdd->IdMod;
			else if ($this->oEnregBdd->IdRubrique > 0) return $this->oEnregBdd->IdRubrique;
			else if ($this->oEnregBdd->IdSousActiv > 0) return $this->oEnregBdd->IdSousActiv;
		}
		return 0;
	}
	
	function retTypeNiveau ()
	{
		if (is_object($this->oEnregBdd))
		{
			if ($this->oEnregBdd->IdMod > 0) return TYPE_MODULE;
			else if ($this->oEnregBdd->IdRubrique > 0) return TYPE_RUBRIQUE;
			else if ($this->oEnregBdd->IdSousActiv > 0) return TYPE_SOUS_ACTIVITE;
		}
		
		return TYPE_INCONNU;
	}
	
	function defNom ($v_sNomForum) { $this->oEnregBdd->NomForum = $v_sNomForum; }
	function retNom ()
	{
		if (is_string($this->oEnregBdd->NomForum))
			return $this->oEnregBdd->NomForum;
		else
			return NULL;
	}
	
	function retModalite () { return (is_object($this->oEnregBdd) ? $this->oEnregBdd->ModaliteForum : MODALITE_POUR_TOUS); }
	function defModalite ($v_iIdModalite) { $this->oEnregBdd->ModaliteForum = $v_iIdModalite; }
	
	function retTexteModalite ($v_iIdModalite=NULL)
	{
		if (empty($v_iIdModalite))
			$v_iIdModalite = $this->retModalite();
		
		$aamModalites = $this->retListeModalites();
		
		foreach ($aamModalites as $amModalite)
			if ($amModalite[0] == $v_iIdModalite)
				return $amModalite[1];
		
		return $aamModalites[0][1];
	}
	
	function estForumParEquipe () { return (MODALITE_POUR_TOUS != $this->retModalite()); }
	
	function retListeModalites ()
	{
		$iModalite = $this->retModalite();
		return array(
			array(MODALITE_POUR_TOUS,"Pour tout le monde",($iModalite == MODALITE_POUR_TOUS))
			, array(MODALITE_PAR_EQUIPE,"Par �quipe (isol�e)",($iModalite == MODALITE_PAR_EQUIPE))
			, array(MODALITE_PAR_EQUIPE_INTERCONNECTEE,"Par �quipe (interconnect�e)",($iModalite == MODALITE_PAR_EQUIPE_INTERCONNECTEE))
			, array(MODALITE_PAR_EQUIPE_COLLABORANTE,"Par �quipe (collaborante)",($iModalite == MODALITE_PAR_EQUIPE_COLLABORANTE)));
	}
	
	function defStatut ($v_iIdStatut) { $this->oEnregBdd->StatutForum = $v_iIdStatut; }
	function retStatut () { return $this->oEnregBdd->StatutForum; }
	
	function defAccessibleVisiteurs ($v_bAccessibleVisiteurs) { $this->oEnregBdd->AccessibleVisiteursForum = $v_bAccessibleVisiteurs; }
	function retAccessibleVisiteurs () { return (is_object($this->oEnregBdd) ? $this->oEnregBdd->AccessibleVisiteursForum : TRUE); }
	
	function retIdPers () { return (is_numeric($this->oEnregBdd->IdPers) ? $this->oEnregBdd->IdPers : 0); }
	
	function verrouillerTables ($v_bVerrouillerTables=TRUE)
	{
		// V�rrouiller les tables
		if ($v_bVerrouillerTables)
			$sRequeteSql = "LOCK TABLES ".$this->STRING_LOCK_TABLES();
		else
			$sRequeteSql = "UNLOCK TABLES";
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function supprimer ()
	{
		// Effacer les sujets de ce forum
		$this->supprimerSujets();
		
		// Effacer ce forum
		$sRequeteSql = "DELETE FROM Forum"
			. " WHERE IdForum='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function supprimerSujets ()
	{
		// Rechercher tous les sujets de ce forum
		$this->initSujets();
		
		// Effacer les sujets de ce forum
		foreach($this->aoSujets as $oSujet)
			$oSujet->supprimer();
	}
	
	function retNombreSujets ($v_iIdPers=NULL)
	{
		$sRequeteSql = "SELECT COUNT(*) FROM SujetForum"
			." WHERE IdForum='".$this->retId()."'"
			.(isset($v_iIdPers) && $v_iIdPers > 0 ? " AND IdPers='{$v_iIdPers}'" : NULL);
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbSujet = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNbSujet;
	}
	
	function retNbMessages ($v_iIdPers=NULL)
	{
		$amNbMessages = array();
		
		$sRequeteSql = "SELECT COUNT(*) AS NbMessagesForum"
				.", MAX(MessageForum.DateMessageForum) AS DateDernierMessage"
			." FROM Forum"
			." LEFT JOIN SujetForum USING (IdForum)"
			." LEFT JOIN MessageForum USING (IdSujetForum)"
			." WHERE Forum.IdForum='".$this->retId()."'"
				.(isset($v_iIdPers) ? " AND MessageForum.IdPers='{$v_iIdPers}'" : NULL);
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		foreach ($oEnreg as $sCle => $sValeur)
			$amNbMessages[$sCle] = $sValeur;
		$this->oBdd->libererResult($hResult);
		return $amNbMessages;
	}
	
	function retDateDernierMessage () { return (isset($this->oEnregBdd->DateDernierMessage) ? $this->oEnregBdd->DateDernierMessage : NULL); }
	
	function STRING_LOCK_TABLES ()
	{
		return "Forum WRITE"
			.", ".CForumPrefs::STRING_LOCK_TABLES()
			.", ".CSujetForum::STRING_LOCK_TABLES();
	}
}

?>
