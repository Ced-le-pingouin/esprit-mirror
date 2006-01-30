<?php

/*
** Fichier ................: collecticiel.tbl.php
** Description ............:
** Date de création .......: 12/06/2004
** Dernière modification ..: 14/06/2004
**
*/

class CCollecticiel
{
	var $iId;
	
	var $oBdd;
	
	var $aoResSousActiv;
	
	function CCollecticiel (&$v_oBdd,$v_iIdSousActiv)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdSousActiv;
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function initCollecticiels ()
	{
	}
	
	/**
	 * Rechercher toutes les ressources de ce collecticiel.
	 *
	 */
	function initRessources ()
	{
		$iIdxRes = 0;
		$this->aoResSousActiv = array();
		
		$sRequeteSql = "SELECT * FROM Ressource_SousActiv"
			." LEFT JOIN Ressource USING (IdRes)"
			." WHERE IdSousActiv='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoResSousActiv[$iIdxRes] = new CRessourceSousActiv($this->oBdd);
			$this->aoResSousActiv[$iIdxRes]->init($oEnregBdd);
			$iIdxRes++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxRes;
	}
	
	function effacer ()
	{
		$iNbRessources = $this->initRessources();
		$this->verrouillerTables();
		$this->effacerCollecticielsLierGaleries();
		if ($iNbRessources > 0)
		{
			$this->effacerEvaluations();
			$this->effacerVotes();
			$this->effacerRessources();
		}
		$this->optimiserTables();
		$this->deverrouillerTables();
	}
	
	function effacerEvaluations ()
	{
		if (!is_array($this->aoResSousActiv) ||
			($iNbRessources = count($this->aoResSousActiv)) < 1)
			return;
		
		$sValeursRequete = NULL;
		foreach ($this->aoResSousActiv as $oResSousActiv)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."'".$oResSousActiv->retId()."'";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Ressource_SousActiv_Evaluation"
				." WHERE IdResSousActiv IN ({$sValeursRequete})"
				." LIMIT {$iNbRessources}";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerVotes ()
	{
		if (!is_array($this->aoResSousActiv) ||
			($iNbRessources = count($this->aoResSousActiv)) < 1)
			return;
		
		$sValeursRequete = NULL;
		foreach ($this->aoResSousActiv as $oResSousActiv)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."'".$oResSousActiv->retId()."'";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Ressource_SousActiv_Vote"
				." WHERE IdResSousActiv IN ({$sValeursRequete})"
				." LIMIT {$iNbRessources}";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	/**
	 * Effacer les collecticiels qui ont été liés avec un ou plusieurs
	 * galeries.
	 *
	 */
	function effacerCollecticielsLierGaleries ()
	{
		// Effacer les liens entre le collecticiel avec un/plusieurs galeries
		$sRequeteSql = "DELETE FROM SousActiv_SousActiv"
			." WHERE IdSousActivRef='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (!is_array($this->aoResSousActiv) ||
			($iNbRessources = count($this->aoResSousActiv)) < 1)
			return;
		
		$sValeursRequete = NULL;
		foreach ($this->aoResSousActiv as $oResSousActiv)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."'".$oResSousActiv->retId()."'";
		
		if (isset($sValeursRequete))
		{
			// Effacer les documents sélectionnés dans les collecticiels des 
			// galeries
			$sRequeteSql = "DELETE FROM SousActiv_Ressource_SousActiv"
				." WHERE IdResSousActiv IN ({$sValeursRequete})";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerRessources ()
	{
		if (!is_array($this->aoResSousActiv) ||
			($iNbRessources = count($this->aoResSousActiv)) < 1)
			return;
		
		// Effacer les ressources de la table
		$sValeursRequete = NULL;
		foreach ($this->aoResSousActiv as $oResSousActiv)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."'".$oResSousActiv->retIdRes()."'";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Ressource"
				." WHERE IdRes IN ({$sValeursRequete})"
				." LIMIT {$iNbRessources}";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "DELETE FROM Ressource_SousActiv"
			." WHERE IdSousActiv='".$this->retId()."'"
			." LIMIT {$iNbRessources}";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->aoResSousActiv = NULL;
	}
	
	function verrouillerTables ()
	{
		$sRequeteSql = "LOCK TABLES"
			." Ressource_SousActiv WRITE"
			.", Ressource WRITE"
			.", Ressource_SousActiv_Evaluation WRITE"
			.", Ressource_SousActiv_Vote WRITE"
			.", SousActiv_SousActiv WRITE"
			.", SousActiv_Ressource_SousActiv WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function optimiserTables ()
	{
		if (rand(1,10) != 10)
			return;
		
		$sRequeteSql = "OPTIMIZE TABLE"
			." Ressource_SousActiv"
			.", Ressource"
			.", Ressource_SousActiv_Evaluation"
			.", Ressource_SousActiv_Vote"
			.", SousActiv_SousActiv"
			.", SousActiv_Ressource_SousActiv";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function deverrouillerTables ()
	{
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
}

?>