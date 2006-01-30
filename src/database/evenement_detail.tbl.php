<?php

/*
** Fichier .................: evenement_detail.tbl.php
** Description .............:
** Date de création ........: 01/03/2002
** Dernière modification ...: 19/12/2005
** Auteurs .................: Filippo PORCO
** Emails ..................: ute@umh.ac.be
**
*/

class CEvenement_Detail
{
	var $oBdd;
	var $iId;
	
	var $iIdForm;
	
	var $sErreur;
	
	function CEvenement_Detail ($v_oBdd,$v_iId,$v_iIdForm=NULL)
	{
		$this->oBdd = &$v_oBdd;
		
		$this->sErreur = NULL;
		$this->iId = $v_iId;
		$this->iIdForm = $v_iIdForm;
	}
	
	function optimiserTable () { $this->oBdd->executerRequete("OPTIMIZE TABLE Evenement_Detail"); }
	
	function effacerParFormation ($v_bOptimiserTable=TRUE)
	{
		$sRequeteSql = "DELETE FROM Evenement_Detail"
			." WHERE IdForm='{$this->iIdForm}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		if ($v_bOptimiserTable)
			$this->optimiserTable();
	}
	
	function retIdsEvensParFormation ()
	{
		$aiIdsEvens = array();
		
		$sRequeteSql = "SELECT IdEven FROM Evenement_Detail"
			." WHERE IdForm='{$this->iIdForm}'"
				." AND MomentEven IS NOT NULL"
				." AND SortiMomentEven IS NOT NULL"
			." GROUP BY IdEven";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$aiIdsEvens[] = $oEnreg->IdEven;
		
		$this->oBdd->libererResult($hResult);
		
		return $aiIdsEvens;
	}
	
	function dejaEntrerFormation ()
	{
		$sRequeteSql = "SELECT Evenement_Detail.IdEven"
			." FROM Evenement_Detail"
			." WHERE Evenement_Detail.IdEven='{$this->iId}'"
				." AND Evenement_Detail.IdForm='{$this->iIdForm}'"
				." AND Evenement_Detail.SortiMomentEven IS NULL";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bDejaEntrerFormation = ($this->oBdd->retEnregSuiv($hResult) != NULL);
		$this->oBdd->libererResult($hResult);
		
		return $bDejaEntrerFormation;
	}
	
	function entrerFormation ($v_iIdForm=NULL)
	{
		$this->sErreur = NULL;
		
		if ($v_iIdForm > 0)
			$this->iIdForm = $v_iIdForm;
		
		if ($this->iId < 1 || $this->iIdForm < 1)
		{
			$this->sErreur = "CEvenement_Detail.entrerFormation: IdEven < 1 OR IdForm < 1";
			return FALSE;
		}
		
		if ($this->dejaEntrerFormation())
			return TRUE;
		
		$sRequeteSql = "INSERT INTO Evenement_Detail"
			." (IdEven,MomentEven,SortiMomentEven,IdForm)"
			." VALUES"
				." ('{$this->iId}',NOW(),NULL,'{$this->iIdForm}')";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function sortirFormation ($v_iIdForm=NULL)
	{
		$this->sErreur = NULL;
		
		if ($v_iIdForm > 0)
			$this->iIdForm = $v_iIdForm;
		
		if ($this->iId < 1 || $this->iIdForm < 1)
		{
			$this->sErreur = "CEvenement_Detail.sortirFormation: IdEven < 1 OR IdForm < 1";
			return FALSE;
		}
		
		$sRequeteSql = "UPDATE Evenement_Detail"
			." SET SortiMomentEven=NOW()"
			." WHERE IdEven='{$this->iId}'"
				." AND IdForm='{$this->iIdForm}'"
				." AND SortiMomentEven IS NULL";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function retErreur () { return $this->sErreur; }
}

?>
