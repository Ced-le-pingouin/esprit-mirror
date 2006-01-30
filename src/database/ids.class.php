<?php

/*
** Fichier ................: ids.class.php
** Description ............: 
** Date de création .......: 07/02/2003
** Dernière modification ..: 19/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CIds
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iTypeNiveau;
	var $iIdNiveau;
	
	function CIds (&$v_oBdd,$v_iTypeNiveau=NULL,$v_iIdNiveau=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iTypeNiveau = $v_iTypeNiveau;
		$this->iIdNiveau = $v_iIdNiveau;
		
		$this->init();
	}
	
	function init ()
	{
		$this->oEnregBdd = NULL;
		
		switch ($this->iTypeNiveau)
		{
			case TYPE_SOUS_ACTIVITE:
				$sRequeteSql = "SELECT Module.IdForm"
					.", Module.IdMod"
					.", Module_Rubrique.IdRubrique"
					.", Activ.IdActiv"
					.", SousActiv.IdSousActiv"
					." FROM SousActiv"
					." LEFT JOIN Activ USING (IdActiv)"
					." LEFT JOIN Module_Rubrique USING (IdRubrique)"
					." LEFT JOIN Module USING (IdMod)"
					." WHERE SousActiv.IdSousActiv='{$this->iIdNiveau}'"
					." LIMIT 1";
				break;
				
			case TYPE_ACTIVITE:
				$sRequeteSql = "SELECT Module.IdForm"
					.", Module.IdMod"
					.", Module_Rubrique.IdRubrique"
					.", Activ.IdActiv"
					." FROM Activ"
					." LEFT JOIN Module_Rubrique USING (IdRubrique)"
					." LEFT JOIN Module USING (IdMod)"
					." WHERE Activ.IdActiv='{$this->iIdNiveau}'"
					." LIMIT 1";
				break;
				
			case TYPE_RUBRIQUE:
				$sRequeteSql = "SELECT Module.IdForm"
					.", Module.IdMod"
					.", Module_Rubrique.IdRubrique"
					." FROM Module_Rubrique"
					." LEFT JOIN Module USING (IdMod)"
					." WHERE Module_Rubrique.IdRubrique='{$this->iIdNiveau}'"
					." LIMIT 1";
				break;
				
			case TYPE_MODULE:
				$sRequeteSql = "SELECT Module.IdForm"
					.", Module.IdMod"
					." FROM Module"
					." WHERE Module.IdMod='{$this->iIdNiveau}'"
					." LIMIT 1";
				break;
				
			case TYPE_FORMATION:
				$this->oEnregBdd->IdForm = $this->iIdNiveau;
				break;
				
			default:
				$sRequeteSql = NULL;
		}
		
		if (isset($sRequeteSql))
		{
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			if ($hResult !== FALSE)
			{
				$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
				$this->oBdd->libererResult($hResult);
			}
		}
	}
	
	function retIds () { return $this->oEnregBdd; }
	
	function retListeIds () { return $this->retTableIds(); }
	
	function retTableIds ()
	{
		return array(
			0
			, $this->retIdForm()		// TYPE_FORMATION
			, $this->retIdMod()			// TYPE_MODULE
			, $this->retIdRubrique()	// TYPE_RUBRIQUE
			, 0
			, $this->retIdActiv()		// TYPE_ACTIVITE
			, $this->retIdSousActiv()	// TYPE_SOUS_ACTIVITE
		);
	}
	
	/*:08/09/2004:function defId ($v_iTypeNiveau,$v_iIdNiveau)
	{
		$this->iTypeNiveau = $v_iTypeNiveau;
		$this->iIdNiveau = $v_iIdNiveau;
		$this->init();
	}*/
	
	function retIdForm () { return (isset($this->oEnregBdd->IdForm) ? $this->oEnregBdd->IdForm : 0); }
	function retIdMod () { return (isset($this->oEnregBdd->IdMod) ? $this->oEnregBdd->IdMod : 0); }
	function retIdRubrique () { return (isset($this->oEnregBdd->IdRubrique) ? $this->oEnregBdd->IdRubrique : 0); }
	function retIdActiv () { return (isset($this->oEnregBdd->IdActiv) ? $this->oEnregBdd->IdActiv : 0); }
	function retIdSousActiv () { return (isset($this->oEnregBdd->IdSousActiv) ? $this->oEnregBdd->IdSousActiv : 0); }
}

?>
