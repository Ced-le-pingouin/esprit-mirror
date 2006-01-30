<?php

/*
** Fichier ................: galerie.tbl.php
** Description ............: 
** Date de création .......: 28/10/2002
** Dernière modification ..: 07/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CGalerie
{
	var $iId;
	
	var $oBdd;
	
	var $aoCollecticiels;
	var $aoDocuments;
	
	function CGalerie (&$v_oBdd,$v_iId=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
	}
	
	function nettoyer ()
	{
		if ($this->iId < 0)
			return;
		
		$sRequeteSql = "DELETE FROM SousActiv_SousActiv"
			." WHERE IdSousActiv='{$this->iId}'";
		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function effacer ()
	{
		$sRequeteSql = "LOCK TABLES"
			." SousActiv_SousActiv WRITE"
			.", SousActiv_Ressource_SousActiv WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->effacerToutesRessources();
		$this->effacerGalerie();
		
		$this->oBdd->deverrouillerTables();
	}
	
	function effacerGalerie ()
	{
		$sRequeteSql = "DELETE FROM SousActiv_SousActiv"
			." WHERE IdSousActiv='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	// {{{ Collecticiels associés à la galerie
	/**
	 * @param $v_bInitRessources boolean
	 * @param $v_bToutesRessources boolean Rechercher les ressources attachées à cette galerie + ceux qui ne sont pas attachées
	 */
	function initCollecticiels ($v_bInitRessources=FALSE,$v_bToutesRessources=FALSE)
	{
		$iIdxCollecticiel = 0;
		
		$this->aoCollecticiels = array();
		
		$sRequeteSql = "SELECT SousActiv.*"
			." FROM SousActiv_SousActiv"
			." LEFT JOIN SousActiv"
				." ON SousActiv.IdSousActiv=SousActiv_SousActiv.IdSousActivRef"
			." WHERE SousActiv_SousActiv.IdSousActiv='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoCollecticiels[$iIdxCollecticiel] = new CSousActiv($this->oBdd);
			$this->aoCollecticiels[$iIdxCollecticiel]->init($oEnreg);
			
			if ($v_bInitRessources)
			{
				$this->initRessources($this->aoCollecticiels[$iIdxCollecticiel]->retId(),$v_bToutesRessources);
				$this->aoCollecticiels[$iIdxCollecticiel]->aoRessources = $this->aoRessources;
				unset($this->aoRessources);
			}
			
			$iIdxCollecticiel++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxCollecticiel;
	}
	
	function ajouterCollecticiels ($v_aiIdSousActiv)
	{
		if ($this->iId < 0 || (($iNbrAjouter = count($v_aiIdSousActiv)) < 1))
			return;
		
		$sValeursRequete = NULL;
		
		for ($i=0; $i<$iNbrAjouter; $i++)
			$sValeursRequete .= ($i > 0 ? ", " : NULL)
				." ('{$this->iId}','{$v_aiIdSousActiv[$i]}')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "INSERT INTO SousActiv_SousActiv"
				." (IdSousActiv,IdSousActivRef)"
				." VALUES {$sValeursRequete}";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function estAssocierGalerie ($v_iIdCollecticiel)
	{
		if ($v_iIdCollecticiel > 0)
			for ($i=0; $i<count($this->aoCollecticiels); $i++)
				if ($this->aoCollecticiels[$i]->retId() == $v_iIdCollecticiel)
					return TRUE;
		
		return FALSE;
	}
	
	function effacerCollecticiels ()
	{
		$sRequeteSql = "DELETE FROM SousActiv_SousActiv"
			." WHERE IdSousActiv='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	// }}}
	
	// {{{ Ressources
	function ajouterRessources ($v_aiIdSousActiv,$v_bEffacerRessources=TRUE)
	{
		if (($iIdSousActiv = $this->retId()) > 0)
		{
			if ($v_bEffacerRessources)
				$this->effacerToutesRessources();
			
			$sValeursRequete = NULL;
			
			foreach ($v_aiIdSousActiv as $iIdResSousActiv)
				$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
					."('{$iIdSousActiv}','{$iIdResSousActiv}')";
			
			if (isset($sValeursRequete))
			{
				$sRequeteSql = "REPLACE INTO SousActiv_Ressource_SousActiv"
					." (IdSousActiv,IdResSousActiv)"
					." VALUES {$sValeursRequete}";
				$this->oBdd->executerRequete($sRequeteSql);
			}
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	function initRessources ($v_iIdSousActiv=NULL,$v_bToutesRessources=FALSE)
	{
		$iId = $this->retId();
		
		$iIdxRessource = 0;
		
		$this->aoRessources = array();
		
		if (empty($v_iIdSousActiv))
			// Rechercher toutes les ressources attachées à cette galerie
			$sRequeteSql = "SELECT Ressource_SousActiv.*"
				.", Ressource.*"
				." FROM SousActiv_Ressource_SousActiv"
				." LEFT JOIN Ressource_SousActiv USING (IdResSousActiv)"
				." LEFT JOIN Ressource USING (IdRes)"
				." WHERE SousActiv_Ressource_SousActiv.IdSousActiv='{$iId}'";
		else if ($v_bToutesRessources)
			$sRequeteSql = "SELECT Ressource_SousActiv.*"
					.", Ressource.*"
					.", SousActiv_Ressource_SousActiv.IdSousActiv AS estSelectionne"
					." FROM SousActiv_SousActiv"
					." LEFT JOIN Ressource_SousActiv ON SousActiv_SousActiv.IdSousActivRef=Ressource_SousActiv.IdSousActiv"
					." LEFT JOIN SousActiv_Ressource_SousActiv"
						." ON Ressource_SousActiv.IdResSousActiv=SousActiv_Ressource_SousActiv.IdResSousActiv"
						." AND SousActiv_Ressource_SousActiv.IdSousActiv='{$iId}'"
					." LEFT JOIN Ressource ON Ressource_SousActiv.IdRes=Ressource.IdRes"
					." WHERE SousActiv_SousActiv.IdSousActivRef='{$v_iIdSousActiv}'"
						." AND SousActiv_SousActiv.IdSousActiv='{$iId}'";
		else
			// Rechercher toutes les ressources attachées à un collecticiel
			$sRequeteSql = "SELECT Ressource_SousActiv.*"
				.", Ressource.*"
				." FROM SousActiv_Ressource_SousActiv"
				." LEFT JOIN Ressource_SousActiv"
					." ON SousActiv_Ressource_SousActiv.IdResSousActiv=Ressource_SousActiv.IdResSousActiv"
				." LEFT JOIN Ressource USING (IdRes)"
				." WHERE SousActiv_Ressource_SousActiv.IdSousActiv='{$iId}'"
				." AND Ressource_SousActiv.IdSousActiv='{$v_iIdSousActiv}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoRessources[$iIdxRessource] = new CRessourceSousActiv($this->oBdd);
			$this->aoRessources[$iIdxRessource]->init($oEnreg);
			$this->aoRessources[$iIdxRessource]->estSelectionne = (!$v_bToutesRessources | (isset($oEnreg->estSelectionne) && $oEnreg->estSelectionne > 0));
			$iIdxRessource++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxRessource;
	}
	
	function effacerToutesRessources ()
	{
		$sRequeteSql = "DELETE FROM SousActiv_Ressource_SousActiv"
			." WHERE IdSousActiv='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerRessources ($v_iaIdResSousActiv)
	{
		$sValeursRequete = NULL;
		
		foreach ($v_iaIdResSousActiv as $iIdResSousActiv)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."'{$iIdResSousActiv}'";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM SousActiv_Ressource_SousActiv"
				." WHERE IdSousActiv='".$this->retId()."'"
				." AND IdResSousActiv IN ({$sValeursRequete})"
				." LIMIT ".count($v_iaIdResSousActiv);
			$this->oBdd->executerRequete($sRequeteSql);
			
			return TRUE;
		}
		
		return FALSE;
	}
	// }}}
	
	function verrouillerTables ($v_bVerrouiller=TRUE)
	{
		if ($v_bVerrouiller)
			$sRequeteSql = "LOCK TABLES"
				." SousActiv WRITE"
				.", SousActiv_SousActiv WRITE";
		else
			$sRequeteSql = "UNLOCK TABLES";
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
}

?>
