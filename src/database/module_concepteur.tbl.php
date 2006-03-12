<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: module_concepteur.tbl.php
** Description ............: 
** Date de création .......: 23/09/2002
** Dernière modification ..: 07/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

class CFormation_Concepteur
{
	var $oBdd;
	var $iIdForm;
	var $iIdPers;
	
	var $aoConcepteurs;
	
	function CFormation_Concepteur (&$v_oBdd,$v_iIdForm=0,$v_iIdPers=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdForm = $v_iIdForm;
		$this->iIdPers = $v_iIdPers;
	}
	
	function ajouterConcepteurs ($v_aiIdPers)
	{
		if ($this->iIdForm < 1)
			return;
		
		settype($v_aiIdPers,"array");
		
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				." ('{$this->iIdForm}','{$iIdPers}')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "REPLACE INTO Formation_Concepteur"
				." (IdForm,IdPers) VALUES {$sValeursRequete}";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerConcepteur ()
	{
		if ($this->iIdForm < 1 || $this->iIdPers < 1)
			return;
		
		$oModuleConcepteur = new CModule_Concepteur($this->oBdd,0,$this->iIdPers);
		
		$oModuleConcepteur->initModules(FALSE,$this->iIdForm);
		
		$sValeursRequete = NULL;
		
		foreach ($oModuleConcepteur->aoModules as $oModule)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				."'".$oModule->retId()."'";
		
		// Bloquer les tables
		$sRequeteSql = "LOCK TABLES"
			." Formation_Concepteur WRITE"
			.", Module_Concepteur WRITE";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Module_Concepteur"
				." WHERE IdPers='{$this->iIdPers}'"
				." AND IdMod IN ($sValeursRequete)";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "DELETE FROM Formation_Concepteur"
			." WHERE IdForm=".$this->iIdForm
			." AND IdPers=".$this->iIdPers;
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	function initConcepteurs ()
	{
		$iIdxConcepteurs = 0;
		
		$this->aoConcepteurs = array();
		
		if ($this->iIdForm < 1)
			return $iIdxConcepteurs;
		
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." LEFT JOIN Formation_Concepteur USING(IdPers)"
			." WHERE Formation_Concepteur.IdForm='{$this->iIdForm}'"
			." ORDER BY Personne.Nom ASC, Personne.Prenom ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoConcepteurs[$iIdxConcepteurs] = new CPersonne($this->oBdd);
			$this->aoConcepteurs[$iIdxConcepteurs]->init($oEnreg);
			$iIdxConcepteurs++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxConcepteurs;
	}
}

class CModule_Concepteur
{
	var $oBdd;
	
	var $iIdMod;
	var $iIdPers;
	
	var $aoModules;
	var $aoConcepteurs;
	
	function CModule_Concepteur (&$v_oBdd,$v_iIdMod=0,$v_iIdPers=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdMod = $v_iIdMod;
		$this->iIdPers = $v_iIdPers;
	}
	
	function initConcepteurs ($v_iIdForm=0)
	{
		$idx = 0;
		
		$this->aoConcepteurs = array();
		
		$sRequeteSql = "SELECT p.* FROM Module_Concepteur AS mc"
			." LEFT JOIN Personne AS p USING(IdPers)";
		
		if ($v_iIdForm > 0)
			$sRequeteSql .= " LEFT JOIN Module AS m ON mc.IdMod=m.IdMod"
				." WHERE m.IdForm={$v_iIdForm}";
		else
			$sRequeteSql .= ($this->iIdMod > 0 ? " WHERE mc.IdMod={$this->iIdMod}" : NULL);
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoConcepteurs[$idx] = new CPersonne($this->oBdd);
			$this->aoConcepteurs[$idx]->init($oEnreg);
			$idx++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $idx;
	}
	
	function ajouter ($v_aiIdPers)
	{
		settype($v_aiIdPers,"array");
		
		if (count($v_aiIdPers) < 1 || $this->iIdMod < 1)
			return;
		
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				." ('{$iIdPers}','{$this->iIdMod}')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "REPLACE INTO Module_Concepteur"
				." (IdMod,IdPers)"
				." VALUES {$sValeursRequete}";
			
			echo "$sRequeteSql";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function Effacer ()
	{
		$this->effacerConcepteurs();
	}
	
	function effacerConcepteurs ()
	{
		if ($this->iIdMod > 0)
		{
			$sRequeteSql = "DELETE FROM Module_Concepteur"
				." WHERE IdMod='{$this->iIdMod}'";
			
			$this->oBdd->executerRequete ($sRequeteSql);
		}
		
		$this->iIdMod = NULL;
	}
	
	function effacerConcepteur ($v_iIdPers=0)
	{
		if ($v_iIdPers < 1)
			$v_iIdPers = $this->iIdPers;
		
		if ($v_iIdPers < 1 || $this->iIdMod < 1)
			return;
		
		$sRequeteSql = "DELETE FROM Module_Concepteur"
			." WHERE IdMod='{$this->iIdMod}'"
			." AND IdPers='{$v_iIdPers}'";
		
		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function initModules ($v_bRechTousCours=FALSE,$v_iIdForm=0)
	{
		$sRequeteSql = "SELECT Module_Concepteur.IdPers AS MC_IdPers"
			.", Module.*"
			." FROM Module"
			." LEFT JOIN Module_Concepteur ON Module.IdMod=Module_Concepteur.IdMod"
			.($v_bRechTousCours ? " AND Module_Concepteur.IdPers='{$this->iIdPers}'" : NULL)
			.($v_iIdForm > 0 ? " WHERE Module.IdForm='{$v_iIdForm}'" : NULL)
			." ORDER BY Module.OrdreMod ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$iIndexModules = 0;
		$this->aoModules = array();
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoModules[$iIndexModules] = new CModule($this->oBdd);
			$this->aoModules[$iIndexModules]->init($oEnreg);
			if ($v_bRechTousCours)
				$this->aoModules[$iIndexModules]->estSelectionne = ($oEnreg->MC_IdPers > 0 && $oEnreg->MC_IdPers == $this->iIdPers);
			$iIndexModules++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexModules;
	}
	
	function ajouterModules ($v_amIdMod)
	{
		if (is_array($v_amIdMod))
			$aiIdMod = $v_amIdMod;
		else
			$aiIdMod = array($v_amIdMod);
		
		foreach ($aiIdMod as $iIdMod)
		{
			$sRequeteSql = "REPLACE INTO Module_Concepteur SET"
				." IdMod='{$iIdMod}'"
				.",IdPers='{$this->iIdPers}'";
				
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerModules ($v_iIdForm)
	{
		if (($iNbrModules = $this->initModules(TRUE,$v_iIdForm)) > 0)
		{
			$sValeurRequete = NULL;
			
			for ($i=0; $i<$iNbrModules; $i++)
				$sValeurRequete .= (isset($sValeurRequete) ? "," : NULL)
					."'".$this->aoModules[$i]->retId()."'";
			
			if (isset($sValeurRequete))
			{
				$sRequeteSql = "DELETE FROM Module_Concepteur"
					." WHERE IdPers='{$this->iIdPers}'"
					." AND IdMod IN ({$sValeurRequete})";
				
				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
		
		return $iNbrModules;
	}
}

?>
