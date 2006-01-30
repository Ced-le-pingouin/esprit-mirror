<?php

/*
** Fichier ................: typestatutpers.tbl.php
** Description ............: 
** Date de cr�ation .......: 22/02/2005
** Derni�re modification ..: 22/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CTypeStatutPers
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iId;
	
	var $aoTypesStatutsPers;
	
	function CTypeStatutPers (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (is_object($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdStatut;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM TypeStatutPers"
				." WHERE IdStatut='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	// {{{ M�thodes de retour
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retNomStatut () { return $this->oEnregBdd->TxtStatut; }
	function retNomStatutMasculin () { return $this->oEnregBdd->NomMasculinStatut; }
	function retNomStatutFeminin () { return $this->oEnregBdd->NomFemininStatut; }
	// }}}
	
	function initTypesStatutsPers ()
	{
		$iIdxTypeStatutPers = 0;
		$this->aoTypesStatutsPers = array();
		
		$sRequeteSql = "SELECT * FROM TypeStatutPers"
			." ORDER BY IdStatut ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoTypesStatutsPers[$iIdxTypeStatutPers] = new CTypeStatutPers($this->oBdd);
			$this->aoTypesStatutsPers[$iIdxTypeStatutPers]->init($oEnreg);
			$iIdxTypeStatutPers++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxTypeStatutPers;
	}
}

?>
