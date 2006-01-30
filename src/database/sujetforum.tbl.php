<?php

/*
** Fichier ................: sujetforum.tbl.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 16/12/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("messageforum.tbl.php"));

class CSujetForum
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iId;
	
	var $aoMessages;	// Liste des messages
	var $oAuteur;		// Auteur du sujet
	
	var $sRepRessources;
	
	function CSujetForum (&$v_oBdd,$v_iId=NULL)
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
			$this->iId = $this->oEnregBdd->IdSujetForum;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM SujetForum"
				." WHERE IdSujetForum='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initAuteur ()
	{
		if (is_numeric($this->oEnregBdd->IdPers))
			$this->oAuteur = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers);
		else
			$this->oAuteur = NULL;
	}
	
	function initMessages ($v_iIdEquipe=0)
	{
		$iIdxMessage = 0;
		$this->aoMessages = array();
		
		$sRequeteSql = "SELECT MessageForum.*"
			." FROM MessageForum"
			.($v_iIdEquipe > 0
				? " LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				: NULL)
			." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
			.($v_iIdEquipe > 0
				? " AND (MessageForum_Equipe.IdEquipe IS NULL OR MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}' )"
				: NULL)
			." ORDER BY MessageForum.DateMessageForum DESC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoMessages[$iIdxMessage] = new CMessageForum($this->oBdd);
			$this->aoMessages[$iIdxMessage]->init($oEnregBdd);
			$iIdxMessage++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxMessage;
	}
	
	/**
	 * Cette méthode retourne le nombre de messages que la personne aura
	 * déposés pour ce sujet.
	 *
	 * @param v_iIdPers integer Identifiant unique de la personne
	 * @param v_iIdEquipe integer Identifiant unique de l'équipe
	 * @return Retourne le nombre de messages de ce sujet de la personne désiré.
	 */
	function retNbMessagesDeposesPersonne ($v_iIdPers,$v_iIdEquipe=0)
	{
		if ($v_iIdEquipe > 0)
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM MessageForum"
				." LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
				." AND (MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}'"
				." OR MessageForum_Equipe.IdEquipe IS NULL)"
				." AND MessageForum.IdPers='{$v_iIdPers}'";
		else
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM MessageForum"
				." WHERE IdSujetForum='".$this->retId()."'"
				." AND IdPers='{$v_iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbMessagesDeposesPersonne = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNbMessagesDeposesPersonne;
	}
	
	function ajouter ($v_sTitreSujet,$v_iModaliteSujet,$v_iStatutSujet,$v_bAccessibleVisiteur,$v_iIdForum,$v_iIdPers)
	{
		$sRequeteSql = "INSERT INTO SujetForum SET"
			." IdSujetForum=NULL"
			.", TitreSujetForum='".MySQLEscapeString($v_sTitreSujet)."'"
			.", DateSujetForum=NOW()"
			.", ModaliteSujetForum=".(isset($v_iModaliteSujet) ? "'{$v_iModaliteSujet}'" : "'0'")
			.", StatutSujetForum=".(isset($v_iStatutSujet) ? "'{$v_iStatutSujet}'" : "'0'")
			.", AccessibleVisiteursSujetForum=".(isset($v_bAccessibleVisiteur) ? "'{$v_bAccessibleVisiteur}'" : "'1'")
			.", IdForum='{$v_iIdForum}'"
			.", IdPers='{$v_iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId($hResult);
		$this->init();
		return $this->iId;
	}
	
	function enregistrer ()
	{
		$sRequeteSql = "UPDATE SujetForum SET"
			." TitreSujetForum='".MySQLEscapeString($this->oEnregBdd->TitreSujetForum)."'"
			.", ModaliteSujetForum='".$this->oEnregBdd->ModaliteSujetForum."'"
			.", StatutSujetForum='".$this->oEnregBdd->StatutSujetForum."'"
			.", AccessibleVisiteursSujetForum='".$this->oEnregBdd->AccessibleVisiteursSujetForum."'"
			.", StatutSujetForum='".$this->oEnregBdd->StatutSujetForum."'"
			." WHERE IdSujetForum='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacer ()
	{
		$this->verrouillerTables();
		$this->effacerSujet();
		$this->verrouillerTables(FALSE);
	}
	
	function effacerSujet ()
	{
		$this->effacerEquipesAssocieesSujet();
		$this->effacerMessages();
		
		$sRequeteSql = "DELETE FROM SujetForum"
			." WHERE IdSujetForum='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerMessages ()
	{
		$this->initMessages();
		$sRepRessources = $this->retRepRessources();
		foreach ($this->aoMessages as $oMessage)
		{
			$oMessage->defRepRessources($sRepRessources);
			$oMessage->effacer();
		}
	}
	
	function effacerEquipesAssocieesSujet ()
	{
		$sRequeteSql = "DELETE FROM SujetForum_Equipe"
			." WHERE IdSujetForum='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function retRepRessources () { return (empty($this->sRepRessources) ? NULL : $this->sRepRessources); }
	function defRepRessources ($v_sRepRessources) { $this->sRepRessources = $v_sRepRessources; }
	
	function verrouillerTables ($v_bVerrouillerTables=TRUE)
	{
		// Vérrouiller les tables
		if ($v_bVerrouillerTables)
			$sRequeteSql = "LOCK TABLES ".$this->STRING_LOCK_TABLES();
		else
			$sRequeteSql = "UNLOCK TABLES";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/*:08/10/2004:function effacer ()
	{
		// Effacer les messages de ce sujet
		$this->effacerMessages();
		
		// Effacer le sujet
		$sRequeteSql = "DELETE FROM SujetForum"
			." WHERE IdSujetForum='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (rand(0,20) == 0)
			$this->oBdd->executerRequete("OPTIMIZE TABLE SujetForum");
	}*/
	
	/*:08/10/2004:function effacerMessages ()
	{
		$sRequeteSql = "DELETE FROM MessageForum"
			." WHERE IdSujetForum='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (rand(0,20) == 0)
			$this->oBdd->executerRequete("OPTIMIZE TABLE MessageForum");
	}*/
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retIdParent () { return (is_numeric($this->oEnregBdd->IdForum) ? $this->oEnregBdd->IdForum : 0); }
	function retIdPers () { return (is_numeric($this->oEnregBdd->IdPers) ? $this->oEnregBdd->IdPers : 0); }
	
	function defTitre ($v_sTitre) { $this->oEnregBdd->TitreSujetForum = $v_sTitre; }
	function retTitre () { return $this->oEnregBdd->TitreSujetForum; }
	function retNom () { return $this->oEnregBdd->TitreSujetForum; }
	
	function defModalite ($v_iIdModalite) { $this->oEnregBdd->ModaliteSujetForum = $v_iIdModalite; }
	function retModalite () { return $this->oEnregBdd->ModaliteSujetForum; }
	
	function defStatut ($v_iIdStatut) { $this->oEnregBdd->StatutSujetForum = $v_iIdStatut; }
	function retStatut () { return $this->oEnregBdd->StatutSujetForum; }
	
	function retTexteModalite ()
	{
		$iModalite = $this->oEnregBdd->ModaliteSujetForum;
		
		if ($iModalite == MODALITE_IDEM_PARENT)
		{
			$oForum = new CForum($this->oBdd,$this->oEnregBdd->IdForum);
			$iModalite = $oForum->retModalite();
			$oForum = NULL;
		}
		
		return ($iModalite == MODALITE_POUR_TOUS
			? "Pour&nbsp;tous"
			: "Par&nbsp;&eacute;quipe");
	}
	
	function retNombreMessages ($v_iIdEquipe=0)
	{
		if ($v_iIdEquipe > 0)
			$sRequeteSql = "SELECT COUNT(*) FROM MessageForum"
				." LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
				." AND (MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}'"
				." OR MessageForum_Equipe.IdEquipe IS NULL)";
		else
			$sRequeteSql = "SELECT COUNT(*) FROM MessageForum"
				." WHERE IdSujetForum='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbMessages = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iNbMessages;
	}
	
	function retDate ($v_sFormatterDate="d/m/y H:i") { return formatterDate($this->oEnregBdd->DateSujetForum,$v_sFormatterDate); }
	function retForum () { return new CForum($this->oBdd,$this->retIdParent()); }
	
	function estPourTous ()
	{
		$bEstPourTous = FALSE;
		
		$sRequeteSql = "SELECT SujetForum.IdSujetForum"
			.", SujetForum_Equipe.IdSujetForum AS IdSujetForumEquipe"
			." FROM Forum"
			." LEFT JOIN SujetForum USING (IdForum)"
			." LEFT JOIN SujetForum_Equipe USING (IdSujetForum)"
			." WHERE Forum.IdForum='".$this->retIdParent()."'"
			." AND Forum.ModaliteForum<>'".MODALITE_POUR_TOUS."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			$iIdSujetForum = $this->retId();
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				if ($oEnreg->IdSujetForum == $iIdSujetForum && empty($oEnreg->IdSujetForumEquipe))
				{
					$bEstPourTous = TRUE;
					break;
				}
			}
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstPourTous;
	}
	
	function retDateDernierMessagePoster ($v_sFormatterDate="d/m/y H:i",$v_iIdEquipe=0)
	{
		if ($v_iIdEquipe > 0)
			$sRequeteSql = "SELECT"
				." MAX(MessageForum.DateMessageForum) AS DateDernierMessagePoster"
				." FROM MessageForum"
				." LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
				." AND (MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}'"
				." OR MessageForum_Equipe.IdEquipe IS NULL)";
		else
			$sRequeteSql = "SELECT"
				." MAX(DateMessageForum) AS DateDernierMessagePoster"
				." FROM MessageForum"
				." WHERE IdSujetForum='".$this->retId()."'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		
		if (isset($oEnregBdd->DateDernierMessagePoster))
			return formatterDate($oEnregBdd->DateDernierMessagePoster,$v_sFormatterDate);
		else
			return "&#8212;";
	}
	
	function defAccessibleVisiteurs ($v_iAccessibleVisiteurs) { $this->oEnregBdd->AccessibleVisiteursSujetForum = $v_iAccessibleVisiteurs; }
	function retAccessibleVisiteurs () { return $this->oEnregBdd->AccessibleVisiteursSujetForum; }
	
	function defNumOrdre ($v_iNumOrdre) { $this->oEnregBdd->OrdreSujetForum = $v_iNumOrdre; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreSujetForum; }
	
	function associerEquipe ($v_iIdEquipe)
	{
		$iIdSujetForum = $this->retId();
		if ($iIdSujetForum < 1 || $v_iIdEquipe < 1) return FALSE;
		$this->oBdd->executerRequete("REPLACE INTO SujetForum_Equipe (IdSujetForum,IdEquipe) VALUES ('$iIdSujetForum','$v_iIdEquipe')");
	}
	
	function STRING_LOCK_TABLES ()
	{
		return "SujetForum WRITE, SujetForum_Equipe WRITE"
			.", ".CMessageForum::STRING_LOCK_TABLES();
	}
}

?>
