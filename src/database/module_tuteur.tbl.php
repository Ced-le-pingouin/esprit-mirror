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
** Fichier ................: module_tuteur.tbl.php
** Description ............:
** Date de création .......: 18-10-2002
** Dernière modification ..: 24-10-2003
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CFormation_Tuteur
{
	var $oBdd;
	var $iIdForm;
	var $iIdPers;
	
	var $aoTuteurs;
	
	function CFormation_Tuteur (&$v_oBdd,$v_iIdForm=0,$v_iIdPers=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdForm = $v_iIdForm;
		$this->iIdPers = $v_iIdPers;
	}
	
	function ajouterTuteurs ($v_aiIdPers)
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
			$sRequeteSql = "REPLACE INTO Formation_Tuteur"
				." (IdForm,IdPers)"
				." VALUES {$sValeursRequete}";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerTuteur ()
	{
		if ($this->iIdForm < 1 || $this->iIdPers < 1)
			return;
		
		$oModuleTuteur = new CModule_Tuteur($this->oBdd,0,$this->iIdPers);
		
		$oModuleTuteur->initModules(FALSE,$this->iIdForm);
		
		$sValeursRequete = NULL;
		
		foreach ($oModuleTuteur->aoModules as $oModule)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				."'".$oModule->retId()."'";
		
		$sRequeteSql = "LOCK TABLES"
			." Formation_Tuteur WRITE"
			.", Module_Tuteur WRITE";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Module_Tuteur"
				." WHERE IdPers='{$this->iIdPers}'"
				." AND IdMod IN ($sValeursRequete)";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "DELETE FROM Formation_Tuteur"
			." WHERE IdForm='{$this->iIdForm}'"
			." AND IdPers='{$this->iIdPers}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	function initTuteurs ($v_sModeTri="ASC")
	{
		$iIdxTuteurs = 0;
		
		$this->aoTuteurs = array();
		
		$sRequeteSql = "SELECT Personne.* FROM Formation_Tuteur"
			." LEFT JOIN Personne USING(IdPers)"
			." WHERE Formation_Tuteur.IdForm=".$this->iIdForm
			." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoTuteurs[$iIdxTuteurs] = new CPersonne($this->oBdd);
			$this->aoTuteurs[$iIdxTuteurs]->init($oEnreg);
			$iIdxTuteurs++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxTuteurs;
	}
}

class CModule_Tuteur
{
	var $oBdd;
	var $iIdMod;
	var $iIdPers;
	var $aoPersonnes;
	
	function CModule_Tuteur (&$v_oBdd,$v_iIdMod=0,$v_iIdPers=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdMod = $v_iIdMod;
		$this->iIdPers = $v_iIdPers;
	}
	
	function initTuteurs ()
	{
		$idx = 0;
		
		$this->aoPersonnes = array();
		
		if ($this->iIdMod > 0)
		{
			$sRequeteSql = "SELECT p.* FROM Module_Tuteur AS mt"
				." LEFT JOIN Personne AS p USING (IdPers)"
				." AND mt.IdMod={$this->iIdMod}";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoPersonnes[$idx] = new CPersonne($this->oBdd);
				$this->aoPersonnes[$idx]->init($oEnreg);
				$idx++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $idx;
	}
	
	function ajouter ($v_amIdPers)
	{
		if (count($v_amIdPers) < 1)
			return;
		
		if (is_array($v_amIdPers))
			$aiIdPers = $v_amIdPers;
		else
			$aiIdPers = array($v_amIdPers);
		
		foreach ($aiIdPers as $iIdPers)
		{
			$sRequeteSql = "REPLACE INTO Module_Tuteur SET"
				." IdMod={$this->iIdMod}"
				." , IdPers={$iIdPers}";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacer ()
	{
		$sRequeteSql = "DELETE FROM Module_Tuteur WHERE IdMod={$this->iIdMod}";
	}
	
	function ajouterTuteur ($v_iIdPers=0,$v_bInscrireDsTousCours=FALSE)
	{
		$iIdPers = ($v_iIdPers > 0 ? $v_iIdPers : $this->iIdPers);
		
		if ($iIdPers > 0)
		{
			$iNbrModules = ($v_bInscrireDsTousCours ? count($this->aoModules) : 1);
			
			for ($i=0; $i<$iNbrModules; $i++)
			{
				if ($this->aoModules[$i]->retId() < 1)
					continue;
				
				$sRequeteSql = "REPLACE INTO Module_Tuteur SET"
					." IdMod=".$this->aoModules[$i]->retId()
					." , IdPers={$iIdPers}";
				
				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
	}
	
	function effacerTuteur ($v_iIdPers=0)
	{
		$iIdPers = ($v_iIdPers > 0 ? $v_iIdPers : $this->iIdPers);
		
		if ($iIdPers > 0)
		{
			$sRequeteSql = "DELETE FROM Module_Tuteur WHERE (";
			
			for ($i=0; $i<count($this->aoModules); $i++)
				$sRequeteSql .= ($i > 0 ? " OR" : NULL)." IdMod=".$this->aoModules[$i]->retId();
			
			$sRequeteSql .= ") AND IdPers='{$iIdPers}'";
			
			$this->oBdd->executerRequete ($sRequeteSql);
			
			$this->aoModules = NULL;
		}
	}
	
	function initModules ($v_bRechTousCours=FALSE,$v_iIdForm=0)
	{
		$iIndexModules = 0;
		
		$this->aoModules = array();
		
		$sRequeteSql = "SELECT Module.*, Module_Tuteur.IdPers FROM Module"
			." LEFT JOIN Module_Tuteur ON Module.IdMod=Module_Tuteur.IdMod"
			.($v_bRechTousCours ? " AND Module_Tuteur.IdPers='{$this->iIdPers}'" : NULL)
			.($v_iIdForm > 0 ? " WHERE Module.IdForm='{$v_iIdForm}'" : NULL)
			." ORDER BY Module.OrdreMod ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoModules[$iIndexModules] = new CModule($this->oBdd);
			
			$this->aoModules[$iIndexModules]->init($oEnreg);
			
			if ($v_bRechTousCours)
				$this->aoModules[$iIndexModules]->estSelectionne = ($oEnreg->IdPers == $this->iIdPers);
			
			$iIndexModules++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexModules;
	}

	function ajouterModules ($v_aiIdMod)
	{
		settype($v_aiIdMod,'array');
		
		$sValeursRequete = NULL;

		foreach ($v_aiIdMod as $iIdMod)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				." ({$iIdMod},{$this->iIdPers})";

		if (isset($sValeursRequete))
		{
			$sRequeteSql = "REPLACE INTO Module_Tuteur"
				." (IdMod,IdPers) VALUES {$sValeursRequete}";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerModules ($v_iIdForm)
	{
		if (($iNbrModules = $this->initModules(TRUE,$v_iIdForm)) > 0)
		{
			$sRequeteSql = "DELETE FROM Module_Tuteur WHERE (";
			
			for ($i=0; $i<$iNbrModules; $i++)
				$sRequeteSql .= ($i < 1 ? NULL: " OR")." IdMod=".$this->aoModules[$i]->retId();
			
			$sRequeteSql .= ") AND IdPers=".$this->iIdPers;
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		return $iNbrModules;
	}
}

?>
