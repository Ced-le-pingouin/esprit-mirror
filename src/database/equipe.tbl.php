<?php

/*
** Fichier ................: equipe.tbl.php
** Description ............: 
** Date de création .......: 28/01/2003
** Dernière modification ..: 04/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("ids.class.php"));

/**
 * Cette classe...
 *
 * @class CEquipe
 */
class CEquipe
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	function CEquipe (&$v_oBdd,$v_iId=NULL,$v_bInitMembres=FALSE)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if (isset($this->iId))
		{
			$this->init();
			
			if ($v_bInitMembres)
				$this->initMembres();
		}
	}
	
	function init ($v_oEnregBdd=NULL)
	{
		if (isset($v_oEnregBdd))
		{
			$this->oEnregBdd = $v_oEnregBdd;
			$this->iId = $this->oEnregBdd->IdEquipe;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Equipe"
				." WHERE IdEquipe='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function verrouillerTables ($v_bExecuterRequete=TRUE)
	{
		$sListeTables = "Equipe WRITE, Equipe_Membre WRITE";
		if ($v_bExecuterRequete) $this->oBdd->executerRequete("LOCK TABLES {$sListeTables}");
		return $sListeTables;
	}
	
	function initEquipe ($v_iIdPers,$v_iIdNiveau,$v_iTypeNiveau,$v_bInitMembres=FALSE,$v_iIdNiveauDernier=TYPE_FORMATION)
	{
		$oIds = new CIds($this->oBdd,$v_iTypeNiveau,$v_iIdNiveau);
		$aiIds = $oIds->retTableIds();
		
		$bRemonter = TRUE;
		$asChampsNiveaux = array(NULL,"IdForm","IdMod","IdRubrique","IdActiv","IdSousActiv");
		
		for ($iIdxNiveau=$v_iTypeNiveau; $iIdxNiveau>=$v_iIdNiveauDernier; $iIdxNiveau--)
		{
			if ($aiIds[$iIdxNiveau] > 0 && isset($asChampsNiveaux[$iIdxNiveau]))
			{
				if ($bRemonter)
				{
					$sRequeteSql = "SELECT COUNT(*)"
						." FROM Equipe"
						." WHERE Equipe.".$asChampsNiveaux[$iIdxNiveau]."='".$aiIds[$iIdxNiveau]."'"
						.(isset($asChampsNiveaux[$iIdxNiveau+1]) ? " AND Equipe.".$asChampsNiveaux[$iIdxNiveau+1]."='0'" : NULL)
						." LIMIT 1";
					$hResult = $this->oBdd->executerRequete($sRequeteSql);
					$bRemonter = ($this->oBdd->retEnregPrecis($hResult) == 0);
					$this->oBdd->libererResult($hResult);
				}
				
				if ($bRemonter)
					continue;
				
				$sRequeteSql = "SELECT Equipe.*"
					." FROM Equipe_Membre"
					." LEFT JOIN Equipe USING (IdEquipe)"
					." WHERE Equipe_Membre.IdPers='{$v_iIdPers}'"
					." AND Equipe.".$asChampsNiveaux[$iIdxNiveau]."='".$aiIds[$iIdxNiveau]."'"
					.(isset($asChampsNiveaux[$iIdxNiveau+1]) ? " AND Equipe.".$asChampsNiveaux[$iIdxNiveau+1]."='0'" : NULL)
					." LIMIT 1";
				$hResult = $this->oBdd->executerRequete($sRequeteSql);
				
				if ($hResult !== FALSE && ($oEnreg = $this->oBdd->retEnregSuiv($hResult)))
				{
					$this->oBdd->libererResult($hResult);
					
					$this->init($oEnreg);
					
					if ($v_bInitMembres)
						$this->initMembres();
					
					return TRUE;
				}
				
				break;
			}
		}
		
		return FALSE;
	}
	
	function initEquipes ($v_iIdForm=NULL,$v_iIdMod=NULL,$v_iIdRubrique=NULL,$v_iIdActiv=NULL,$v_iIdSousActiv=NULL,$v_bInitMembres=FALSE)
	{
		$iIdxEquipe = 0;
		
		$this->aoEquipes = array();
		
		$sRequeteSql = "SELECT * FROM Equipe WHERE (1=1)"
			.(empty($v_iIdForm) ? NULL : " AND IdForm='{$v_iIdForm}'")
			.(empty($v_iIdMod) ? NULL : " AND IdMod='{$v_iIdMod}'")
			.(empty($v_iIdRubrique) ? NULL : " AND IdRubrique='{$v_iIdRubrique}'")
			.(empty($v_iIdActiv) ? NULL : " AND IdActiv='{$v_iIdActiv}'")
			.(empty($v_iIdSousActiv) ? NULL : " AND IdSousActiv='{$v_iIdSousActiv}'")
			." ORDER BY NomEquipe";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoEquipes[$iIdxEquipe] = new CEquipe($this->oBdd);
			$this->aoEquipes[$iIdxEquipe]->init($oEnreg);
			
			if ($v_bInitMembres)
				$this->aoEquipes[$iIdxEquipe]->initMembres();
			
			$iIdxEquipe++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxEquipe;
	}
	
	function initEquipesNiveau ($v_iTypeNiveau,$v_iIdNiveau,$v_bInitMembres=FALSE,$v_bNonEquipesEnfants=TRUE)
	{
		$iIdxEquipe = 0;
		$this->aoEquipes = array();
		
		if ($v_iTypeNiveau < 1 || $v_iIdNiveau < 1)
			return $iIdxEquipe;
		
		$asRecherche = array(NULL,"IdForm","IdMod","IdRubrique",NULL,"IdActiv","IdSousActiv",NULL);
		
		if (!isset($asRecherche[$v_iTypeNiveau]))
			return $iIdxEquipe;
		
		$sRequeteSql = "SELECT * FROM Equipe"
			." WHERE ".$asRecherche[$v_iTypeNiveau]."='{$v_iIdNiveau}'"
			.($v_bNonEquipesEnfants && isset($asRecherche[$v_iTypeNiveau+1])
				? " AND ".$asRecherche[$v_iTypeNiveau+1]."='0'"
				: NULL)
			." ORDER BY NomEquipe";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoEquipes[$iIdxEquipe] = new CEquipe($this->oBdd);
				$this->aoEquipes[$iIdxEquipe]->init($oEnreg);
				
				if ($v_bInitMembres)
					$this->aoEquipes[$iIdxEquipe]->initMembres();
				
				$iIdxEquipe++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxEquipe;
	}
	
	function initEquipesEx ($v_iIdNiveauDepart,$v_iTypeNiveauDepart,$v_bInitMembres=FALSE)
	{
		$oIds = new CIds($this->oBdd,$v_iTypeNiveauDepart,$v_iIdNiveauDepart);
		$aiIds = $oIds->retListeIds();
		
		// Rechercher les équipes par niveau
		for ($iIdxTypeNiveau=$v_iTypeNiveauDepart; $iIdxTypeNiveau>=TYPE_FORMATION; $iIdxTypeNiveau--)
		{
			if ($aiIds[$iIdxTypeNiveau] < 1)
				continue;
			
			if ($this->initEquipesNiveau($iIdxTypeNiveau,$aiIds[$iIdxTypeNiveau],$v_bInitMembres) > 0)
				break;
		}
		
		return count($this->aoEquipes);
	}
	
	// {{{ Membres
	function initMembres ()
	{
		$iIdxPers = 0;
		$this->aoMembres = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Equipe_Membre"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Equipe_Membre.IdEquipe='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoMembres[$iIdxPers] = new CPersonne($this->oBdd);
				$this->aoMembres[$iIdxPers]->init($oEnreg);
				$iIdxPers++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxPers;
	}
	
	function retNbMembres () { return (isset($this->aoMembres) && is_array($this->aoMembres) ? count($this->aoMembres) : 0); }
	
	function verifMembre ($v_iIdPers,$v_iIdEquipe=NULL)
	{
		$bVerifMembre = FALSE;
		
		if (empty($v_iIdEquipe))
			$v_iIdEquipe = $this->retId();
		
		if ($v_iIdPers > 0 && $v_iIdEquipe > 0)
		{
			$sRequeteSql = "SELECT IdPers FROM Equipe_Membre"
				." WHERE IdEquipe='{$v_iIdEquipe}'"
				." AND IdPers='{$v_iIdPers}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($hResult !== FALSE && $this->oBdd->retEnregSuiv($hResult))
				$bVerifMembre = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bVerifMembre;
	}
	// }}}
	
	function initGraceIdEquipes ($v_aiIdEquipes)
	{
		$iIdxEquipe = 0;
		$this->aoEquipes = array();
		
		foreach ($v_aiIdEquipes as $iIdEquipe)
			$this->aoEquipes[$iIdxEquipe++] = new CEquipe($this->oBdd,$iIdEquipe);
		
		return $iIdxEquipe;
	}
	
	// --------------------------------
	
	function ajouter ()
	{
		$sRequeteSql = "INSERT INTO Equipe SET"
			." IdEquipe=NULL"
			.", NomEquipe=\"".$this->retNom()."\""
			.", IdForm=".$this->retIdFormation()
			.", IdMod=".$this->retIdModule()
			.", IdRubrique=".$this->retIdRubrique()
			.", IdActiv=".$this->retIdActivite()
			.", IdSousActiv=".$this->retIdSousActivite()
			.", OrdreEquipe=".$this->retNumOrdre();
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
	}
	
	function effacer ()
	{
		$iIdEquipe = $this->retId();
		$sRequeteSql = "DELETE FROM Equipe_Membre WHERE IdEquipe='{$iIdEquipe}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$sRequeteSql = "DELETE FROM Equipe WHERE IdEquipe='{$iIdEquipe}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerParNiveau ($v_iNiveau,$v_iIdNiveau)
	{
		if ($v_iIdNiveau < 1)
			return;
		
		$sNomChamp = NULL;
		
		switch ($v_iNiveau)
		{
			case TYPE_RUBRIQUE: $sNomChamp = "IdRubrique"; break;
			case TYPE_MODULE: $sNomChamp = "IdMod"; break;
			case TYPE_FORMATION: $sNomChamp = "IdForm"; break;
		}
		
		if ($sNomChamp == NULL)
			return;
		
		$sValeursRequete = NULL;
		
		// Rechercher les équipes à effacer
		$sRequeteSql = "SELECT IdEquipe FROM Equipe"
			." WHERE {$sNomChamp}='{$v_iIdNiveau}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				."'{$oEnreg->IdEquipe}'";
		
		$this->oBdd->libererResult($hResult);
		
		if (isset($sValeursRequete))
		{
			// Effacer les enregistrements de la table "Equipe_Membre"
			$sRequeteSql = "DELETE FROM Equipe_Membre"
				." WHERE IdEquipe IN ({$sValeursRequete})";
			
			$this->oBdd->executerRequete($sRequeteSql);
			
			// Effacer les enregistrements de la table "Equipe"
			$sRequeteSql = "DELETE FROM Equipe"
				." WHERE IdEquipe IN ({$sValeursRequete})";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function sauvegarder ()
	{
		$sRequeteSql = "UPDATE Equipe SET"
			." NomEquipe=\"".$this->retNom()."\""
			.", IdForm=".$this->retIdFormation()
			.", IdMod=".$this->retIdModule()
			.", IdRubrique=".$this->retIdRubrique()
			.", IdActiv=".$this->retIdActivite()
			.", IdSousActiv=".$this->retIdSousActivite()
			.", OrdreEquipe=".$this->retNumOrdre()
			." WHERE IdEquipe='".$this->retId()."'";
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function ajouterMembres ($v_aiIdPers)
	{
		$oMembre = new CEquipe_Membre($this->oBdd,$this->retId());
		$oMembre->ajouterMembres($v_aiIdPers);
	}
	
	function defNom ($v_sNomEquipe) { $this->oEnregBdd->NomEquipe = $v_sNomEquipe; }
	
	function defIdFormation ($v_iIdFormation) { $this->oEnregBdd->IdForm = $v_iIdFormation; }
	function defIdModule ($v_iIdModule) { $this->oEnregBdd->IdMod = $v_iIdModule; }
	function defIdRubrique ($v_iIdRubrique) { $this->oEnregBdd->IdRubrique = $v_iIdRubrique; }
	function defIdActivite ($v_iIdActiv) { $this->oEnregBdd->IdActiv = $v_iIdActiv; }
	function defIdSousActivite ($v_iIdSousActiv) { $this->oEnregBdd->IdSousActiv = $v_iIdSousActiv; }
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function retNom ($v_sMode=NULL)
	{
		if ($v_sMode == "html")
			return htmlentities($this->oEnregBdd->NomEquipe);
		else if ($v_sMode == "url")
			return rawurlencode($this->oEnregBdd->NomEquipe);
		else
			return $this->oEnregBdd->NomEquipe;
	}
	
	function retIdFormation () { return (empty($this->oEnregBdd->IdForm) ? 0 : $this->oEnregBdd->IdForm); }
	function retIdModule () { return (empty($this->oEnregBdd->IdMod) ? 0 : $this->oEnregBdd->IdMod); }
	function retIdRubrique () { return (empty($this->oEnregBdd->IdRubrique) ? 0 : $this->oEnregBdd->IdRubrique); }
	function retIdActivite () { return (empty($this->oEnregBdd->IdActiv) ? 0 : $this->oEnregBdd->IdActiv); }
	function retIdSousActivite () { return (empty($this->oEnregBdd->IdSousActiv) ? 0 : $this->oEnregBdd->IdSousActiv); }
	function retNumOrdre () { return (empty($this->oEnregBdd->OrdreEquipe) ? 0 : $this->oEnregBdd->OrdreEquipe); }
	
	// --------------------------------
	
	function retLien ()
	{
		return "<a href=\"javascript: open('"
			.dir_admin("equipe","liste_equipes-index.php")
			."?idEquipe=".$this->retId()."'"
			.",'WIN_INFO_EQUIPE','resizable=1,width=600,height=450,status=0'); void(0);\""
			." title=\"".htmlentities("Cliquer ici pour voir les membres de cette équipe")."\""
			." onfocus=\"blur()\""
			.">".$this->retNom()."</a>";
	}
}

class CEquipe_Membre
{
	var $oBdd;
	var $iId;
	
	var $aoMembres;
	var $asNiveau;
	
	function CEquipe_Membre (&$v_oBdd,$v_iIdEquipe=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdEquipe;
		
		$this->asNiveau = array(NULL,"IdForm","IdMod",NULL,"IdRubrique","IdActiv","IdSousActiv");
		
		if (isset($this->iId))
			$this->init();
	}
	
	function init ()
	{
		$iIdxMembre = 0;
		
		$this->aoMembres = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Equipe_Membre"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Equipe_Membre.IdEquipe='".$this->retId()."'"
			." ORDER BY Personne.Nom ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoMembres[$iIdxMembre] = new CPersonne($this->oBdd);
			$this->aoMembres[$iIdxMembre]->init($oEnreg);
			$iIdxMembre++;
		}
		
		$this->oBdd->libererResult($hResult);
	}
	
	function initMembresDe ($v_iNiveau,$v_iIdNiveau,$v_bAppartenirEquipe=TRUE)
	{
		$iIdxMembre = 0;
		
		$this->aoMembres = array();
		
		$asIdParent = array(NULL,"IdForm","IdMod","IdRubrique","IdActiv","IdSousActiv",NULL);
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Formation_Inscrit"
			." LEFT JOIN Equipe_Membre ON Formation_Inscrit.IdPers=Equipe_Membre.IdPers"
			." LEFT JOIN Equipe ON Formation_Inscrit.IdForm=Equipe.IdForm"
			." LEFT JOIN Personne ON Formation_Inscrit.IdPers=Personne.IdPers"
			." WHERE Equipe.".$asIdParent[$v_iNiveau]."='".$v_iIdNiveau."'"
			.(isset($asIdParent[$v_iNiveau+1]) ? " AND Equipe.".$asIdParent[$v_iNiveau+1]."='0'" : NULL)
			." AND Equipe_Membre.IdEquipe IS".($v_bAppartenirEquipe ? " NOT" : NULL)." NULL"
			." GROUP BY Formation_Inscrit.IdPers"
			." ORDER BY Personne.Nom";
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
	
	function verifMembre ($v_iIdPers)
	{
		if ($v_iIdPers > 0 && is_array($this->aoMembres))
			foreach ($this->aoMembres as $oMembre)
				if ($oMembre->retId() == $v_iIdPers)
					return TRUE;
		return FALSE;
	}
	
	// --------------------------------
	
	function ajouterMembres ($v_aiIdPers)
	{
		settype($v_aiIdPers,"array");
		
		if (count($v_aiIdPers) < 1 || $this->iId < 1)
			return;
		
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				." ('{$this->iId}','{$iIdPers}','0')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "REPLACE INTO Equipe_Membre"
				." (IdEquipe,IdPers,OrdreEquipeMembre) VALUES {$sValeursRequete}";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function optimiserTable ()
	{
		$this->oBdd->executerRequete("OPTIMIZE TABLE Equipe_Membre");
	}
	
	function effacerMembre ($v_iIdPers,$v_iNiveau,$v_iIdNiveau)
	{
		if ($v_iIdPers < 1)
			return FALSE;
		
		$sRequeteSql = "SELECT Equipe.IdEquipe FROM Equipe"
			." LEFT JOIN Equipe_Membre USING (IdEquipe)"
			." WHERE Equipe_Membre.IdPers='{$v_iIdPers}'"
			." AND Equipe.".$this->asNiveau[$v_iNiveau]."='".$v_iIdNiveau."'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$sValeursRequete = NULL;
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				."'{$oEnreg->IdEquipe}'";
		
		$this->oBdd->libererResult($hResult);
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Equipe_Membre"
				." WHERE IdEquipe IN ({$sValeursRequete}) AND IdPers='{$v_iIdPers}'";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$this->optimiserTable();
		}
		
		return TRUE;
	}
	
	function effacerMembres ($v_aiIdPers)
	{
		settype($v_aiIdPers,"array");
		
		if (count($v_aiIdPers) < 1 || $this->iId < 1)
			return FALSE;
		
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? " OR" : NULL)
				." (IdEquipe='{$this->iId}' AND IdPers='{$iIdPers}')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Equipe_Membre WHERE {$sValeursRequete}";
			$this->oBdd->executerRequete($sRequeteSql);
			$this->optimiserTable();
		}
		
		return TRUE;
	}
	
	function STRING_LOCK_TABLES () { return "Equipe WRITE, Equipe_Membre WRITE"; }
}

?>
