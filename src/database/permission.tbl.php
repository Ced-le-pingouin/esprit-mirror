<?php

/*
** Fichier ................: permission.tbl.php
** Description ............: 
** Date de cr�ation .......: 20/03/2002
** Derni�re modification ..: 09/06/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CPermission
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $aoPermissions;
	var $aiPermisStatut;
	
	function CPermission (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if ($v_iId > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdPermission;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Permission"
				." WHERE IdPermission='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function retId () { return $this->iId; }
	function retNom () { return $this->oEnregBdd->NomPermis; }
	function retDescr () { return $this->oEnregBdd->DescrPermis; }
	
	function initPermissions ($v_sFiltre=NULL)
	{
		$iIdxPermis = 0;
		$this->aoPermissions = array();
		
		$sRequeteSql = "SELECT * FROM Permission"
			.(empty($v_sFiltre) ? NULL : " WHERE NomPermis LIKE '%{$v_sFiltre}%'")
			." ORDER BY IdPermission ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoPermissions[$iIdxPermis] = new CPermission($this->oBdd);
			$this->aoPermissions[$iIdxPermis]->init($oEnreg);
			$iIdxPermis++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxPermis;
	}
	
	function initPermissionsStatut ($v_iIdStatut)
	{
		$this->aiPermisStatut = array();
		
		$sRequeteSql = "SELECT Permission.NomPermis"
			.", Statut_Permission.IdPermission"
			." FROM Statut_Permission"
			." LEFT JOIN Permission USING (IdPermission)"
			." WHERE Statut_Permission.IdStatut='{$v_iIdStatut}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$this->aiPermisStatut[$oEnreg->NomPermis] = $oEnreg->IdPermission;
		
		$this->oBdd->libererResult($hResult);
		
		return count($this->aiPermisStatut);
	}
	
	function verifPermission ($v_sPermission) { return (is_array($this->aiPermisStatut) && isset($this->aiPermisStatut[$v_sPermission])); }
}

?>
